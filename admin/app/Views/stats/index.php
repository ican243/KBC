<?php
/**
 * 통계 (막대그래프는 CSS 로 그림 - 외부 라이브러리 없음)
 *
 * @var array  $ranges
 * @var string $range   7 | 30 | 90 | custom
 * @var string $from
 * @var string $to
 * @var array  $summary
 * @var array  $daily    [날짜 => ['consults', 'views']]
 * @var array  $byCourse [이름 => 건수]
 * @var array  $bySource
 * @var array  $byEntry
 * @var array  $topPages [['label', 'path', 'views']]
 */

// 가로 막대 목록 [이름 => 숫자]
$bars = static function (array $items, string $unit = '건'): string {
    if ($items === []) {
        return '<p class="empty">기간 안에 기록이 없습니다.</p>';
    }
    $max  = max($items) ?: 1;
    $html = '<ul class="hbars">';
    foreach ($items as $label => $n) {
        $html .= '<li><span class="hb-label">' . esc($label) . '</span>'
            . '<span class="hb-track"><i style="width:' . round($n / $max * 100) . '%"></i></span>'
            . '<b>' . number_format($n) . $unit . '</b></li>';
    }

    return $html . '</ul>';
};
$maxConsult = max(array_column($daily, 'consults') ?: [0]) ?: 1;
$maxViews   = max(array_column($daily, 'views') ?: [0]) ?: 1;
$pct        = static fn (?float $v): string => $v === null ? '-' : $v . '%';
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="head-row">
    <h1>통계</h1>
    <form class="filters" method="get" action="<?= site_url('stats') ?>">
        <label>기간
            <select name="range">
                <?php foreach ($ranges as $key => $label): ?>
                <option value="<?= $key ?>"<?= $range === (string) $key ? ' selected' : '' ?>><?= $label ?></option>
                <?php endforeach ?>
                <option value="custom"<?= $range === 'custom' ? ' selected' : '' ?>>직접 선택</option>
            </select>
        </label>
        <label>부터 <input type="date" name="from" value="<?= esc($from, 'attr') ?>" max="<?= date('Y-m-d') ?>"></label>
        <label>까지 <input type="date" name="to" value="<?= esc($to, 'attr') ?>" max="<?= date('Y-m-d') ?>"></label>
        <button class="btn" type="submit">조회</button>
    </form>
</div>
<p class="help"><?= esc($from) ?> ~ <?= esc($to) ?> · 조회수는 페이지를 연 횟수입니다 (새로고침 포함, 검색엔진 로봇 제외, 개인정보 미수집).</p>

<div class="stats">
    <div class="stat"><span class="stat-icon i-blue"><?= icon('chat') ?></span><span>상담 접수</span><b><?= number_format($summary['consults']) ?></b></div>
    <div class="stat"><span class="stat-icon i-purple"><?= icon('briefcase') ?></span><span>협력 제안</span><b><?= number_format($summary['partners']) ?></b></div>
    <div class="stat"><span class="stat-icon i-green"><?= icon('check') ?></span><span>상담완료 비율</span><b><?= $pct($summary['doneRate']) ?></b>
        <small class="muted"><?= $summary['done'] ?>건 완료</small></div>
    <div class="stat"><span class="stat-icon i-cyan"><?= icon('chart') ?></span><span>과정 페이지 → 상담 전환</span><b><?= $pct($summary['conversion']) ?></b>
        <small class="muted">과정 상세에서 온 상담 <?= $summary['fromCourse'] ?>건 ÷ 과정 상세 조회 <?= number_format($summary['courseViews']) ?>회</small></div>
</div>

<div class="box">
    <h2>날짜별 <small class="muted">· 전체 조회수 <?= number_format($summary['views']) ?>회</small></h2>
    <div class="vchart" role="img" aria-label="날짜별 상담 접수와 조회수">
        <?php foreach ($daily as $date => $d): ?>
        <div class="vc-day" title="<?= esc($date) ?> · 상담 <?= $d['consults'] ?>건 · 조회 <?= $d['views'] ?>회">
            <div class="vc-bars">
                <i class="vc-views" style="height:<?= round($d['views'] / $maxViews * 100) ?>%"></i>
                <i class="vc-consults" style="height:<?= round($d['consults'] / $maxConsult * 100) ?>%"></i>
            </div>
            <span><?= count($daily) <= 31 ? date('n/j', strtotime($date)) : '' ?></span>
        </div>
        <?php endforeach ?>
    </div>
    <p class="legend"><i class="vc-consults"></i> 상담 접수 (최대 <?= $maxConsult ?>건) <i class="vc-views"></i> 조회수 (최대 <?= number_format($maxViews) ?>회) · 막대에 마우스를 올리면 숫자가 보입니다</p>
</div>

<div class="grid2">
    <div class="box"><h2>과정별 상담</h2><?= $bars($byCourse) ?></div>
    <div class="box"><h2>유입 경로별 상담</h2><?= $bars($bySource) ?></div>
    <div class="box"><h2>신청한 화면별 상담</h2><?= $bars($byEntry) ?></div>
    <div class="box"><h2>많이 본 페이지 (상위 10)</h2><?= $bars(array_column($topPages, 'views', 'label'), '회') ?></div>
</div>
<?= $this->endSection() ?>
