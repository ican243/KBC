<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 관리자 2단계 인증 (OTP)
 * - totp_secret      : 기존 컬럼. 암호화한 OTP 비밀키 저장
 * - totp_enabled_at  : 2단계 인증 설정 완료 일시 (NULL 이면 미설정)
 * - recovery_codes   : 복구 코드 해시 목록 (JSON, 한 번 쓰면 삭제)
 */
class AddTwoFactorToAdmins extends Migration
{
    public function up()
    {
        $this->forge->addColumn('admins', [
            'totp_enabled_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'after'   => 'totp_secret',
                'comment' => '2단계 인증 설정 일시',
            ],
            'recovery_codes' => [
                'type'    => 'TEXT',
                'null'    => true,
                'after'   => 'totp_enabled_at',
                'comment' => '복구 코드 해시 (JSON)',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('admins', ['totp_enabled_at', 'recovery_codes']);
    }
}
