<?php

namespace App\Libraries;

/**
 * 접수번호 생성
 * 형식: 접두어 + 날짜 + '-' + 무작위 5자리  예) C20261001-7F3K2
 * 헷갈리는 문자(0, O, 1, I)는 사용하지 않는다.
 */
class ReceiptNumber
{
    private const CHARS  = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    private const LENGTH = 5;

    public const CONSULT = 'C';
    public const PARTNER = 'P';

    public static function make(string $prefix): string
    {
        $code = '';
        $max  = strlen(self::CHARS) - 1;

        for ($i = 0; $i < self::LENGTH; $i++) {
            $code .= self::CHARS[random_int(0, $max)];
        }

        return $prefix . date('Ymd') . '-' . $code;
    }
}
