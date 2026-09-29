<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 관리자 역할과 비밀번호 변경 강제
 * - role                 : owner(대표 관리자) / staff(담당자)
 * - must_change_password : 1 이면 다음 로그인 때 비밀번호 변경 강제 (임시 비밀번호 발급 시)
 * 이 마이그레이션 이전에 있던 관리자는 모두 대표 관리자로 지정한다.
 */
class AddRoleToAdmins extends Migration
{
    public function up()
    {
        $this->forge->addColumn('admins', [
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'staff',
                'after'      => 'name',
                'comment'    => 'owner(대표 관리자) / staff(담당자)',
            ],
            'must_change_password' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'password_hash',
                'comment'    => '1: 다음 로그인 때 비밀번호 변경 필요',
            ],
        ]);

        $this->db->table('admins')->update(['role' => 'owner']);
    }

    public function down()
    {
        $this->forge->dropColumn('admins', ['role', 'must_change_password']);
    }
}
