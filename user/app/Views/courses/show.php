<?php
/**
 * 과정 상세 - 기획안 4장 공통 틀: 대상, 기간, 주당 수업, 교육 분야, 실습 과제, 수료 산출물, 상담 버튼 순서
 *
 * @var array $course
 * @var array $others
 * @var array $settings
 */
$feeNotice = setting($settings, 'fee_notice', '확정 후 안내');
$items = [
    '대상'           => $course['target'],
    '기간'           => $course['duration'],
    '주당 수업 횟수' => $course['sessions_per_week'],
    '교육 분야'      => $course['fields'],
    '실습 과제'      => $course['assignments'],
    '수료 산출물'    => $course['outputs'],
];
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-head"><div class="wrap">
    <span class="label label-dark">교육과정</span>
    <h1><?= esc($course['title']) ?></h1>
    <p><?= esc($course['summary']) ?></p>
    <?php if (! $course['is_confirmed']): ?>
        <p class="head-note">현재 커리큘럼 제안이며 확정 시간표가 아닙니다. 세부 시간표와 교습비는 확정 후 안내합니다.</p>
    <?php endif ?>
</div></section>

<section class="section"><div class="wrap narrow">
    <dl class="detail">
        <?php foreach ($items as $label => $value): ?>
            <dt><?= $label ?></dt>
            <dd><?= $value !== null && $value !== '' ? nl2br(esc($value)) : '<span class="pending">준비 중</span>' ?></dd>
        <?php endforeach ?>
        <dt>교습비</dt>
        <dd><?= $course['is_confirmed'] && $course['fee_text'] ? esc($course['fee_text']) : '<span class="pending">' . esc($feeNotice) . '</span>' ?></dd>
    </dl>

    <?php if (setting($settings, 'refund_policy')): ?>
    <div class="box-soft refund">
        <h2 class="sub-title">교습비 반환 기준</h2>
        <p><?= nl2br(esc(setting($settings, 'refund_policy'))) ?></p>
    </div>
    <?php endif ?>

    <a class="btn btn-live btn-block" href="<?= site_url('contact') . '?course=' . (int) $course['id'] . '&amp;from=course' ?>">이 과정 상담 신청하기</a>

    <?php if ($others !== []): ?>
    <div class="other-links">
        <b>다른 과정</b>
        <?php foreach ($others as $o): ?>
            <a href="<?= site_url('courses/' . $o['slug']) ?>"><?= esc($o['title']) ?> →</a>
        <?php endforeach ?>
        <a href="<?= site_url('courses') ?>">전체 비교 →</a>
    </div>
    <?php endif ?>
</div></section>
<?= $this->endSection() ?>
