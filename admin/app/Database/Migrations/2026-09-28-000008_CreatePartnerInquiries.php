<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 기관 협력 제안
 */
class CreatePartnerInquiries extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'receipt_no'        => ['type' => 'VARCHAR', 'constraint' => 20, 'comment' => '접수번호 (P+날짜-코드)'],
            'org_name'          => ['type' => 'VARCHAR', 'constraint' => 100, 'comment' => '기관명'],
            'contact_name'      => ['type' => 'VARCHAR', 'constraint' => 50, 'comment' => '담당자'],
            'phone'             => ['type' => 'VARCHAR', 'constraint' => 20],
            'partner_type'      => ['type' => 'VARCHAR', 'constraint' => 20, 'comment' => 'space(교육장)/instructor(강사)/hiring(채용)'],
            'message'           => ['type' => 'TEXT', 'comment' => '제안 내용'],
            'agree_privacy_at'  => ['type' => 'DATETIME', 'comment' => '개인정보 수집·이용 동의 일시 (필수)'],
            'source'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => '유입 경로'],
            'status'            => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'new', 'comment' => 'new/contacted/done/hold'],
            'assigned_admin_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'comment' => '담당자'],
            'ip'                => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true, 'comment' => '접수일'],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'        => ['type' => 'DATETIME', 'null' => true, 'comment' => '삭제 처리 일시'],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('receipt_no');
        $this->forge->addKey(['status', 'created_at']);
        $this->forge->addForeignKey('assigned_admin_id', 'admins', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('partner_inquiries', false, ['ENGINE' => 'InnoDB', 'COMMENT' => '기관 협력 제안']);
    }

    public function down()
    {
        $this->forge->dropTable('partner_inquiries');
    }
}
