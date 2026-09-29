<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 교육과정 (관리자) - 현재는 문의 필터용 목록만 사용
 */
class CourseModel extends Model
{
    protected $table      = 'courses';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    /**
     * [id => 과정명]
     */
    public function options(): array
    {
        return array_column($this->select('id, title')->orderBy('sort_order')->findAll(), 'title', 'id');
    }
}
