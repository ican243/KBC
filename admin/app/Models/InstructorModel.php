<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 강사진 (관리자)
 * 실명·사진·경력은 consent_at(게시 동의 일시)이 있어야 공개 홈페이지에 표시된다.
 */
class InstructorModel extends Model
{
    protected $table         = 'instructors';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'field', 'criteria', 'description', 'name', 'profile', 'career',
        'photo_path', 'consent_at', 'is_published', 'sort_order',
    ];

    public function listing(): array
    {
        return $this->orderBy('sort_order')->orderBy('id')->findAll();
    }
}
