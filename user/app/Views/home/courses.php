<?php
/**
 * 과정 카드 (기획안 3장 3번) - courses 테이블
 * 확정 전(is_confirmed = 0)이면 "세부 시간표·교습비 확정 전" 표시
 *
 * @var array $courses
 */
?>
<section id="courses" class="section section-soft"><div class="wrap">
    <div class="sec-head reveal">
        <span class="label">교육과정</span>
        <h2>목표에 맞게 고르는 <?= count($courses) ?: '' ?>가지 과정</h2>
        <p>현재 커리큘럼 제안 단계이며, 세부 시간표와 교습비는 확정 후 안내합니다.</p>
    </div>

    <?php if ($courses === []): ?>
        <div class="empty">교육과정은 확정 후 안내합니다.</div>
    <?php else: ?>
    <div class="courses reveal">
        <?php foreach ($courses as $course): ?>
        <article class="course">
            <div class="top">
                <span class="dur"><?= esc($course['duration']) ?></span>
                <?php if (! $course['is_confirmed']): ?>
                    <span class="tag">세부 시간표·교습비 확정 전</span>
                <?php endif ?>
            </div>
            <h3><?= esc($course['title']) ?></h3>
            <p><?= esc($course['summary']) ?></p>
            <ul>
                <?php if ($course['target']): ?><li><b>대상</b><?= esc($course['target']) ?></li><?php endif ?>
                <?php if ($course['sessions_per_week']): ?><li><b>수업</b><?= esc($course['sessions_per_week']) ?></li><?php endif ?>
                <?php if ($course['outputs']): ?><li><b>산출물</b><?= esc($course['outputs']) ?></li><?php endif ?>
                <?php if ($course['is_confirmed'] && $course['fee_text']): ?><li><b>교습비</b><?= esc($course['fee_text']) ?></li><?php endif ?>
            </ul>
            <div class="card-actions">
                <a class="btn btn-line" href="<?= site_url('courses/' . $course['slug']) ?>">자세히 보기</a>
                <a class="btn btn-line" href="#contact" data-course="<?= esc($course['id'], 'attr') ?>">상담하기</a>
            </div>
        </article>
        <?php endforeach ?>
    </div>
    <p class="more"><a href="<?= site_url('courses') ?>">과정 한눈에 비교하기 →</a></p>
    <?php endif ?>
</div></section>
