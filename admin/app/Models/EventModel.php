<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 설명회 일정 (관리자)
 */
class EventModel extends Model
{
    protected $table         = 'events';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['title', 'event_at', 'place', 'description', 'is_published'];

    public function listing(): array
    {
        return $this->orderBy('event_at', 'DESC')->orderBy('id', 'DESC')->findAll();
    }
}
