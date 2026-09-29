<?php
/**
 * 사전 상담 신청 폼 (홈 하단, /contact 페이지 공용) → Consult::submit
 *
 * @var array  $courses
 * @var string $selectedCourse 미리 선택할 과정 id (없으면 '')
 * @var string $returnTo       오류 시 돌아갈 곳 home | contact
 */
$oldCourse = form_old('course_id', $selectedCourse ?? '');
$field     = static fn (string $key): string => form_error($key) ? ' has-error' : '';
?>
<form class="form-card" method="post" action="<?= site_url('consult') ?>" novalidate>
    <?= $this->include('partials/form_guard') ?>
    <input type="hidden" name="return_to" value="<?= esc($returnTo ?? 'home', 'attr') ?>">

    <div class="row2">
        <div class="field<?= $field('name') ?>">
            <label for="consult-name">이름 <span class="req">*</span></label>
            <input id="consult-name" name="name" required maxlength="50" autocomplete="name" value="<?= esc(form_old('name'), 'attr') ?>">
            <?php if (form_error('name')): ?><p class="field-error"><?= esc(form_error('name')) ?></p><?php endif ?>
        </div>
        <div class="field<?= $field('phone') ?>">
            <label for="consult-phone">연락처 <span class="req">*</span></label>
            <input id="consult-phone" name="phone" type="tel" required maxlength="20" placeholder="010-0000-0000" autocomplete="tel" value="<?= esc(form_old('phone'), 'attr') ?>">
            <?php if (form_error('phone')): ?><p class="field-error"><?= esc(form_error('phone')) ?></p><?php endif ?>
        </div>
    </div>

    <fieldset class="field<?= $field('course_id') ?>" style="border:0;padding:0;margin:0 0 16px">
        <legend class="legend">관심 과정</legend>
        <div class="pills">
            <label><input type="radio" name="course_id" value=""<?= $oldCourse === '' ? ' checked' : '' ?>><span>아직 모르겠어요</span></label>
            <?php foreach ($courses as $course): ?>
            <label><input type="radio" name="course_id" value="<?= esc($course['id'], 'attr') ?>"<?= $oldCourse === (string) $course['id'] ? ' checked' : '' ?>><span><?= esc($course['title']) ?></span></label>
            <?php endforeach ?>
        </div>
        <?php if (form_error('course_id')): ?><p class="field-error"><?= esc(form_error('course_id')) ?></p><?php endif ?>
    </fieldset>

    <div class="field<?= $field('contact_time') ?>">
        <label for="consult-time">희망 연락시간</label>
        <input id="consult-time" name="contact_time" maxlength="50" placeholder="예) 평일 오후 2시 이후" value="<?= esc(form_old('contact_time'), 'attr') ?>">
        <?php if (form_error('contact_time')): ?><p class="field-error"><?= esc(form_error('contact_time')) ?></p><?php endif ?>
    </div>
    <div class="field<?= $field('message') ?>">
        <label for="consult-message">문의내용</label>
        <textarea id="consult-message" name="message" maxlength="2000" placeholder="궁금한 점을 자유롭게 적어주세요"><?= esc(form_old('message')) ?></textarea>
        <?php if (form_error('message')): ?><p class="field-error"><?= esc(form_error('message')) ?></p><?php endif ?>
    </div>

    <label class="agree"><input type="checkbox" name="agree_privacy" value="1" required<?= form_old('agree_privacy') === '1' ? ' checked' : '' ?>> <span>[필수] 개인정보 수집·이용에 동의합니다.</span></label>
    <?= view('partials/consent_notice', ['noticeType' => 'consult']) ?>
    <?php if (form_error('agree_privacy')): ?><p class="field-error"><?= esc(form_error('agree_privacy')) ?></p><?php endif ?>
    <label class="agree"><input type="checkbox" name="agree_marketing" value="1"<?= form_old('agree_marketing') === '1' ? ' checked' : '' ?>> <span>[선택] 설명회·모집 안내 등 홍보성 연락을 받겠습니다.</span></label>
    <?= view('partials/consent_notice', ['noticeType' => 'marketing']) ?>

    <button class="btn btn-navy btn-block" style="margin-top:8px">상담 신청하기</button>
</form>
