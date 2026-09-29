<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 교육 상담 신청 (관리자)
 * 삭제는 deleted_at 표시만 한다(복구 가능).
 */
class ConsultInquiryModel extends Model
{
    protected $table          = 'consult_inquiries';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = ['status', 'assigned_admin_id'];

    /**
     * 목록/CSV 공통 조회 조건
     *
     * @param array{status?: string, course_id?: string, from?: string, to?: string, q?: string} $filters
     */
    public function filtered(array $filters): self
    {
        $this->select('consult_inquiries.*, courses.title AS course_title, admins.name AS admin_name')
            ->join('courses', 'courses.id = consult_inquiries.course_id', 'left')
            ->join('admins', 'admins.id = consult_inquiries.assigned_admin_id', 'left');

        if (($filters['status'] ?? '') !== '') {
            $this->where('consult_inquiries.status', $filters['status']);
        }
        if (($filters['course_id'] ?? '') !== '') {
            $filters['course_id'] === 'none'
                ? $this->where('consult_inquiries.course_id', null)
                : $this->where('consult_inquiries.course_id', (int) $filters['course_id']);
        }
        if (isset($filters['source_values'])) {
            // 같은 이름으로 묶인 유입 값들 (NULL 포함 가능)
            $values = array_values(array_filter($filters['source_values'], static fn ($v) => $v !== null));
            $hasNull = in_array(null, $filters['source_values'], true);
            $this->groupStart();
            if ($values !== []) {
                $this->whereIn('consult_inquiries.source', $values);
            }
            if ($hasNull) {
                $values !== [] ? $this->orWhere('consult_inquiries.source', null) : $this->where('consult_inquiries.source', null);
            }
            $this->groupEnd();
        }
        if (($filters['from'] ?? '') !== '') {
            $this->where('consult_inquiries.created_at >=', $filters['from'] . ' 00:00:00');
        }
        if (($filters['to'] ?? '') !== '') {
            $this->where('consult_inquiries.created_at <=', $filters['to'] . ' 23:59:59');
        }
        if (($filters['q'] ?? '') !== '') {
            $q      = $filters['q'];
            // 숫자·하이픈만 3자리 이상 입력했을 때만 연락처로도 찾는다 (예: 01012345678, 1234-5678)
            $digits = preg_match('/^[\d\-\s]+$/', $q) ? preg_replace('/\D/', '', $q) : '';
            $digits = strlen($digits) >= 3 ? $digits : '';

            $this->groupStart()
                ->like('consult_inquiries.name', $q)
                ->orLike('consult_inquiries.receipt_no', $q);
            if ($digits !== '') {
                // 하이픈 없이 입력해도 찾도록 저장값의 하이픈을 빼고 비교
                $this->orWhere("REPLACE(consult_inquiries.phone, '-', '') LIKE " . $this->db->escape('%' . $digits . '%'), null, false);
            }
            $this->groupEnd();
        }

        return $this->orderBy('consult_inquiries.created_at', 'DESC')->orderBy('consult_inquiries.id', 'DESC');
    }

    /**
     * 목록 필터용: 지금까지 들어온 서로 다른 유입 값 (삭제 처리된 문의 제외)
     */
    public function distinctSources(): array
    {
        return array_column($this->db->table('consult_inquiries')->select('source')->distinct()->where('deleted_at', null)->get()->getResultArray(), 'source');
    }

    public function findDetail(int $id): ?array
    {
        return $this->select('consult_inquiries.*, courses.title AS course_title, admins.name AS admin_name')
            ->join('courses', 'courses.id = consult_inquiries.course_id', 'left')
            ->join('admins', 'admins.id = consult_inquiries.assigned_admin_id', 'left')
            ->find($id);
    }
}
