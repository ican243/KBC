<?php
/**
 * 교육과정 목록 + 비교표 (기획안 2장·4장)
 * 교습비 안내 문구(fee_notice)와 반환 기준(refund_policy)은 사이트 설정 값. 반환 기준이 비어 있으면 영역을 숨긴다.
 *
 * @var array $courses
 * @var array $settings
 */
$feeNotice = setting($settings, 'fee_notice', '확정 후 안내');
$rows = [
    '대상'        => 'target',
    '기간'        => 'duration',
    '주당 수업'   => 'sessions_per_week',
    '수료 산출물' => 'outputs',
];
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-head"><div class="wrap">
    <span class="label label-dark">교육과정</span>
    <h1>목표에 맞게 고르는 교육과정</h1>
    <p>현재 커리큘럼 제안 단계이며, 세부 시간표와 교습비는 확정 후 안내합니다.</p>
</div></section>

<section class="section"><div class="wrap">
    <?php if ($courses === []): ?>
        <div class="empty">교육과정은 확정 후 안내합니다.</div>
    <?php else: ?>
    <h2 class="sr-only">과정 목록</h2>
    <div class="courses">
        <?php foreach ($courses as $c): ?>
        <article class="course">
            <div class="top">
                <span class="dur"><?= esc($c['duration']) ?></span>
                <?php if (! $c['is_confirmed']): ?><span class="tag">세부 시간표·교습비 확정 전</span><?php endif ?>
            </div>
            <h3><?= esc($c['title']) ?></h3>
            <p><?= esc($c['summary']) ?></p>
            <ul>
                <?php if ($c['target']): ?><li><b>대상</b><?= esc($c['target']) ?></li><?php endif ?>
                <?php if ($c['sessions_per_week']): ?><li><b>수업</b><?= esc($c['sessions_per_week']) ?></li><?php endif ?>
            </ul>
            <a class="btn btn-line" href="<?= site_url('courses/' . $c['slug']) ?>">자세히 보기</a>
        </article>
        <?php endforeach ?>
    </div>

    <h2 class="sub-title">한눈에 비교</h2>
    <div class="compare-wrap"><table class="compare">
        <thead><tr><th scope="col"></th>
            <?php foreach ($courses as $c): ?><th scope="col"><?= esc($c['title']) ?></th><?php endforeach ?>
        </tr></thead>
        <tbody>
        <?php foreach ($rows as $label => $key): ?>
            <tr><th scope="row"><?= $label ?></th>
                <?php foreach ($courses as $c): ?><td><?= esc($c[$key] ?? '-') ?></td><?php endforeach ?>
            </tr>
        <?php endforeach ?>
            <tr><th scope="row">교습비</th>
                <?php foreach ($courses as $c): ?>
                    <td><?= $c['is_confirmed'] && $c['fee_text'] ? esc($c['fee_text']) : '<span class="pending">' . esc($feeNotice) . '</span>' ?></td>
                <?php endforeach ?>
            </tr>
        </tbody>
    </table></div>
    <?php endif ?>

    <?php if (setting($settings, 'refund_policy')): ?>
    <div class="box-soft refund">
        <h2 class="sub-title">교습비 반환 기준</h2>
        <p><?= nl2br(esc(setting($settings, 'refund_policy'))) ?></p>
    </div>
    <?php endif ?>

    <div class="box-soft">
        <h2 class="sub-title">모든 과정 공통 교육</h2>
        <p>댄스·보컬·화술·메이크업·연출·촬영·음향·편집·SNS 마케팅, 저작권, 광고·협찬 표시, 상품 설명 및 출연자 권리</p>
    </div>
</div></section>
<?= $this->endSection() ?>
