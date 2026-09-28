<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 알림 발송 기록 (성공/실패, 오류 내용)
 */
class CreateNotificationLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'channel'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'email', 'comment' => 'email (추후 sms 등)'],
            'recipient'     => ['type' => 'VARCHAR', 'constraint' => 255, 'comment' => '받는 곳'],
            'subject'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'related_type'  => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'comment' => 'consult / partner'],
            'related_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 20, 'comment' => 'sent / failed'],
            'error_message' => ['type' => 'TEXT', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['status', 'created_at']);
        $this->forge->createTable('notification_logs', false, ['ENGINE' => 'InnoDB', 'COMMENT' => '알림 발송 기록']);
    }

    public function down()
    {
        $this->forge->dropTable('notification_logs');
    }
}
