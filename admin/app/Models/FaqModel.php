<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 자주 묻는 질문 (관리자)
 */
class FaqModel extends Model
{
    protected $table         = 'faqs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['category', 'question', 'answer', 'is_published', 'sort_order'];

    public function listing(): array
    {
        return $this->orderBy('sort_order')->orderBy('id')->findAll();
    }
}
