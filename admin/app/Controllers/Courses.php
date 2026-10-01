<?php

namespace App\Controllers;

use App\Libraries\AuditLog;
use App\Models\CourseModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Model;

/**
 * 교육과정 관리
 *
 * 삭제: 연결된 상담 기록이 0건일 때만 실제 삭제 (잘못 만든 과정·시험 데이터 정리용)
 *       1건 이상이면 삭제하지 않고, 관리자가 확인하면 비공개로만 바꾼다 (상담 이력 보존)
 * 상담 수는 삭제 요청 시점에 서버에서 다시 센다 (목록을 연 뒤 상담이 들어와도 안전)
 * DB 도 연결된 상담이 있는 과정의 삭제를 거부한다 (마이그레이션 RestrictCourseDeleteWithConsults)
 */
class Courses extends ContentController
{
    protected string $path     = 'courses';
    protected string $label    = '교육과정';
    protected bool $sortable   = true;

    /** 목록용 [과정 id => 연결된 상담 수] */
    private ?array $consultCounts = null;

    protected function model(): Model
    {
        return new CourseModel();
    }

    protected function rules(?int $id): array
    {
        $unique = 'is_unique[courses.slug' . ($id !== null ? ',id,' . $id : '') . ']';

        return [
            'title'             => ['label' => '과정명', 'rules' => 'required|max_length[100]'],
            'slug'              => ['label' => '주소용 이름', 'rules' => 'required|alpha_dash|max_length[50]|' . $unique],
            'summary'           => ['label' => '한 줄 소개', 'rules' => 'permit_empty|max_length[255]'],
            'target'            => ['label' => '대상', 'rules' => 'permit_empty|max_length[255]'],
            'duration'          => ['label' => '기간', 'rules' => 'permit_empty|max_length[50]'],
            'sessions_per_week' => ['label' => '주당 수업', 'rules' => 'permit_empty|max_length[50]'],
            'fields'            => ['label' => '교육 분야', 'rules' => 'permit_empty|max_length[2000]'],
            'assignments'       => ['label' => '실습 과제', 'rules' => 'permit_empty|max_length[2000]'],
            'outputs'           => ['label' => '수료 산출물', 'rules' => 'permit_empty|max_length[2000]'],
            'fee_text'          => ['label' => '교습비 안내', 'rules' => 'permit_empty|max_length[255]'],
            'sort_order'        => ['label' => '순서', 'rules' => 'permit_empty|integer'],
        ];
    }

    protected function toData(array $post, bool $isNew): array
    {
        return [
            'title'             => trim($post['title']),
            'slug'              => trim($post['slug']),
            'summary'           => self::nullable($post, 'summary'),
            'target'            => self::nullable($post, 'target'),
            'duration'          => self::nullable($post, 'duration'),
            'sessions_per_week' => self::nullable($post, 'sessions_per_week'),
            'fields'            => self::nullable($post, 'fields'),
            'assignments'       => self::nullable($post, 'assignments'),
            'outputs'           => self::nullable($post, 'outputs'),
            'fee_text'          => self::nullable($post, 'fee_text'),
            'is_confirmed'      => self::flag($post, 'is_confirmed'),
            'is_published'      => self::flag($post, 'is_published'),
            'sort_order'        => (int) ($post['sort_order'] ?? 0),
        ];
    }

    protected function columns(): array
    {
        return [
            '과정명' => static fn (array $r): string => esc($r['title']) . ' <small class="muted">' . esc($r['slug']) . '</small>',
            '기간'   => static fn (array $r): string => esc($r['duration'] ?? '-'),
            '확정'   => static fn (array $r): string => $r['is_confirmed'] ? '<span class="badge badge-done">확정</span>' : '<span class="badge badge-hold">확정 전</span>',
        ];
    }

    protected function defaults(): array
    {
        return ['is_published' => 0, 'is_confirmed' => 0, 'sort_order' => 0];
    }

    protected function deleteConfirm(array $row): array
    {
        $count = $this->consultCounts()[(int) $row['id']] ?? 0;

        if ($count === 0) {
            return ['message' => "'{$row['title']}' 과정을 삭제할까요?\n연결된 상담 기록이 없어 바로 삭제되며, 되돌릴 수 없습니다.",
                    'ok' => '삭제', 'tone' => 'danger', 'unpublish' => false, 'info' => false];
        }

        $note = "연결된 상담 {$count}건 · 기존 상담 이력은 그대로 보존됩니다.";

        if (! $row['is_published']) {
            return ['message' => "상담 기록이 연결되어 있어 삭제할 수 없습니다.\n이미 비공개 상태입니다.\n{$note}",
                    'ok' => '확인', 'tone' => 'primary', 'unpublish' => false, 'info' => true];
        }

        return ['message' => "상담 기록이 연결되어 있어 삭제할 수 없습니다. 비공개 처리하시겠습니까?\n{$note}",
                'ok' => '비공개 처리', 'tone' => 'primary', 'unpublish' => true, 'info' => false];
    }

    /**
     * 삭제 요청
     * - 상담 0건: 삭제
     * - 상담 1건 이상 + 비공개 처리 확인(unpublish=1): 비공개로 변경
     * - 상담 1건 이상 + 그 외: 아무것도 바꾸지 않고 안내
     */
    public function delete(int $id)
    {
        $row   = $this->findOr404($id);
        $count = $this->countConsults($id);

        if ($count === 0) {
            try {
                $deleted = (bool) $this->model()->delete($id);
            } catch (DatabaseException) {
                $deleted = false;   // 그 사이 상담이 들어와 DB 가 삭제를 거부한 경우
            }

            if ($deleted) {
                AuditLog::write('content_delete', "courses #{$id} {$row['slug']}");

                return redirect()->to(site_url($this->path))->with('message', "'{$row['title']}' 과정을 삭제했습니다.");
            }
            $count = max(1, $this->countConsults($id));
        }

        $back = redirect()->to(site_url($this->path));

        if ($this->request->getPost('unpublish') !== '1') {
            return $back->with('error', "상담 기록 {$count}건이 연결되어 있어 삭제할 수 없습니다. 필요하면 비공개로 바꿔 주세요.");
        }
        if (! $row['is_published']) {
            return $back->with('message', "상담 기록 {$count}건이 연결되어 있어 삭제하지 않았습니다. 이미 비공개 상태입니다.");
        }

        $this->model()->update($id, ['is_published' => 0]);
        AuditLog::write('content_toggle', "courses #{$id} → 비공개 (삭제 대신, 연결된 상담 {$count}건)");

        return $back->with('message', "상담 기록 {$count}건이 연결되어 있어 삭제하지 않고 비공개로 바꿨습니다. 기존 상담 이력은 그대로 보존됩니다.");
    }

    /**
     * 이 과정을 고른 상담 수 (삭제 처리한 상담도 포함 - 기록은 DB 에 남아 있으므로)
     */
    private function countConsults(int $id): int
    {
        return db_connect()->table('consult_inquiries')->where('course_id', $id)->countAllResults();
    }

    private function consultCounts(): array
    {
        if ($this->consultCounts === null) {
            $rows = db_connect()->table('consult_inquiries')->select('course_id, COUNT(*) AS n')
                ->where('course_id IS NOT NULL')->groupBy('course_id')->get()->getResultArray();
            $this->consultCounts = array_map('intval', array_column($rows, 'n', 'course_id'));
        }

        return $this->consultCounts;
    }
}
