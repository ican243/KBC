<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>관리자 로그인 | KBC아카데미</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>" sizes="32x32">
    <link rel="stylesheet" href="<?= asset('assets/css/auth.css') ?>">
</head>
<body>
<div class="box">
    <h1>KBC아카데미 관리자</h1>

    <?php if (session('error')): ?>
        <p class="error" role="alert"><?= esc(session('error')) ?></p>
    <?php endif ?>

    <form method="post" action="<?= site_url('login') ?>">
        <?= csrf_field() ?>

        <label for="username">아이디</label>
        <input type="text" id="username" name="username" value="<?= esc(old('username')) ?>"
               autocomplete="username" required autofocus>

        <label for="password">비밀번호</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>

        <button type="submit">로그인</button>
    </form>
</div>
</body>
</html>
