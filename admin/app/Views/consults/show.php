<?php
/**
 * 상담 문의 상세
 *
 * @var array $row
 */
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1>상담 문의 <?= esc($row['receipt_no']) ?> <?= status_badge($row['status']) ?></h1>

<div class="box">
    <h2>신청 정보</h2>
    <dl class="info">
        <dt>이름</dt><dd><?= esc($row['name']) ?></dd>
        <dt>연락처</dt><dd><a href="tel:<?= esc(preg_replace('/\D/', '', $row['phone']), 'attr') ?>"><?= esc($row['phone']) ?></a></dd>
        <dt>관심 과정</dt><dd><?= esc($row['course_title'] ?? '미정') ?></dd>
        <dt>희망 연락시간</dt><dd><?= esc($row['contact_time'] ?? '-') ?></dd>
        <dt>문의내용</dt><dd><?= $row['message'] !== null ? nl2br(esc($row['message'])) : '-' ?></dd>
        <dt>접수일시</dt><dd><?= dt($row['created_at']) ?></dd>
        <dt>개인정보 동의</dt><dd><?= dt($row['agree_privacy_at']) ?></dd>
        <dt>홍보성 연락</dt><dd><?= $row['agree_marketing'] ? '동의 (' . dt($row['agree_marketing_at']) . ')' : '동의 안 함' ?></dd>
        <dt>유입 경로</dt><dd><?= esc(\App\Libraries\SourceLabel::label($row['source'])) ?> <small class="muted"><?= esc($row['source'] ?? '') ?></small></dd>
        <dt>접속 IP</dt><dd><?= esc($row['ip'] ?? '-') ?></dd>
        <dt>담당자</dt><dd><?= esc($row['admin_name'] ?? '-') ?></dd>
    </dl>
</div>

<?= $this->include('partials/inquiry_process') ?>
<?= $this->endSection() ?>
