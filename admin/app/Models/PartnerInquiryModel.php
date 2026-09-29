<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 기관 협력 제안 (관리자)
 * 삭제는 deleted_at 표시만 한다(복구 가능).
 */
class PartnerInquiryModel extends Model
{
    protected $table          = 'partner_inquiries';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = ['status', 'assigned_admin_id'];

    public const TYPES = [
        'space'      => '교육장',
        'instructor' => '강사',
        'hiring'     => '채용',
    ];

    /**
     * @param array{status?: string, partner_type?: string, from?: string, to?: string, q?: string} $filters
     */
    public function filtered(array $filters): self
    {
        $this->select('partner_inquiries.*, admins.name AS admin_name')
            ->join('admins', 'admins.id = partner_inquiries.assigned_admin_id', 'left');

        if (($filters['status'] ?? '') !== '') {
            $this->where('partner_inquiries.status', $filters['status']);
        }
        if (($filters['partner_type'] ?? '') !== '') {
            $this->where('partner_inquiries.partner_type', $filters['partner_type']);
        }
        if (($filters['from'] ?? '') !== '') {
            $this->where('partner_inquiries.created_at >=', $filters['from'] . ' 00:00:00');
        }
        if (($filters['to'] ?? '') !== '') {
            $this->where('partner_inquiries.created_at <=', $filters['to'] . ' 23:59:59');
        }
        if (($filters['q'] ?? '') !== '') {
            $q      = $filters['q'];
            // 숫자·하이픈만 3자리 이상 입력했을 때만 연락처로도 찾는다 (예: 01012345678, 1234-5678)
            $digits = preg_match('/^[\d\-\s]+$/', $q) ? preg_replace('/\D/', '', $q) : '';
            $digits = strlen($digits) >= 3 ? $digits : '';

            $this->groupStart()
                ->like('partner_inquiries.org_name', $q)
                ->orLike('partner_inquiries.contact_name', $q)
                ->orLike('partner_inquiries.receipt_no', $q);
            if ($digits !== '') {
                // 하이픈 없이 입력해도 찾도록 저장값의 하이픈을 빼고 비교
                $this->orWhere("REPLACE(partner_inquiries.phone, '-', '') LIKE " . $this->db->escape('%' . $digits . '%'), null, false);
            }
            $this->groupEnd();
        }

        return $this->orderBy('partner_inquiries.created_at', 'DESC')->orderBy('partner_inquiries.id', 'DESC');
    }

    public function findDetail(int $id): ?array
    {
        return $this->select('partner_inquiries.*, admins.name AS admin_name')
            ->join('admins', 'admins.id = partner_inquiries.assigned_admin_id', 'left')
            ->find($id);
    }
}
