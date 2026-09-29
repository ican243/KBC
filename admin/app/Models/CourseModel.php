<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 교육과정 (관리자)
 * 상담 신청이 과정을 참조하므로 삭제하지 않고 비공개로만 숨긴다.
 */
class CourseModel extends Model
{
    protected $table         = 'courses';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'slug', 'title', 'summary', 'target', 'duration', 'sessions_per_week',
        'fields', 'assignments', 'outputs', 'fee_text', 'is_confirmed', 'is_published', 'sort_order',
    ];

    /**
     * [id => 과정명]
     */
    public function options(): array
    {
        return array_column($this->select('id, title')->orderBy('sort_order')->findAll(), 'title', 'id');
    }

    public function listing(): array
    {
        return $this->orderBy('sort_order')->orderBy('id')->findAll();
    }
}
