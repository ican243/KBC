<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 교육과정 (속성 6주 / 기본 3개월 / 심화 6개월)
 */
class CreateCourses extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'slug'              => ['type' => 'VARCHAR', 'constraint' => 50, 'comment' => '주소용 이름'],
            'title'             => ['type' => 'VARCHAR', 'constraint' => 100, 'comment' => '과정명'],
            'summary'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => '카드용 한 줄 소개'],
            'target'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => '대상'],
            'duration'          => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'comment' => '기간'],
            'sessions_per_week' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'comment' => '주당 수업 횟수'],
            'fields'            => ['type' => 'TEXT', 'null' => true, 'comment' => '교육 분야'],
            'assignments'       => ['type' => 'TEXT', 'null' => true, 'comment' => '실습 과제'],
            'outputs'           => ['type' => 'TEXT', 'null' => true, 'comment' => '수료 산출물'],
            'fee_text'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => '교습비 안내 (확정 후 입력)'],
            'is_confirmed'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'comment' => '1: 확정 / 0: 제안 과정(준비 중 표시)'],
            'is_published'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'comment' => '홈페이지 노출 여부'],
            'sort_order'        => ['type' => 'INT', 'default' => 0],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('courses', false, ['ENGINE' => 'InnoDB', 'COMMENT' => '교육과정']);
    }

    public function down()
    {
        $this->forge->dropTable('courses');
    }
}
