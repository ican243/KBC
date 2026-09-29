<?php

namespace App\Libraries;

/**
 * 문의 처리 상태 (상담/협력 공용)
 */
class InquiryStatus
{
    public const LABELS = [
        'new'       => '신규',
        'contacted' => '연락',
        'done'      => '상담완료',
        'hold'      => '보류',
    ];

    public static function label(string $status): string
    {
        return self::LABELS[$status] ?? $status;
    }

    public static function keys(): array
    {
        return array_keys(self::LABELS);
    }
}
