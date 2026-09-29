<?php

namespace App\Commands;

use App\Models\AdminModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * 관리자 2단계 인증 초기화 (휴대폰과 복구 코드를 모두 잃어버렸을 때)
 * 다음 로그인 때 다시 설정 화면이 나온다.
 *
 * 사용: php spark admin:reset-2fa 아이디
 */
class AdminReset2fa extends BaseCommand
{
    protected $group       = 'Admin';
    protected $name        = 'admin:reset-2fa';
    protected $description = '관리자 2단계 인증을 초기화합니다.';
    protected $usage       = 'admin:reset-2fa <아이디>';

    public function run(array $params)
    {
        $username = $params[0] ?? CLI::prompt('아이디', null, 'required');
        $model    = new AdminModel();
        $admin    = $model->findByUsername($username);

        if ($admin === null) {
            CLI::error('없는 아이디입니다.');

            return EXIT_ERROR;
        }

        $model->update($admin['id'], [
            'totp_secret'     => null,
            'totp_enabled_at' => null,
            'recovery_codes'  => null,
        ]);

        CLI::write($username . ' 계정의 2단계 인증을 초기화했습니다. 다음 로그인 때 다시 설정합니다.', 'green');

        return EXIT_SUCCESS;
    }
}
