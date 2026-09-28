<?php

namespace App\Libraries;

use CodeIgniter\HTTP\IncomingRequest;

/**
 * 문의 폼 스팸·중복 방지
 *
 * 1) 숨김 칸(website)이 채워져 있으면 봇
 * 2) 폼을 연 지 3초 안에 제출하면 봇
 * 3) 같은 IP는 10분에 3회까지
 * 4) 같은 연락처는 10분 안에 재접수 불가
 *
 * 공개 사이트 DB 계정은 문의를 조회할 수 없으므로 3), 4)는 캐시(writable/cache)에 기록한다.
 * 연락처는 해시로만 저장한다.
 */
class SpamGuard
{
    public const HONEYPOT_FIELD = 'website';
    public const TIME_FIELD     = 'form_ts';

    private const MIN_SECONDS  = 3;
    private const IP_LIMIT     = 3;
    private const WINDOW       = 600;

    /**
     * 봇으로 판단되면 true
     */
    public function isBot(IncomingRequest $request): bool
    {
        if (trim((string) $request->getPost(self::HONEYPOT_FIELD)) !== '') {
            return true;
        }

        $renderedAt = $request->getPost(self::TIME_FIELD);
        if (! ctype_digit((string) $renderedAt)) {
            return true;
        }

        return (time() - (int) $renderedAt) < self::MIN_SECONDS;
    }

    /**
     * 같은 IP 제출 횟수 초과면 true (호출할 때마다 1회로 계산)
     */
    public function isTooFrequent(string $ip, string $form): bool
    {
        $key = 'kbc_rate_' . md5($form . '|' . $ip);

        return service('throttler')->check($key, self::IP_LIMIT, self::WINDOW) === false;
    }

    public function isDuplicate(string $phone, string $form): bool
    {
        return cache($this->duplicateKey($phone, $form)) !== null;
    }

    /**
     * 접수 성공 후 호출. 같은 연락처를 일정 시간 기억한다.
     */
    public function remember(string $phone, string $form): void
    {
        cache()->save($this->duplicateKey($phone, $form), 1, self::WINDOW);
    }

    private function duplicateKey(string $phone, string $form): string
    {
        return 'kbc_dup_' . hash('sha256', $form . '|' . preg_replace('/\D/', '', $phone));
    }
}
