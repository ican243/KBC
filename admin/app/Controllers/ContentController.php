<?php

namespace App\Controllers;

use App\Libraries\AuditLog;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Model;

/**
 * 홈페이지 콘텐츠 관리 공통 (교육과정, 공지, 설명회, FAQ ...)
 * 목록 → 등록/수정 → 공개 전환 → 순서 저장 → 삭제
 * 메뉴별 컨트롤러는 입력 규칙과 목록 칸만 정한다.
 */
abstract class ContentController extends BaseController
{
    /** 주소 앞부분 (courses, notices ...) */
    protected string $path;

    /** 화면 제목 */
    protected string $label;

    /** 삭제 버튼 표시 여부 */
    protected bool $deletable = true;

    /** 목록에서 순서(sort_order) 입력 사용 */
    protected bool $sortable = false;

    abstract protected function model(): Model;

    /** 저장 규칙 (CI4 검증) */
    abstract protected function rules(?int $id): array;

    /** POST 값을 저장할 데이터로 변환 */
    abstract protected function toData(array $post, bool $isNew): array;

    /** 목록 칸 [제목 => fn(array $row): string (HTML)] */
    abstract protected function columns(): array;

    /** 새로 만들 때 기본값 */
    protected function defaults(): array
    {
        return ['is_published' => 0];
    }

    /**
     * 목록의 삭제 버튼을 눌렀을 때 확인 창 내용
     * message 안내 문구 / ok 확인 버튼 글자 / tone danger(빨강)·primary(남색)
     * unpublish true 면 확인 시 "삭제 대신 비공개" 로 요청 / info true 면 안내만 하고 아무것도 보내지 않음
     */
    protected function deleteConfirm(array $row): array
    {
        return ['message' => '삭제할까요? 되돌릴 수 없습니다.', 'ok' => '삭제', 'tone' => 'danger', 'unpublish' => false, 'info' => false];
    }

    public function index()
    {
        $rows = $this->model()->listing();

        return view('content/list', [
            'title'     => $this->label,
            'path'      => $this->path,
            'rows'      => $rows,
            'columns'   => $this->columns(),
            'deletable' => $this->deletable,
            'sortable'  => $this->sortable,
            'confirms'  => $this->deletable ? array_column(array_map(fn (array $r): array => ['id' => $r['id']] + $this->deleteConfirm($r), $rows), null, 'id') : [],
        ]);
    }

    public function create()
    {
        return $this->form(null, $this->defaults());
    }

    public function store()
    {
        $post = $this->request->getPost();

        if (! $this->validateData($post, $this->rules(null))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id = $this->model()->insert($this->toData($post, true));
        AuditLog::write('content_create', "{$this->path} #{$id}");

        return redirect()->to(site_url($this->path))->with('message', '등록했습니다.');
    }

    public function edit(int $id)
    {
        return $this->form($id, $this->findOr404($id));
    }

    public function update(int $id)
    {
        $this->findOr404($id);
        $post = $this->request->getPost();

        if (! $this->validateData($post, $this->rules($id))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model()->update($id, $this->toData($post, false));
        AuditLog::write('content_update', "{$this->path} #{$id}");

        return redirect()->to(site_url($this->path))->with('message', '저장했습니다.');
    }

    /**
     * 공개 ↔ 비공개
     */
    public function toggle(int $id)
    {
        $row = $this->findOr404($id);
        $new = $row['is_published'] ? 0 : 1;

        $this->model()->update($id, ['is_published' => $new]);
        AuditLog::write('content_toggle', "{$this->path} #{$id} → " . ($new ? '공개' : '비공개'));

        return redirect()->to(site_url($this->path))->with('message', $new ? '공개로 바꿨습니다.' : '비공개로 바꿨습니다.');
    }

    /**
     * 목록에서 입력한 순서 저장  order[id] = 숫자
     */
    public function sort()
    {
        if (! $this->sortable) {
            throw PageNotFoundException::forPageNotFound();
        }

        $model = $this->model();
        foreach ((array) $this->request->getPost('order') as $id => $value) {
            if (ctype_digit((string) $id) && is_numeric($value)) {
                $model->update((int) $id, ['sort_order' => (int) $value]);
            }
        }
        AuditLog::write('content_sort', $this->path);

        return redirect()->to(site_url($this->path))->with('message', '순서를 저장했습니다.');
    }

    public function delete(int $id)
    {
        if (! $this->deletable) {
            return redirect()->to(site_url($this->path))->with('error', '이 항목은 삭제할 수 없습니다. 비공개로 바꿔 주세요.');
        }

        $this->findOr404($id);
        $this->model()->delete($id);
        AuditLog::write('content_delete', "{$this->path} #{$id}");

        return redirect()->to(site_url($this->path))->with('message', '삭제했습니다.');
    }

    protected function form(?int $id, array $row)
    {
        return view($this->path . '/form', [
            'title' => $this->label . ($id === null ? ' 등록' : ' 수정'),
            'path'  => $this->path,
            'id'    => $id,
            'row'   => $row,
        ]);
    }

    protected function findOr404(int $id): array
    {
        return $this->model()->find($id) ?? throw PageNotFoundException::forPageNotFound();
    }

    // ---- 폼 값 변환 도우미 ----

    /** 체크박스 → 0/1 */
    protected static function flag(array $post, string $key): int
    {
        return ($post[$key] ?? '') === '1' ? 1 : 0;
    }

    /** 빈 문자열 → NULL */
    protected static function nullable(array $post, string $key): ?string
    {
        $value = trim((string) ($post[$key] ?? ''));

        return $value === '' ? null : $value;
    }

    /** datetime-local(2026-10-01T14:00) → 2026-10-01 14:00:00 */
    protected static function datetime(array $post, string $key): ?string
    {
        $value = trim((string) ($post[$key] ?? ''));

        return $value === '' ? null : date('Y-m-d H:i:s', strtotime($value));
    }
}
