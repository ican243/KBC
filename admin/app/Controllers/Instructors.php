<?php

namespace App\Controllers;

use App\Libraries\AuditLog;
use App\Libraries\ImageUpload;
use App\Models\InstructorModel;
use CodeIgniter\Model;
use RuntimeException;

/**
 * 강사진 관리 (사진 업로드 포함)
 * 목록·순서·공개 전환은 ContentController 공통 기능을 쓰고, 저장·삭제만 사진 처리를 더한다.
 */
class Instructors extends ContentController
{
    protected string $path   = 'instructors';
    protected string $label  = '강사진';
    protected bool $sortable = true;

    private const PHOTO_FOLDER = 'instructors';

    protected function model(): Model
    {
        return new InstructorModel();
    }

    protected function rules(?int $id): array
    {
        return [
            'field'       => ['label' => '분야', 'rules' => 'required|max_length[50]'],
            'criteria'    => ['label' => '섭외 기준', 'rules' => 'permit_empty|max_length[255]'],
            'description' => ['label' => '교육 내용', 'rules' => 'permit_empty|max_length[255]'],
            'name'        => ['label' => '실명', 'rules' => 'permit_empty|max_length[50]'],
            'profile'     => ['label' => '프로필', 'rules' => 'permit_empty|max_length[2000]'],
            'career'      => ['label' => '경력', 'rules' => 'permit_empty|max_length[2000]'],
            'sort_order'  => ['label' => '순서', 'rules' => 'permit_empty|integer'],
        ];
    }

    protected function toData(array $post, bool $isNew): array
    {
        return [
            'field'        => trim($post['field']),
            'criteria'     => self::nullable($post, 'criteria'),
            'description'  => self::nullable($post, 'description'),
            'name'         => self::nullable($post, 'name'),
            'profile'      => self::nullable($post, 'profile'),
            'career'       => self::nullable($post, 'career'),
            'is_published' => self::flag($post, 'is_published'),
            'sort_order'   => (int) ($post['sort_order'] ?? 0),
        ];
    }

    protected function columns(): array
    {
        return [
            '사진' => static fn (array $r): string => $r['photo_path']
                ? '<img class="thumb" src="' . esc(public_url($r['photo_path']), 'attr') . '" alt="">'
                : '<span class="muted">없음</span>',
            '분야' => static fn (array $r): string => esc($r['field']),
            '실명' => static fn (array $r): string => esc($r['name'] ?? '-'),
            '게시 동의' => static fn (array $r): string => $r['consent_at']
                ? '<span class="badge badge-done">동의</span>'
                : '<span class="badge badge-hold">미동의</span> <small class="muted">실명·사진 비공개</small>',
        ];
    }

    protected function defaults(): array
    {
        return ['is_published' => 1, 'sort_order' => 0];
    }

    public function store()
    {
        return $this->save(null);
    }

    public function update(int $id)
    {
        return $this->save($this->findOr404($id));
    }

    /**
     * 강사 삭제 시 사진 파일도 함께 삭제
     */
    public function delete(int $id)
    {
        $row = $this->findOr404($id);

        $this->model()->delete($id);
        ImageUpload::delete($row['photo_path']);
        AuditLog::write('content_delete', "instructors #{$id}");

        return redirect()->to(site_url($this->path))->with('message', '삭제했습니다.');
    }

    /**
     * 등록/수정 공통: 입력 검사 → 새 사진 저장 → DB 저장 → 옛 사진 삭제
     */
    private function save(?array $row)
    {
        $post = $this->request->getPost();

        if (! $this->validateData($post, $this->rules($row['id'] ?? null))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data     = $this->toData($post, $row === null);
        $oldPhoto = $row['photo_path'] ?? null;
        $newPhoto = null;

        // 게시 동의: 처음 체크하면 지금 시각, 이미 동의했으면 날짜 유지, 체크 해제하면 NULL
        $data['consent_at'] = self::flag($post, 'consent') === 1
            ? ($row['consent_at'] ?? date('Y-m-d H:i:s'))
            : null;

        $file = $this->request->getFile('photo');
        if ($file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            try {
                $newPhoto = ImageUpload::save($file, self::PHOTO_FOLDER);
            } catch (RuntimeException $e) {
                return redirect()->back()->withInput()->with('errors', ['photo' => $e->getMessage()]);
            }
            $data['photo_path'] = $newPhoto;
        } elseif (self::flag($post, 'remove_photo') === 1) {
            $data['photo_path'] = null;
        }

        try {
            if ($row === null) {
                $id = $this->model()->insert($data);
                AuditLog::write('content_create', "instructors #{$id}");
            } else {
                $this->model()->update($row['id'], $data);
                AuditLog::write('content_update', "instructors #{$row['id']}");
            }
        } catch (\Throwable $e) {
            ImageUpload::delete($newPhoto); // DB 저장 실패 시 방금 올린 사진 정리

            throw $e;
        }

        // 사진을 바꿨거나 지웠으면 옛 파일 삭제
        if (array_key_exists('photo_path', $data) && $oldPhoto !== $data['photo_path']) {
            ImageUpload::delete($oldPhoto);
        }

        return redirect()->to(site_url($this->path))->with('message', $row === null ? '등록했습니다.' : '저장했습니다.');
    }
}
