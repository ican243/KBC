<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1>비밀번호 변경</h1>
<?php if (! empty($forced)): ?>
    <p class="flash flash-err">임시 비밀번호로 로그인했습니다. 계속하려면 새 비밀번호로 바꿔 주세요. (현재 비밀번호 칸에는 임시 비밀번호를 입력)</p>
<?php endif ?>

<form class="box narrow-form" method="post" action="<?= site_url('account/password') ?>">
    <?= csrf_field() ?>
    <label>현재 비밀번호
        <input type="password" name="current" autocomplete="current-password" required>
    </label>
    <label>새 비밀번호 (10자 이상)
        <input type="password" name="new" autocomplete="new-password" minlength="10" maxlength="72" required>
    </label>
    <label>새 비밀번호 확인
        <input type="password" name="confirm" autocomplete="new-password" minlength="10" maxlength="72" required>
    </label>
    <p class="muted">이름·생일·아이디처럼 추측하기 쉬운 비밀번호는 피해 주세요.</p>
    <button class="btn" type="submit">변경</button>
    <?php if (empty($forced)): ?><a class="btn btn-line" href="<?= site_url('account') ?>">취소</a><?php endif ?>
</form>
<?= $this->endSection() ?>
