<?php

namespace App\Commands;

use App\Libraries\AuditLog;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * 보존 기간이 지난 문의 파기 (개인정보 보호법: 목적 달성·보유 기간 경과 시 지체 없이 파기)
 *
 * - 보존 기간은 admin/.env 의 inquiry.retentionDays (일). 비어 있으면 아무것도 지우지 않는다.
 * - 접수일(created_at)로부터 보존 기간이 지난 상담·협력 문의를 완전히 삭제하고,
 *   해당 문의의 처리 이력(inquiry_memos)과 알림 기록(notification_logs)도 함께 삭제한다.
 *
 * 사용: php spark inquiry:purge            실제 삭제
 *       php spark inquiry:purge --dry-run  삭제 대상 건수만 확인
 */
class InquiryPurge extends BaseCommand
{
    protected $group       = 'Admin';
    protected $name        = 'inquiry:purge';
    protected $description = '보존 기간이 지난 문의를 파기합니다.';
    protected $usage       = 'inquiry:purge [--dry-run]';
    protected $options     = ['--dry-run' => '삭제하지 않고 대상 건수만 표시'];

    private const TABLES = ['consult' => 'consult_inquiries', 'partner' => 'partner_inquiries'];

    public function run(array $params)
    {
        $days = (int) env('inquiry.retentionDays', 0);
        if ($days <= 0) {
            CLI::write('보존 기간(inquiry.retentionDays)이 설정되지 않아 파기하지 않습니다.', 'yellow');

            return EXIT_SUCCESS;
        }

        $dryRun = CLI::getOption('dry-run') !== null;
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $db     = db_connect();
        $total  = 0;

        foreach (self::TABLES as $type => $table) {
            $ids = array_column($db->table($table)->select('id')->where('created_at <', $cutoff)->get()->getResultArray(), 'id');
            $total += count($ids);

            CLI::write(sprintf('%s: %d건 (접수일 %s 이전)', $table, count($ids), $cutoff));
            if ($dryRun || $ids === []) {
                continue;
            }

            $db->transStart();
            $db->table('inquiry_memos')->where('inquiry_type', $type)->whereIn('inquiry_id', $ids)->delete();
            $db->table('notification_logs')->where('related_type', $type)->whereIn('related_id', $ids)->delete();
            $db->table($table)->whereIn('id', $ids)->delete();
            $db->transComplete();
        }

        if ($dryRun) {
            CLI::write('(확인만 했습니다. 실제로 지우지 않았습니다.)', 'yellow');
        } elseif ($total > 0) {
            AuditLog::write('inquiry_purge', "{$total}건 (보존 {$days}일)");
            CLI::write("{$total}건을 파기했습니다.", 'green');
        }

        return EXIT_SUCCESS;
    }
}
