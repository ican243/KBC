<?php
/**
 * 접수 완료 (상담 신청 / 협력 제안 공용)
 *
 * @var string $receiptNo
 */
$isPartner = str_starts_with($receiptNo, 'P');
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-head"><div class="wrap">
    <span class="label label-dark"><?= $isPartner ? '협력 제안' : '상담 신청' ?></span>
    <h1><?= $isPartner ? '협력 제안이 접수되었습니다' : '상담 신청이 접수되었습니다' ?></h1>
</div></section>

<section class="section"><div class="wrap narrow">
    <div class="done-card">
        <div class="done-ico" aria-hidden="true">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
        </div>
        <p class="done-label">접수번호</p>
        <p class="done-no"><?= esc($receiptNo) ?></p>
        <p class="done-text">
            <?= $isPartner
                ? '검토 후 남겨주신 연락처로 담당자가 연락드립니다.'
                : '남겨주신 연락처로 상담 담당자가 연락드립니다.' ?><br>
            문의하실 때 접수번호를 알려주시면 빠르게 확인할 수 있습니다.
        </p>
        <a class="btn btn-navy" href="<?= site_url('/') ?>">홈으로</a>
    </div>
</div></section>
<?= $this->endSection() ?>
