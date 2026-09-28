<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 교육 상담 신청 - 공개 사이트에서는 등록(INSERT)만 한다.
 */
class ConsultInquiryModel extends Model
{
    protected $table         = 'consult_inquiries';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'receipt_no',
        'name',
        'phone',
        'course_id',
        'contact_time',
        'message',
        'agree_privacy_at',
        'agree_marketing',
        'agree_marketing_at',
        'source',
        'ip',
        'user_agent',
    ];
}
