<?php

namespace App\Libraries;

/**
 * 개인정보 열람·다운로드 등 관리자 작업 기록
 * writable/logs/audit-YYYY-MM-DD.log 에 한 줄씩 남긴다. (운영 모드에서도 기록)
 */
class AuditLog
{
    public static function write(string $action, string $detail = ''): void
    {
        $line = sprintf(
            "%s\tadmin=%s(%s)\tip=%s\t%s\t%s\n",
            date('Y-m-d H:i:s'),
            session('admin_id') ?? '-',
            session('admin_name') ?? '-',
            service('request')->getIPAddress(),
            $action,
            str_replace(["\t", "\n", "\r"], ' ', $detail)
        );

        file_put_contents(WRITEPATH . 'logs/audit-' . date('Y-m-d') . '.log', $line, FILE_APPEND | LOCK_EX);
    }
}
