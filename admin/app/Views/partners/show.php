<?php
/**
 * 협력 제안 상세
 *
 * @var array $row
 */
use App\Models\PartnerInquiryModel;
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1>협력 제안 <?= esc($row['receipt_no']) ?> <?= status_badge($row['status']) ?></h1>

<div class="box">
    <h2>제안 정보</h2>
    <dl class="info">
        <dt>기관명</dt><dd><?= esc($row['org_name']) ?></dd>
        <dt>담당자</dt><dd><?= esc($row['contact_name']) ?></dd>
        <dt>연락처</dt><dd><a href="tel:<?= esc(preg_replace('/\D/', '', $row['phone']), 'attr') ?>"><?= esc($row['phone']) ?></a></dd>
        <dt>제휴 유형</dt><dd><?= esc(PartnerInquiryModel::TYPES[$row['partner_type']] ?? $row['partner_type']) ?></dd>
        <dt>제안 내용</dt><dd><?= nl2br(esc($row['message'])) ?></dd>
        <dt>접수일시</dt><dd><?= dt($row['created_at']) ?></dd>
        <dt>개인정보 동의</dt><dd><?= dt($row['agree_privacy_at']) ?></dd>
        <dt>유입 경로</dt><dd><?= esc($row['source'] ?? '-') ?></dd>
        <dt>접속 IP</dt><dd><?= esc($row['ip'] ?? '-') ?></dd>
        <dt>처리 담당</dt><dd><?= esc($row['admin_name'] ?? '-') ?></dd>
    </dl>
</div>

<?= $this->include('partials/inquiry_process') ?>
<?= $this->endSection() ?>
