<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * 공개 페이지 조회수 +1 (page_views: 날짜·페이지·숫자만, 개인정보 없음)
 *
 * 세는 것: 정상으로 열린(200) HTML 페이지를 GET 으로 요청한 경우
 * 세지 않는 것: POST, 404·오류, HTML 이 아닌 응답(robots.txt 등), 검색엔진·미리보기 로봇
 * 쿠키를 쓰지 않으므로 "방문자 수"가 아니라 "조회수"다. (새로고침도 1회로 셈)
 * 기록에 실패해도 페이지 표시에는 영향이 없다.
 */
class PageView implements FilterInterface
{
    private const BOTS = '/bot|crawl|spider|slurp|preview|facebookexternalhit|kakaotalk-scrap|daum|yeti|headless|python|curl|wget|httpclient|monitor/i';

    public function before(RequestInterface $request, $arguments = null)
    {
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if ($request->getMethod() !== 'GET'
            || $response->getStatusCode() !== 200
            || ! str_contains($response->getHeaderLine('Content-Type'), 'text/html')) {
            return;
        }

        $agent = (string) $request->getUserAgent();
        if ($agent === '' || preg_match(self::BOTS, $agent)) {
            return;
        }

        $path = '/' . trim($request->getUri()->getRoutePath(), '/');

        try {
            db_connect()->query(
                'INSERT INTO page_views (view_date, path, views) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE views = views + 1',
                [date('Y-m-d'), mb_substr($path, 0, 150)]
            );
        } catch (\Throwable $e) {
            log_message('error', '[pageview] 조회수 기록 실패: ' . $e->getMessage());
        }
    }
}
