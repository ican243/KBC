<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 소식·공지 (관리자)
 */
class NoticeModel extends Model
{
    protected $table         = 'notices';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['title', 'content', 'is_published', 'published_at', 'admin_id'];

    public function listing(): array
    {
        return $this->orderBy('published_at', 'DESC')->orderBy('id', 'DESC')->findAll();
    }
}
