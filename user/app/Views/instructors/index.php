<?php
/**
 * 강사진 - 실명·사진·프로필·경력은 게시 동의한 강사만 (InstructorModel)
 *
 * @var array $instructors
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-head"><div class="wrap">
    <span class="label label-dark">아카데미</span>
    <h1>분야별 현장 강사진</h1>
    <p>강사 명단과 경력은 협업 확정 후 증빙을 확인해 공개합니다.</p>
    <?= view('partials/subnav', ['tabs' => [['소개', site_url('/') . '#about'], ['강사진', site_url('instructors')]], 'active' => '강사진']) ?>
</div></section>

<section class="section"><div class="wrap">
    <?php if ($instructors === []): ?>
        <div class="empty">강사진은 확정 후 안내합니다.</div>
    <?php else: ?>
    <div class="crew-list">
        <?php foreach ($instructors as $item): ?>
        <article class="crew-card">
            <div class="crew-photo">
                <?php if ($item['photo_path']): ?>
                    <img src="<?= esc(base_url($item['photo_path']), 'attr') ?>" alt="<?= esc($item['name'] ?? $item['field'], 'attr') ?>">
                <?php else: ?>
                    <span><?= esc(mb_substr($item['field'], 0, 1)) ?></span>
                <?php endif ?>
            </div>
            <div>
                <h2><?= esc($item['field']) ?><?php if ($item['name']): ?> <small><?= esc($item['name']) ?></small><?php endif ?></h2>
                <?php if ($item['description']): ?><p class="crew-desc"><?= esc($item['description']) ?></p><?php endif ?>
                <?php if ($item['criteria']): ?><p class="crew-meta"><b>강사 기준</b> <?= esc($item['criteria']) ?></p><?php endif ?>
                <?php if ($item['profile']): ?><p class="crew-meta"><?= nl2br(esc($item['profile'])) ?></p><?php endif ?>
                <?php if ($item['career']): ?><p class="crew-meta"><b>경력</b><br><?= nl2br(esc($item['career'])) ?></p><?php endif ?>
            </div>
        </article>
        <?php endforeach ?>
    </div>
    <?php endif ?>
</div></section>
<?= $this->endSection() ?>
