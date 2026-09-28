<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table      = 'events';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    /**
     * 앞으로 열릴 공개 설명회 (가까운 순)
     */
    public function getUpcoming(int $limit = 3): array
    {
        return $this->where('is_published', 1)
            ->where('event_at >=', date('Y-m-d H:i:s'))
            ->orderBy('event_at', 'ASC')
            ->findAll($limit);
    }
}
