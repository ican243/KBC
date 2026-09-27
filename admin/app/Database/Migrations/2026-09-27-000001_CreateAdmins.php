<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 관리자 계정 테이블
 */
class CreateAdmins extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => '로그인 아이디',
            ],
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'comment'    => 'password_hash() 결과',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => '표시 이름',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'active',
                'comment'    => 'active / disabled',
            ],
            'failed_attempts' => [
                'type'       => 'TINYINT',
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => '연속 로그인 실패 횟수',
            ],
            'locked_until' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => '이 시각까지 로그인 차단',
            ],
            'totp_secret' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => '2단계 인증 키 (추후 적용)',
            ],
            'last_login_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_login_ip' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('admins', false, [
            'ENGINE'  => 'InnoDB',
            'COMMENT' => '관리자 계정',
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('admins');
    }
}
