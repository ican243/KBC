<?php
/**
 * 소식 - events(설명회), notices(공지) 테이블
 *
 * @var array $events
 * @var array $notices
 */
?>
<section id="news" class="section section-soft"><div class="wrap">
    <div class="sec-head reveal">
        <span class="label">소식</span>
        <h2>설명회·공지</h2>
    </div>
    <div class="news reveal">
        <div class="news-box">
            <h3>설명회 일정</h3>
            <?php if ($events === []): ?>
                <p class="none">설명회 일정은 확정 후 안내합니다.</p>
            <?php else: ?>
            <ul>
                <?php foreach ($events as $event): ?>
                <li>
                    <span><?= esc($event['title']) ?><?php if ($event['place']): ?> · <?= esc($event['place']) ?><?php endif ?></span>
                    <time datetime="<?= esc($event['event_at'], 'attr') ?>"><?= esc(date('n월 j일 H:i', strtotime($event['event_at']))) ?></time>
                </li>
                <?php endforeach ?>
            </ul>
            <?php endif ?>
        </div>
        <div class="news-box">
            <h3>공지</h3>
            <?php if ($notices === []): ?>
                <p class="none">등록된 공지가 없습니다.</p>
            <?php else: ?>
            <ul>
                <?php foreach ($notices as $notice): ?>
                <li>
                    <a href="<?= site_url('news/' . $notice['id']) ?>"><?= esc($notice['title']) ?></a>
                    <time datetime="<?= esc($notice['published_at'], 'attr') ?>"><?= esc(date('Y.m.d', strtotime($notice['published_at']))) ?></time>
                </li>
                <?php endforeach ?>
            </ul>
            <?php endif ?>
        </div>
    </div>
    <p class="more"><a href="<?= site_url('news') ?>">소식 전체 보기 →</a> · <a href="<?= site_url('faq') ?>">자주 묻는 질문 →</a></p>
</div></section>
