<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 설명회 일정
 */
class CreateEvents extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'title'        => ['type' => 'VARCHAR', 'constraint' => 200],
            'event_at'     => ['type' => 'DATETIME', 'comment' => '일시'],
            'place'        => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true, 'comment' => '장소'],
            'description'  => ['type' => 'TEXT', 'null' => true],
            'is_published' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('event_at');
        $this->forge->createTable('events', false, ['ENGINE' => 'InnoDB', 'COMMENT' => '설명회 일정']);
    }

    public function down()
    {
        $this->forge->dropTable('events');
    }
}
