<?php

namespace App\Commands;

use App\Models\AdminModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * 관리자 계정 생성
 * 비밀번호는 명령어 인자로 받지 않는다. (셸 기록에 남지 않도록 입력으로만 받음)
 * 역할: 대표 관리자가 아직 없으면 기본값 owner, 있으면 staff (새 서버 첫 계정은 대표 관리자)
 *
 * 사용: php spark admin:create
 */
class AdminCreate extends BaseCommand
{
    protected $group       = 'Admin';
    protected $name        = 'admin:create';
    protected $description = '관리자 계정을 생성합니다.';
    protected $usage       = 'admin:create';

    public function run(array $params)
    {
        $model = new AdminModel();

        $username = trim(CLI::prompt('아이디', null, 'required'));
        if ($model->findByUsername($username) !== null) {
            CLI::error('이미 사용 중인 아이디입니다.');

            return EXIT_ERROR;
        }

        $name = trim(CLI::prompt('이름', null, 'required'));

        $default = $model->countActiveOwners() === 0 ? 'owner' : 'staff';
        $role    = CLI::prompt('역할 (owner=대표 관리자 / staff=담당자)', $default === 'owner' ? ['owner', 'staff'] : ['staff', 'owner']);

        $password = $this->readPassword('비밀번호: ');
        if (mb_strlen($password) < 8) {
            CLI::error('비밀번호는 8자 이상이어야 합니다.');

            return EXIT_ERROR;
        }

        if ($this->isInteractive() && $this->readPassword('비밀번호 확인: ') !== $password) {
            CLI::error('비밀번호가 일치하지 않습니다.');

            return EXIT_ERROR;
        }

        $model->insert([
            'username'      => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'name'          => $name,
            'role'          => $role,
            'status'        => 'active',
        ]);

        CLI::write('관리자 계정이 생성되었습니다: ' . $username . ' (' . ($role === 'owner' ? '대표 관리자' : '담당자') . ')', 'green');

        return EXIT_SUCCESS;
    }

    private function isInteractive(): bool
    {
        return function_exists('posix_isatty') && posix_isatty(STDIN);
    }

    /**
     * 터미널에서는 입력 내용을 화면에 표시하지 않는다.
     */
    private function readPassword(string $label): string
    {
        CLI::print($label);

        if ($this->isInteractive()) {
            shell_exec('stty -echo');
            $value = fgets(STDIN);
            shell_exec('stty echo');
            CLI::newLine();
        } else {
            $value = fgets(STDIN);
        }

        return rtrim((string) $value, "\r\n");
    }
}
