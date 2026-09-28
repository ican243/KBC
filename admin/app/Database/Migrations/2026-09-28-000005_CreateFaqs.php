<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 자주 묻는 질문
 */
class CreateFaqs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'category'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'comment' => '분류 (수강, 환불, 연령 등)'],
            'question'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'answer'       => ['type' => 'TEXT'],
            'is_published' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'sort_order'   => ['type' => 'INT', 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('faqs', false, ['ENGINE' => 'InnoDB', 'COMMENT' => '자주 묻는 질문']);
    }

    public function down()
    {
        $this->forge->dropTable('faqs');
    }
}
