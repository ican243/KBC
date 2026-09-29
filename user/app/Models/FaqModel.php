<?php

namespace App\Models;

use CodeIgniter\Model;

class FaqModel extends Model
{
    protected $table      = 'faqs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    /**
     * 공개 FAQ 를 분류별로 묶어서 [분류 => [질문...]]
     */
    public function getPublishedGrouped(): array
    {
        $groups = [];
        $rows   = $this->where('is_published', 1)->orderBy('sort_order')->orderBy('id')->findAll();

        foreach ($rows as $row) {
            $groups[$row['category'] ?? '기타'][] = $row;
        }

        return $groups;
    }
}
