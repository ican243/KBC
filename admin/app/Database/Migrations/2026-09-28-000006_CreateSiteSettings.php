<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 사이트 설정 (연락처, 운영 주체, 등록 정보 등)
 * 값이 비어 있으면 홈페이지에 노출하지 않거나 "준비 중"으로 표시한다.
 */
class CreateSiteSettings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'setting_key' => ['type' => 'VARCHAR', 'constraint' => 50],
            'label'       => ['type' => 'VARCHAR', 'constraint' => 100, 'comment' => '관리자 화면 표시명'],
            'value'       => ['type' => 'TEXT', 'null' => true],
            'sort_order'  => ['type' => 'INT', 'default' => 0],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('setting_key');
        $this->forge->createTable('site_settings', false, ['ENGINE' => 'InnoDB', 'COMMENT' => '사이트 설정']);
    }

    public function down()
    {
        $this->forge->dropTable('site_settings');
    }
}
