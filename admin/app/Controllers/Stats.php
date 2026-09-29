<?php

namespace App\Controllers;

use App\Models\StatsModel;

/**
 * 통계 (기간: 최근 7·30·90일 또는 직접 선택, 최대 1년)
 */
class Stats extends BaseController
{
    private const RANGES = ['7' => '최근 7일', '30' => '최근 30일', '90' => '최근 90일'];

    public function index()
    {
        [$range, $from, $to] = $this->period();
        $stats = new StatsModel($from, $to);

        return view('stats/index', [
            'title'     => '통계',
            'ranges'    => self::RANGES,
            'range'     => $range,
            'from'      => $from,
            'to'        => $to,
            'summary'   => $stats->summary(),
            'daily'     => $stats->daily(),
            'byCourse'  => $stats->byCourse(),
            'bySource'  => $stats->bySource(),
            'byEntry'   => $stats->byEntryPage(),
            'topPages'  => $stats->topPages(10),
        ]);
    }

    /**
     * @return array{0: string, 1: string, 2: string} [선택한 기간, 시작일, 종료일]
     */
    private function period(): array
    {
        $today = date('Y-m-d');
        $range = (string) $this->request->getGet('range');
        $isDate = static fn ($v): bool => is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) && strtotime($v) !== false;

        if ($range === 'custom') {
            $from = (string) $this->request->getGet('from');
            $to   = (string) $this->request->getGet('to');
            if ($isDate($from) && $isDate($to) && $from <= $to && $to <= $today
                && (strtotime($to) - strtotime($from)) <= 366 * 86400) {
                return ['custom', $from, $to];
            }
            $range = '30';
        }

        $range = isset(self::RANGES[$range]) ? $range : '30';

        return [$range, date('Y-m-d', strtotime('-' . ((int) $range - 1) . ' days')), $today];
    }
}
