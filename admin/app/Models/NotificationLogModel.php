<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 알림 발송 기록 (공개 사이트가 남긴 기록을 조회만 한다)
 */
class NotificationLogModel extends Model
{
    protected $table      = 'notification_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    public function recentFailures(int $days = 7): int
    {
        return $this->where('status', 'failed')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")))
            ->countAllResults();
    }
}
