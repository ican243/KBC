<?php

namespace App\Controllers;

use App\Libraries\AuditLog;
use App\Libraries\CsvExport;
use App\Libraries\InquiryStatus;
use App\Models\AdminModel;
use App\Models\InquiryMemoModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Model;

/**
 * 상담 문의 / 협력 제안 관리 공통
 * 목록(필터·검색·페이지) → 상세 → 처리(상태·담당자·메모) → 삭제 표시, CSV 내보내기
 */
abstract class InquiryAdminController extends BaseController
{
    protected const PER_PAGE = 20;

    /** consult | partner (inquiry_memos.inquiry_type) */
    protected string $type;

    /** 주소 앞부분 (consults | partners) */
    protected string $path;

    /** 화면 제목 */
    protected string $label;

    abstract protected function model(): Model;

    /** 목록 필터값 (허용된 값만 남긴다) */
    abstract protected function filters(): array;

    /** 필터 화면에 필요한 선택지 */
    abstract protected function filterOptions(): array;

    /** CSV [제목 목록, 행 변환 함수] */
    abstract protected function csvColumns(): array;

    public function index()
    {
        $filters = $this->filters();
        $model   = $this->model();
        $rows    = $model->filtered($filters)->paginate(self::PER_PAGE);

        return view($this->path . '/index', [
            'title'   => $this->label,
            'rows'    => $rows,
            'pager'   => $model->pager,
            'total'   => $model->pager->getTotal(),
            'filters' => $filters,
            'options' => $this->filterOptions(),
            'path'    => $this->path,
        ]);
    }

    public function show(int $id)
    {
        $row = $this->model()->findDetail($id) ?? throw PageNotFoundException::forPageNotFound();

        AuditLog::write('view', "{$this->type} #{$id} {$row['receipt_no']}");

        return view($this->path . '/show', [
            'title'    => $this->label . ' ' . $row['receipt_no'],
            'row'      => $row,
            'history'  => (new InquiryMemoModel())->history($this->type, $id),
            'admins'   => (new AdminModel())->options(),
            'statuses' => InquiryStatus::LABELS,
            'path'     => $this->path,
        ]);
    }

    /**
     * 상태·담당자 변경, 메모 추가 → 이력 1건 기록
     */
    public function update(int $id)
    {
        $model = $this->model();
        $row   = $model->find($id) ?? throw PageNotFoundException::forPageNotFound();
        $back  = redirect()->to(site_url("{$this->path}/{$id}"));

        $admins = (new AdminModel())->options();
        $rules  = [
            'status'            => 'required|in_list[' . implode(',', InquiryStatus::keys()) . ']',
            'assigned_admin_id' => 'permit_empty|in_list[' . implode(',', array_keys($admins)) . ']',
            'memo'              => 'permit_empty|max_length[2000]',
        ];
        if (! $this->validate($rules)) {
            return $back->with('error', '입력값을 확인해 주세요. (메모는 2,000자 이내)');
        }

        $status   = (string) $this->request->getPost('status');
        $assignee = $this->request->getPost('assigned_admin_id');
        $assignee = $assignee === '' || $assignee === null ? null : (int) $assignee;
        $memo     = trim((string) $this->request->getPost('memo'));

        $statusChanged   = $status !== $row['status'];
        $assigneeChanged = $assignee !== ($row['assigned_admin_id'] === null ? null : (int) $row['assigned_admin_id']);

        if (! $statusChanged && ! $assigneeChanged && $memo === '') {
            return $back->with('error', '변경된 내용이 없습니다.');
        }

        $model->update($id, ['status' => $status, 'assigned_admin_id' => $assignee]);

        $notes = [];
        if ($assigneeChanged) {
            $notes[] = '[담당자] ' . ($assignee === null ? '지정 해제' : ($admins[$assignee] ?? $assignee));
        }
        if ($memo !== '') {
            $notes[] = $memo;
        }

        (new InquiryMemoModel())->insert([
            'inquiry_type' => $this->type,
            'inquiry_id'   => $id,
            'admin_id'     => session('admin_id'),
            'memo'         => $notes === [] ? null : implode("\n", $notes),
            'status_from'  => $statusChanged ? $row['status'] : null,
            'status_to'    => $statusChanged ? $status : null,
        ]);

        return $back->with('message', '저장했습니다.');
    }

    /**
     * 삭제 표시 (deleted_at). 목록에서 사라지고 DB 에는 남는다.
     */
    public function delete(int $id)
    {
        $model = $this->model();
        $row   = $model->find($id) ?? throw PageNotFoundException::forPageNotFound();

        $model->delete($id);
        (new InquiryMemoModel())->insert([
            'inquiry_type' => $this->type,
            'inquiry_id'   => $id,
            'admin_id'     => session('admin_id'),
            'memo'         => '[삭제 처리]',
        ]);
        AuditLog::write('delete', "{$this->type} #{$id} {$row['receipt_no']}");

        return redirect()->to(site_url($this->path))->with('message', $row['receipt_no'] . ' 문의를 삭제 처리했습니다.');
    }

    /**
     * 현재 필터 결과를 CSV 로 내려받기
     */
    public function export()
    {
        $filters = $this->filters();
        $rows    = $this->model()->filtered($filters)->findAll();

        [$headers, $mapper] = $this->csvColumns();

        AuditLog::write('export', $this->type . ' ' . count($rows) . '건 ' . http_build_query(array_filter($filters)));

        return CsvExport::download(
            $this->type . '_' . date('Ymd_His') . '.csv',
            $headers,
            array_map($mapper, $rows)
        );
    }

    /**
     * 공통 필터 (상태, 기간, 검색어)
     */
    protected function commonFilters(): array
    {
        $get    = fn (string $key): string => trim((string) $this->request->getGet($key));
        $date   = static fn (string $v): string => preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? $v : '';
        $status = $get('status');

        return [
            'status' => in_array($status, InquiryStatus::keys(), true) ? $status : '',
            'from'   => $date($get('from')),
            'to'     => $date($get('to')),
            'q'      => mb_substr($get('q'), 0, 50),
        ];
    }
}
