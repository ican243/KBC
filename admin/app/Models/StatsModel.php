<?php

namespace App\Models;

use App\Libraries\SourceLabel;
use CodeIgniter\Database\BaseConnection;

/**
 * 관리자 통계 (기획안 5장 측정 항목: 상담 제출, 과정 페이지 → 상담 전환, 제휴 문의)
 * 모든 기간은 [시작일 00:00:00, 종료일 23:59:59], 삭제 처리한 문의는 제외한다.
 */
class StatsModel
{
    private BaseConnection $db;
    private string $from;   // 2026-09-01 00:00:00
    private string $to;     // 2026-09-30 23:59:59
    private string $fromDate;
    private string $toDate;

    public function __construct(string $fromDate, string $toDate)
    {
        $this->db       = db_connect();
        $this->fromDate = $fromDate;
        $this->toDate   = $toDate;
        $this->from     = $fromDate . ' 00:00:00';
        $this->to       = $toDate . ' 23:59:59';
    }

    /**
     * 요약 카드
     */
    public function summary(): array
    {
        $consults = $this->countInquiries('consult_inquiries');
        $done     = $this->countInquiries('consult_inquiries', ['status' => 'done']);

        // 과정 페이지 → 상담 전환 = 과정 상세에서 온 상담 ÷ 과정 상세 조회수
        $fromCourse  = $this->countInquiries('consult_inquiries', ['entry_page LIKE' => 'courses/%']);
        $courseViews = (int) $this->db->table('page_views')->selectSum('views')
            ->where('view_date >=', $this->fromDate)->where('view_date <=', $this->toDate)
            ->like('path', '/courses/', 'after')
            ->get()->getRow()->views;

        return [
            'consults'    => $consults,
            'partners'    => $this->countInquiries('partner_inquiries'),
            'doneRate'    => $consults > 0 ? round($done / $consults * 100) : null,
            'done'        => $done,
            'fromCourse'  => $fromCourse,
            'courseViews' => $courseViews,
            'conversion'  => $courseViews > 0 ? round($fromCourse / $courseViews * 100, 1) : null,
            'views'       => (int) $this->db->table('page_views')->selectSum('views')
                ->where('view_date >=', $this->fromDate)->where('view_date <=', $this->toDate)
                ->get()->getRow()->views,
        ];
    }

    /**
     * 날짜별 [날짜 => ['consults' => n, 'views' => n]] (접수가 없는 날도 0 으로 채움)
     */
    public function daily(): array
    {
        $days = [];
        for ($d = strtotime($this->fromDate); $d <= strtotime($this->toDate); $d += 86400) {
            $days[date('Y-m-d', $d)] = ['consults' => 0, 'views' => 0];
        }

        $rows = $this->db->table('consult_inquiries')->select('DATE(created_at) AS d, COUNT(*) AS n')
            ->where('deleted_at', null)->where('created_at >=', $this->from)->where('created_at <=', $this->to)
            ->groupBy('d')->get()->getResultArray();
        foreach ($rows as $r) {
            if (isset($days[$r['d']])) {
                $days[$r['d']]['consults'] = (int) $r['n'];
            }
        }

        $rows = $this->db->table('page_views')->select('view_date AS d, SUM(views) AS n')
            ->where('view_date >=', $this->fromDate)->where('view_date <=', $this->toDate)
            ->groupBy('view_date')->get()->getResultArray();
        foreach ($rows as $r) {
            if (isset($days[$r['d']])) {
                $days[$r['d']]['views'] = (int) $r['n'];
            }
        }

        return $days;
    }

    /**
     * 과정별 상담 [과정명 => 건수]
     */
    public function byCourse(): array
    {
        $rows = $this->db->table('consult_inquiries c')->select('COALESCE(k.title, \'미정\') AS label, COUNT(*) AS n')
            ->join('courses k', 'k.id = c.course_id', 'left')
            ->where('c.deleted_at', null)->where('c.created_at >=', $this->from)->where('c.created_at <=', $this->to)
            ->groupBy('label')->orderBy('n', 'DESC')->get()->getResultArray();

        return array_column($rows, 'n', 'label');
    }

    /**
     * 유입 경로별 상담 [이름 => 건수] (같은 이름끼리 합침)
     */
    public function bySource(): array
    {
        $rows = $this->db->table('consult_inquiries')->select('source, COUNT(*) AS n')
            ->where('deleted_at', null)->where('created_at >=', $this->from)->where('created_at <=', $this->to)
            ->groupBy('source')->get()->getResultArray();

        $out = [];
        foreach ($rows as $r) {
            $label       = SourceLabel::label($r['source']);
            $out[$label] = ($out[$label] ?? 0) + (int) $r['n'];
        }
        arsort($out);

        return $out;
    }

    /**
     * 신청한 화면별 상담 [이름 => 건수]
     */
    public function byEntryPage(): array
    {
        $titles = $this->courseTitles();
        $rows   = $this->db->table('consult_inquiries')->select('entry_page, COUNT(*) AS n')
            ->where('deleted_at', null)->where('created_at >=', $this->from)->where('created_at <=', $this->to)
            ->groupBy('entry_page')->get()->getResultArray();

        $out = [];
        foreach ($rows as $r) {
            $e     = $r['entry_page'];
            $label = match (true) {
                $e === null                    => '기록 없음 (기능 추가 전 접수 등)',
                $e === 'home'                  => '홈 하단',
                $e === 'contact'               => '상담 문의 페이지',
                str_starts_with($e, 'courses/') => '과정 상세: ' . ($titles[substr($e, 8)] ?? substr($e, 8)),
                default                        => $e,
            };
            $out[$label] = ($out[$label] ?? 0) + (int) $r['n'];
        }
        arsort($out);

        return $out;
    }

    /**
     * 많이 본 페이지 [이름 => 조회수] 상위 $limit 개
     */
    public function topPages(int $limit = 10): array
    {
        $rows = $this->db->table('page_views')->select('path, SUM(views) AS n')
            ->where('view_date >=', $this->fromDate)->where('view_date <=', $this->toDate)
            ->groupBy('path')->orderBy('n', 'DESC')->limit($limit)->get()->getResultArray();

        $titles  = $this->courseTitles();
        $notices = array_column($this->db->table('notices')->select('id, title')->get()->getResultArray(), 'title', 'id');
        $names   = ['/' => '홈', '/about' => '아카데미 소개', '/courses' => '교육과정', '/instructors' => '강사진',
                    '/career' => '진로 연계', '/news' => '소식', '/faq' => '자주 묻는 질문', '/contact' => '상담 문의',
                    '/partner' => '협력 제안', '/privacy' => '개인정보 처리방침',
                    '/consult/done' => '상담 접수 완료', '/partner/done' => '협력 접수 완료'];

        $out = [];
        foreach ($rows as $r) {
            $p     = $r['path'];
            $label = $names[$p]
                ?? (preg_match('#^/courses/(.+)$#', $p, $m) ? '과정: ' . ($titles[$m[1]] ?? $m[1])
                : (preg_match('#^/news/(\d+)$#', $p, $m) ? '공지: ' . ($notices[$m[1]] ?? $m[1]) : $p));
            $out[] = ['label' => $label, 'path' => $p, 'views' => (int) $r['n']];
        }

        return $out;
    }

    private function countInquiries(string $table, array $where = []): int
    {
        $b = $this->db->table($table)->where('deleted_at', null)
            ->where('created_at >=', $this->from)->where('created_at <=', $this->to);

        foreach ($where as $key => $value) {
            str_ends_with($key, ' LIKE') ? $b->like(substr($key, 0, -5), rtrim($value, '%'), 'after') : $b->where($key, $value);
        }

        return $b->countAllResults();
    }

    /** [주소이름 => 과정명] */
    private function courseTitles(): array
    {
        return array_column($this->db->table('courses')->select('slug, title')->get()->getResultArray(), 'title', 'slug');
    }
}
