<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table      = 'courses';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    /**
     * 홈페이지에 노출하는 과정 (정렬순)
     */
    public function getPublished(): array
    {
        return $this->where('is_published', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * 과정 상세 (공개 중인 과정만)
     */
    public function findPublishedBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->where('is_published', 1)->first();
    }
}
