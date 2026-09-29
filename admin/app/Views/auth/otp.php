<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>2단계 인증 | KBC아카데미</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>" sizes="32x32">
    <link rel="stylesheet" href="<?= asset('assets/css/auth.css') ?>">
</head>
<body>
<div class="box">
    <h1>2단계 인증</h1>
    <p class="hint">휴대폰 OTP 앱(Google Authenticator 등)에 표시된 <b>6자리 숫자</b>를 입력해 주세요.</p>

    <?php if (session('error')): ?>
        <p class="error" role="alert"><?= esc(session('error')) ?></p>
    <?php endif ?>

    <form method="post" action="<?= site_url('login/otp') ?>">
        <?= csrf_field() ?>
        <label for="code">인증번호</label>
        <input type="text" id="code" name="code" inputmode="numeric" autocomplete="one-time-code"
               maxlength="12" placeholder="123456" required autofocus>
        <button type="submit">확인</button>
    </form>

    <span class="sub">휴대폰을 사용할 수 없으면 복구 코드(예: ABCD-EFGH)를 입력하세요.<br>
        <a href="<?= site_url('login') ?>">처음으로</a></span>
</div>
</body>
</html>
