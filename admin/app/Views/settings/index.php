<?php
/**
 * 사이트 설정
 *
 * @var array $settings [key => row]
 * @var array $groups   [묶음 => [key => 형식]]
 * @var array $help     [key => 도움말]
 */
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="head-row">
    <h1>사이트 설정</h1>
    <a class="btn btn-line" href="<?= esc(public_url(), 'attr') ?>" target="_blank" rel="noopener">홈페이지에서 보기</a>
</div>
<p class="help">비워 둔 항목은 홈페이지에 표시하지 않거나 "준비 중"으로 표시됩니다. 운영 정보는 계약·등록이 확정된 뒤 입력하세요.</p>

<form method="post" action="<?= site_url('settings') ?>">
    <?= csrf_field() ?>
    <?php foreach ($groups as $group => $fields): ?>
    <div class="box">
        <h2><?= esc($group) ?></h2>
        <div class="form-grid">
            <?php foreach ($fields as $key => $type): ?>
                <?php if (! isset($settings[$key])) continue; ?>
                <?php $value = (string) old("settings.{$key}", $settings[$key]['value'] ?? ''); ?>
                <label<?= $type === 'textarea' ? ' class="full"' : '' ?>><b><?= esc($settings[$key]['label']) ?></b>
                    <?php if ($type === 'textarea'): ?>
                        <textarea name="settings[<?= esc($key, 'attr') ?>]" maxlength="2000"><?= esc($value) ?></textarea>
                    <?php else: ?>
                        <input type="<?= $type === 'email' ? 'email' : ($type === 'date' ? 'date' : 'text') ?>"
                               name="settings[<?= esc($key, 'attr') ?>]" value="<?= esc($value, 'attr') ?>">
                    <?php endif ?>
                    <?php if (isset($help[$key])): ?><span class="help"><?= esc($help[$key]) ?></span><?php endif ?>
                    <?= field_error($key) ?>
                </label>
            <?php endforeach ?>
        </div>
    </div>
    <?php endforeach ?>
    <button class="btn" type="submit">저장</button>
</form>
<?= $this->endSection() ?>
