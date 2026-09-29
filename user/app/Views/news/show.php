<?php
/**
 * 공지 상세 (일반 글자, 줄바꿈만 반영)
 *
 * @var array $notice
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-head"><div class="wrap narrow">
    <span class="label label-dark">공지</span>
    <h1><?= esc($notice['title']) ?></h1>
    <p><time datetime="<?= esc($notice['published_at'], 'attr') ?>"><?= esc(date('Y.m.d', strtotime($notice['published_at']))) ?></time></p>
</div></section>

<section class="section"><div class="wrap narrow">
    <div class="article"><?= nl2br(esc($notice['content'])) ?></div>
    <a class="btn btn-line" href="<?= site_url('news') ?>">목록으로</a>
</div></section>
<?= $this->endSection() ?>
