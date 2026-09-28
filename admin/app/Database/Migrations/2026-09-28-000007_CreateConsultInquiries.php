<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 교육 상담 신청
 * 공개 사이트 계정(kbc_user)은 INSERT 만 가능하다. 조회는 관리자만.
 */
class CreateConsultInquiries extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'receipt_no'         => ['type' => 'VARCHAR', 'constraint' => 20, 'comment' => '접수번호 (C+날짜-코드)'],
            'name'               => ['type' => 'VARCHAR', 'constraint' => 50],
            'phone'              => ['type' => 'VARCHAR', 'constraint' => 20],
            'course_id'          => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'comment' => '관심 과정 (미정이면 NULL)'],
            'contact_time'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'comment' => '희망 연락시간'],
            'message'            => ['type' => 'TEXT', 'null' => true, 'comment' => '문의내용'],
            'agree_privacy_at'   => ['type' => 'DATETIME', 'comment' => '개인정보 수집·이용 동의 일시 (필수)'],
            'agree_marketing'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'comment' => '홍보성 연락 동의 (선택)'],
            'agree_marketing_at' => ['type' => 'DATETIME', 'null' => true],
            'source'             => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => '유입 경로'],
            'status'             => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'new', 'comment' => 'new/contacted/done/hold'],
            'assigned_admin_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'comment' => '담당자'],
            'ip'                 => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'         => ['type' => 'DATETIME', 'null' => true, 'comment' => '접수일'],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'         => ['type' => 'DATETIME', 'null' => true, 'comment' => '삭제 처리 일시'],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('receipt_no');
        $this->forge->addKey(['status', 'created_at']);
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('assigned_admin_id', 'admins', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('consult_inquiries', false, ['ENGINE' => 'InnoDB', 'COMMENT' => '교육 상담 신청']);
    }

    public function down()
    {
        $this->forge->dropTable('consult_inquiries');
    }
}
