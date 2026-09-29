<?php
/**
 * 기관 협력 제안 → Partner::submit
 *
 * @var array $types ['space' => '교육장', ...]
 */
$oldType = form_old('partner_type');
$field   = static fn (string $key): string => form_error($key) ? ' has-error' : '';
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-head"><div class="wrap">
    <span class="label label-dark">협력 제안</span>
    <h1>교육 공간·강사·채용 협력 제안</h1>
    <p>KBC아카데미와 함께할 교육장, 강사, 방송 운영업체의 제안을 받습니다.</p>
</div></section>

<section class="section"><div class="wrap narrow">
    <form class="form-card" method="post" action="<?= site_url('partner') ?>" novalidate>
        <?= $this->include('partials/form_guard') ?>

        <fieldset class="field<?= $field('partner_type') ?>" style="border:0;padding:0;margin:0 0 16px">
            <legend class="legend">제휴 유형 <span class="req">*</span></legend>
            <div class="pills">
                <?php foreach ($types as $value => $label): ?>
                <label><input type="radio" name="partner_type" value="<?= esc($value, 'attr') ?>" required<?= $oldType === $value ? ' checked' : '' ?>><span><?= esc($label) ?></span></label>
                <?php endforeach ?>
            </div>
            <?php if (form_error('partner_type')): ?><p class="field-error"><?= esc(form_error('partner_type')) ?></p><?php endif ?>
        </fieldset>

        <div class="field<?= $field('org_name') ?>">
            <label for="partner-org">기관명 <span class="req">*</span></label>
            <input id="partner-org" name="org_name" required maxlength="100" autocomplete="organization" value="<?= esc(form_old('org_name'), 'attr') ?>">
            <?php if (form_error('org_name')): ?><p class="field-error"><?= esc(form_error('org_name')) ?></p><?php endif ?>
        </div>
        <div class="row2">
            <div class="field<?= $field('contact_name') ?>">
                <label for="partner-name">담당자 <span class="req">*</span></label>
                <input id="partner-name" name="contact_name" required maxlength="50" autocomplete="name" value="<?= esc(form_old('contact_name'), 'attr') ?>">
                <?php if (form_error('contact_name')): ?><p class="field-error"><?= esc(form_error('contact_name')) ?></p><?php endif ?>
            </div>
            <div class="field<?= $field('phone') ?>">
                <label for="partner-phone">연락처 <span class="req">*</span></label>
                <input id="partner-phone" name="phone" type="tel" required maxlength="20" placeholder="010-0000-0000" autocomplete="tel" value="<?= esc(form_old('phone'), 'attr') ?>">
                <?php if (form_error('phone')): ?><p class="field-error"><?= esc(form_error('phone')) ?></p><?php endif ?>
            </div>
        </div>
        <div class="field<?= $field('message') ?>">
            <label for="partner-message">제안 내용 <span class="req">*</span></label>
            <textarea id="partner-message" name="message" required maxlength="2000" placeholder="제안하시는 공간, 협력 방식, 일정 등을 적어주세요"><?= esc(form_old('message')) ?></textarea>
            <?php if (form_error('message')): ?><p class="field-error"><?= esc(form_error('message')) ?></p><?php endif ?>
        </div>

        <label class="agree"><input type="checkbox" name="agree_privacy" value="1" required<?= form_old('agree_privacy') === '1' ? ' checked' : '' ?>> <span>[필수] 개인정보 수집·이용에 동의합니다.</span></label>
        <?= view('partials/consent_notice', ['noticeType' => 'partner']) ?>
        <?php if (form_error('agree_privacy')): ?><p class="field-error"><?= esc(form_error('agree_privacy')) ?></p><?php endif ?>

        <button class="btn btn-navy btn-block" style="margin-top:8px">협력 제안 보내기</button>
        <p class="note">첨부파일 제출은 추후 지원 예정입니다. 자료가 있으면 제안 내용에 링크를 남겨 주세요.</p>
    </form>
</div></section>
<?= $this->endSection() ?>
