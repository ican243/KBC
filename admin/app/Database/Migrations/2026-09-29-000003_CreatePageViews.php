<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 공개 페이지 조회수 (날짜·페이지별 숫자만, 개인정보 없음)
 */
class CreatePageViews extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'view_date' => ['type' => 'DATE', 'comment' => '날짜 (한국 시간)'],
            'path'      => ['type' => 'VARCHAR', 'constraint' => 150, 'comment' => '페이지 주소 (예: /courses/basic-3m)'],
            'views'     => ['type' => 'INT', 'unsigned' => true, 'default' => 0, 'comment' => '조회수'],
        ]);

        $this->forge->addPrimaryKey(['view_date', 'path']);
        $this->forge->createTable('page_views', false, ['ENGINE' => 'InnoDB', 'COMMENT' => '공개 페이지 조회수 (개인정보 없음)']);
    }

    public function down()
    {
        $this->forge->dropTable('page_views');
    }
}
