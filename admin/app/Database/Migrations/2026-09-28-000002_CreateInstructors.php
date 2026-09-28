<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 강사진
 * 실명/사진/경력은 증빙과 게시 동의(consent_at)를 받은 뒤에만 노출한다.
 */
class CreateInstructors extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'field'        => ['type' => 'VARCHAR', 'constraint' => 50, 'comment' => '분야 (댄스·표현 등)'],
            'criteria'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => '강사 섭외 기준'],
            'description'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => '교육 및 실습 내용'],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'comment' => '실명 (동의 후 입력)'],
            'profile'      => ['type' => 'TEXT', 'null' => true],
            'career'       => ['type' => 'TEXT', 'null' => true, 'comment' => '검증된 경력'],
            'photo_path'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'consent_at'   => ['type' => 'DATETIME', 'null' => true, 'comment' => '실명·사진 게시 동의 일시'],
            'is_published' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'sort_order'   => ['type' => 'INT', 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('instructors', false, ['ENGINE' => 'InnoDB', 'COMMENT' => '강사진']);
    }

    public function down()
    {
        $this->forge->dropTable('instructors');
    }
}
