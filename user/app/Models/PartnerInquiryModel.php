<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 기관 협력 제안 - 공개 사이트에서는 등록(INSERT)만 한다.
 */
class PartnerInquiryModel extends Model
{
    protected $table         = 'partner_inquiries';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'receipt_no',
        'org_name',
        'contact_name',
        'phone',
        'partner_type',
        'message',
        'agree_privacy_at',
        'source',
        'ip',
        'user_agent',
    ];

    public const TYPES = [
        'space'      => '교육장',
        'instructor' => '강사',
        'hiring'     => '채용',
    ];
}
