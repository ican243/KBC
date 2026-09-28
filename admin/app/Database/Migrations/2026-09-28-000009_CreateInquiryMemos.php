<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 문의 메모 및 상태 변경 이력 (상담/협력 공용)
 */
class CreateInquiryMemos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'inquiry_type' => ['type' => 'VARCHAR', 'constraint' => 20, 'comment' => 'consult / partner'],
            'inquiry_id'   => ['type' => 'INT', 'unsigned' => true],
            'admin_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'comment' => '작성 관리자'],
            'memo'         => ['type' => 'TEXT', 'null' => true],
            'status_from'  => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'comment' => '변경 전 상태'],
            'status_to'    => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'comment' => '변경 후 상태'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['inquiry_type', 'inquiry_id']);
        $this->forge->addForeignKey('admin_id', 'admins', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('inquiry_memos', false, ['ENGINE' => 'InnoDB', 'COMMENT' => '문의 메모·상태 이력']);
    }

    public function down()
    {
        $this->forge->dropTable('inquiry_memos');
    }
}
