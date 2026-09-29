<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table         = 'admins';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'username',
        'password_hash',
        'name',
        'status',
        'failed_attempts',
        'locked_until',
        'totp_secret',
        'last_login_at',
        'last_login_ip',
    ];

    // 연속 실패 허용 횟수 / 초과 시 차단 시간(분)
    public const MAX_FAILED   = 5;
    public const LOCK_MINUTES = 10;

    /**
     * 담당자 선택용 [id => 이름] (사용 중인 계정만)
     */
    public function options(): array
    {
        return array_column($this->select('id, name')->where('status', 'active')->orderBy('id')->findAll(), 'name', 'id');
    }

    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }

    public function isLocked(array $admin): bool
    {
        return $admin['locked_until'] !== null
            && strtotime($admin['locked_until']) > time();
    }

    /**
     * 로그인 실패 기록. 허용 횟수를 넘으면 일정 시간 차단한다.
     */
    public function recordFailure(array $admin): void
    {
        $failed = (int) $admin['failed_attempts'] + 1;
        $data   = ['failed_attempts' => $failed];

        if ($failed >= self::MAX_FAILED) {
            $data['failed_attempts'] = 0;
            $data['locked_until']    = date('Y-m-d H:i:s', time() + self::LOCK_MINUTES * 60);
        }

        $this->update($admin['id'], $data);
    }

    public function recordSuccess(array $admin, string $ip): void
    {
        $this->update($admin['id'], [
            'failed_attempts' => 0,
            'locked_until'    => null,
            'last_login_at'   => date('Y-m-d H:i:s'),
            'last_login_ip'   => $ip,
        ]);
    }
}
