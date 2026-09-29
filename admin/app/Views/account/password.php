<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1>비밀번호 변경</h1>

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
    <a class="btn btn-line" href="<?= site_url('account') ?>">취소</a>
</form>
<?= $this->endSection() ?>
