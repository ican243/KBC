<?php

namespace App\Libraries;

/**
 * 유입 경로 값을 읽기 쉬운 이름으로
 *   direct                   → 직접 방문
 *   utm:instagram/social/... → 인스타그램 (링크)
 *   ref:m.search.naver.com   → 네이버 검색
 */
class SourceLabel
{
    /** utm_source 값 → 이름 */
    private const UTM = [
        'instagram' => '인스타그램', 'insta' => '인스타그램', 'ig' => '인스타그램',
        'facebook'  => '페이스북', 'fb' => '페이스북',
        'naver'     => '네이버', 'blog' => '블로그',
        'kakao'     => '카카오', 'kakaotalk' => '카카오',
        'google'    => '구글', 'youtube' => '유튜브', 'tiktok' => '틱톡',
    ];

    /** 유입 사이트 주소 → 이름 (앞에서부터 확인) */
    private const HOSTS = [
        'search.naver.com' => '네이버 검색',
        'blog.naver.com'   => '네이버 블로그',
        'naver.com'        => '네이버',
        'google.'          => '구글 검색',
        'daum.net'         => '다음',
        'instagram.com'    => '인스타그램',
        'facebook.com'     => '페이스북',
        'youtube.com'      => '유튜브',
        'tiktok.com'       => '틱톡',
        'kakao'            => '카카오',
    ];

    public static function label(?string $source): string
    {
        if ($source === null || $source === '') {
            return '알 수 없음';
        }
        if ($source === 'direct') {
            return '직접 방문';
        }

        if (str_starts_with($source, 'utm:')) {
            $name = strtolower(explode('/', substr($source, 4))[0]);

            return (self::UTM[$name] ?? $name) . ' (링크)';
        }

        if (str_starts_with($source, 'ref:')) {
            $host = strtolower(substr($source, 4));
            foreach (self::HOSTS as $needle => $name) {
                if (str_contains($host, $needle)) {
                    return $name;
                }
            }

            return $host;
        }

        return $source;
    }

    /**
     * 필터용 선택지: 이름 => [원래 값...]  (같은 이름끼리 묶음)
     *
     * @param list<?string> $sources DB 의 서로 다른 유입 값
     */
    public static function groups(array $sources): array
    {
        $groups = [];
        foreach ($sources as $raw) {
            $groups[self::label($raw)][] = $raw;
        }
        ksort($groups);

        return $groups;
    }
}
