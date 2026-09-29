<?php

namespace App\Models;

use CodeIgniter\Model;

class NoticeModel extends Model
{
    protected $table      = 'notices';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    /**
     * 게시일이 지난 공개 소식 (최신순)
     */
    public function getLatest(int $limit = 3): array
    {
        return $this->select('id, title, published_at')
            ->where('is_published', 1)
            ->where('published_at <=', date('Y-m-d H:i:s'))
            ->orderBy('published_at', 'DESC')
            ->findAll($limit);
    }

    /**
     * 소식 목록 (페이지 나눔)
     */
    public function paginatePublished(int $perPage = 10): array
    {
        return $this->select('id, title, published_at')
            ->where('is_published', 1)
            ->where('published_at <=', date('Y-m-d H:i:s'))
            ->orderBy('published_at', 'DESC')
            ->paginate($perPage);
    }

    /**
     * 공지 상세 (공개 + 게시일이 지난 것만)
     */
    public function findPublished(int $id): ?array
    {
        return $this->where('id', $id)
            ->where('is_published', 1)
            ->where('published_at <=', date('Y-m-d H:i:s'))
            ->first();
    }
}
