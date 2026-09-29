<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 문의 메모·상태 변경 이력 (상담/협력 공용)
 */
class InquiryMemoModel extends Model
{
    protected $table         = 'inquiry_memos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $updatedField  = '';
    protected $allowedFields = ['inquiry_type', 'inquiry_id', 'admin_id', 'memo', 'status_from', 'status_to'];

    public function history(string $type, int $inquiryId): array
    {
        return $this->select('inquiry_memos.*, admins.name AS admin_name')
            ->join('admins', 'admins.id = inquiry_memos.admin_id', 'left')
            ->where('inquiry_type', $type)
            ->where('inquiry_id', $inquiryId)
            ->orderBy('inquiry_memos.id', 'DESC')
            ->findAll();
    }
}
