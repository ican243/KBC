<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 상담을 어느 화면에서 신청했는지 (통계: 과정 페이지 → 상담 전환)
 * 값 예) home, contact, courses/basic-3m
 */
class AddEntryPageToConsultInquiries extends Migration
{
    public function up()
    {
        $this->forge->addColumn('consult_inquiries', [
            'entry_page' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'source',
                'comment'    => '신청한 화면 (home / contact / courses/주소이름)',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('consult_inquiries', 'entry_page');
    }
}
