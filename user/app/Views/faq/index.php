<?php
/**
 * 자주 묻는 질문 - 분류별, 질문을 누르면 답변 펼침 (스크립트 없이 details 사용)
 *
 * @var array $groups [분류 => [행...]]
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-head"><div class="wrap">
    <span class="label label-dark">소식</span>
    <h1>자주 묻는 질문</h1>
    <?= view('partials/subnav', ['tabs' => [['공지·설명회', site_url('news')], ['자주 묻는 질문', site_url('faq')]], 'active' => '자주 묻는 질문']) ?>
</div></section>

<section class="section"><div class="wrap narrow">
    <?php if ($groups === []): ?>
        <div class="empty">자주 묻는 질문은 준비 중입니다. 궁금한 점은 <a href="<?= site_url('contact') ?>">상담 문의</a>로 남겨 주세요.</div>
    <?php endif ?>
    <?php foreach ($groups as $category => $items): ?>
        <h2 class="sub-title"><?= esc($category) ?></h2>
        <div class="faq-list">
            <?php foreach ($items as $f): ?>
            <details class="faq">
                <summary><?= esc($f['question']) ?></summary>
                <div><?= nl2br(esc($f['answer'])) ?></div>
            </details>
            <?php endforeach ?>
        </div>
    <?php endforeach ?>
    <p class="note">원하는 답을 찾지 못하셨다면 <a href="<?= site_url('contact') ?>">상담 문의</a>를 남겨 주세요.</p>
</div></section>
<?= $this->endSection() ?>
