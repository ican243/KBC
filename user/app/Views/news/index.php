<?php
/**
 * 소식: 다가오는 설명회 + 공지 목록
 *
 * @var array                    $notices
 * @var \CodeIgniter\Pager\Pager $pager
 * @var array                    $events
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-head"><div class="wrap">
    <span class="label label-dark">소식</span>
    <h1>설명회·공지</h1>
    <?= view('partials/subnav', ['tabs' => [['공지·설명회', site_url('news')], ['자주 묻는 질문', site_url('faq')]], 'active' => '공지·설명회']) ?>
</div></section>

<section class="section"><div class="wrap narrow">
    <h2 class="sub-title">다가오는 설명회</h2>
    <?php if ($events === []): ?>
        <div class="empty">설명회 일정은 확정 후 안내합니다.</div>
    <?php else: ?>
    <ul class="event-list">
        <?php foreach ($events as $e): ?>
        <li>
            <time datetime="<?= esc($e['event_at'], 'attr') ?>"><b><?= esc(date('n월 j일', strtotime($e['event_at']))) ?></b><?= esc(date('H:i', strtotime($e['event_at']))) ?></time>
            <div>
                <strong><?= esc($e['title']) ?></strong>
                <?php if ($e['place']): ?><span class="muted"> · <?= esc($e['place']) ?></span><?php endif ?>
                <?php if ($e['description']): ?><p><?= nl2br(esc($e['description'])) ?></p><?php endif ?>
            </div>
        </li>
        <?php endforeach ?>
    </ul>
    <?php endif ?>

    <h2 class="sub-title">공지</h2>
    <?php if ($notices === []): ?>
        <div class="empty">등록된 공지가 없습니다.</div>
    <?php else: ?>
    <ul class="notice-list">
        <?php foreach ($notices as $n): ?>
        <li><a href="<?= site_url('news/' . $n['id']) ?>"><span><?= esc($n['title']) ?></span><time datetime="<?= esc($n['published_at'], 'attr') ?>"><?= esc(date('Y.m.d', strtotime($n['published_at']))) ?></time></a></li>
        <?php endforeach ?>
    </ul>
    <?= $pager->links() ?>
    <?php endif ?>
</div></section>
<?= $this->endSection() ?>
