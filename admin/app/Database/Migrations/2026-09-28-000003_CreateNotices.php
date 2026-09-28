<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 소식·공지
 */
class CreateNotices extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'title'        => ['type' => 'VARCHAR', 'constraint' => 200],
            'content'      => ['type' => 'TEXT'],
            'is_published' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'published_at' => ['type' => 'DATETIME', 'null' => true, 'comment' => '게시일'],
            'admin_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'comment' => '작성 관리자'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['is_published', 'published_at']);
        $this->forge->addForeignKey('admin_id', 'admins', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('notices', false, ['ENGINE' => 'InnoDB', 'COMMENT' => '소식·공지']);
    }

    public function down()
    {
        $this->forge->dropTable('notices');
    }
}
