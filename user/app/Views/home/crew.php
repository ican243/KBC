<?php
/**
 * 강사진 - instructors 테이블
 * 실명·사진은 게시 동의 후에만 표시 (InstructorModel 에서 처리)
 *
 * @var array $instructors
 */
?>
<section class="section"><div class="wrap">
    <div class="sec-head reveal">
        <span class="label">강사진</span>
        <h2>분야별 현장 강사진</h2>
        <p>강사 명단과 경력은 협업 확정 후 증빙을 확인해 공개합니다.</p>
    </div>

    <?php if ($instructors === []): ?>
        <div class="empty">강사진은 확정 후 안내합니다.</div>
    <?php else: ?>
    <div class="crew reveal">
        <?php foreach ($instructors as $item): ?>
        <div class="crew-item">
            <span class="av">
                <?php if ($item['photo_path']): ?>
                    <img src="<?= esc(base_url($item['photo_path']), 'attr') ?>" alt="<?= esc($item['name'] ?? $item['field'], 'attr') ?>">
                <?php else: ?>
                    <?= esc(mb_substr($item['field'], 0, 1)) ?>
                <?php endif ?>
            </span>
            <b><?= esc($item['field']) ?></b>
            <?php if ($item['name']): ?><span class="name"><?= esc($item['name']) ?></span><?php endif ?>
            <span><?= esc($item['description']) ?></span>
        </div>
        <?php endforeach ?>
    </div>
    <p class="more"><a href="<?= site_url('instructors') ?>">강사진 자세히 보기 →</a></p>
    <?php endif ?>
</div></section>
