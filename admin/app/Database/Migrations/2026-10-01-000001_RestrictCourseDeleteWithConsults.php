<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 상담 기록이 연결된 교육과정은 DB 에서도 삭제되지 않게 한다.
 * 전: 과정 삭제 시 상담의 관심 과정이 NULL(미정)로 바뀜 → 상담 이력이 바뀜
 * 후: 연결된 상담이 있으면 삭제 자체가 거부됨 (관리자 화면은 비공개 처리를 안내)
 */
class RestrictCourseDeleteWithConsults extends Migration
{
    private const FK = 'consult_inquiries_course_id_foreign';

    public function up()
    {
        $this->replaceForeignKey('RESTRICT');
    }

    public function down()
    {
        $this->replaceForeignKey('SET NULL');
    }

    private function replaceForeignKey(string $onDelete): void
    {
        $table = $this->db->prefixTable('consult_inquiries');
        $ref   = $this->db->prefixTable('courses');

        $this->db->query("ALTER TABLE `{$table}` DROP FOREIGN KEY `" . self::FK . '`');
        $this->db->query("ALTER TABLE `{$table}` ADD CONSTRAINT `" . self::FK . '` FOREIGN KEY (`course_id`)'
            . " REFERENCES `{$ref}` (`id`) ON DELETE {$onDelete} ON UPDATE CASCADE");
    }
}
