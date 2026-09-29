<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\NoticeModel;

/**
 * 검색엔진용 robots.txt, sitemap.xml
 * - 개발 모드: 모든 수집 차단 (개발 중인 화면이 검색에 나오지 않게)
 * - 운영 모드(.env CI_ENVIRONMENT = production): 공개 페이지 허용 + 사이트맵 안내
 * robots.txt 는 도메인 맨 앞(/robots.txt)에 있어야 검색엔진이 읽는다 → 도메인 연결 후 효과.
 */
class Seo extends BaseController
{
    /** 사이트맵에 넣을 고정 페이지 [주소, 변경 빈도, 중요도] */
    private const PAGES = [
        ['', 'weekly', '1.0'],
        ['about', 'monthly', '0.8'],
        ['courses', 'weekly', '0.9'],
        ['instructors', 'monthly', '0.7'],
        ['career', 'monthly', '0.7'],
        ['news', 'daily', '0.7'],
        ['faq', 'monthly', '0.6'],
        ['contact', 'monthly', '0.8'],
        ['privacy', 'yearly', '0.3'],
    ];

    public function robots()
    {
        $body = ENVIRONMENT === 'production'
            ? "User-agent: *\nAllow: /\nDisallow: /admin/\n\nSitemap: " . site_url('sitemap.xml') . "\n"
            : "# 개발 중인 사이트입니다.\nUser-agent: *\nDisallow: /\n";

        return $this->response->setContentType('text/plain', 'UTF-8')->setBody($body);
    }

    public function sitemap()
    {
        $urls = [];
        foreach (self::PAGES as [$path, $freq, $priority]) {
            $urls[] = ['loc' => site_url($path), 'changefreq' => $freq, 'priority' => $priority];
        }

        foreach ((new CourseModel())->getPublished() as $course) {
            $urls[] = ['loc' => site_url('courses/' . $course['slug']), 'lastmod' => $course['updated_at'], 'changefreq' => 'monthly', 'priority' => '0.8'];
        }

        foreach ((new NoticeModel())->getLatest(200) as $notice) {
            $urls[] = ['loc' => site_url('news/' . $notice['id']), 'lastmod' => $notice['published_at'], 'changefreq' => 'yearly', 'priority' => '0.5'];
        }

        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        foreach ($urls as $u) {
            $xml->startElement('url');
            $xml->writeElement('loc', $u['loc']);
            if (! empty($u['lastmod'])) {
                $xml->writeElement('lastmod', date('Y-m-d', strtotime($u['lastmod'])));
            }
            $xml->writeElement('changefreq', $u['changefreq']);
            $xml->writeElement('priority', $u['priority']);
            $xml->endElement();
        }
        $xml->endElement();
        $xml->endDocument();

        return $this->response->setContentType('application/xml', 'UTF-8')->setBody($xml->outputMemory());
    }
}
