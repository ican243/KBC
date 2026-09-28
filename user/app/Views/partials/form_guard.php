<?php
/**
 * 문의 폼 공통 숨김 값
 * - website : 사람에게 보이지 않는 칸 (봇이 채우면 차단)
 * - form_ts : 폼을 연 시각 (너무 빨리 제출하면 차단)
 * - source  : 유입 경로 (site.js 가 채움)
 */
use App\Libraries\SpamGuard;
?>
<?= csrf_field() ?>
<div class="hp" aria-hidden="true">
    <label>웹사이트 <input type="text" name="<?= SpamGuard::HONEYPOT_FIELD ?>" tabindex="-1" autocomplete="off"></label>
</div>
<input type="hidden" name="<?= SpamGuard::TIME_FIELD ?>" value="<?= time() ?>">
<input type="hidden" name="source" value="">
<?php if (form_error('form')): ?>
    <p class="form-alert" role="alert"><?= esc(form_error('form')) ?></p>
<?php endif ?>
