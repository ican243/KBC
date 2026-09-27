<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>관리자 로그인 | KBC아카데미</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
               background: #f3f5f9; font-family: -apple-system, "Malgun Gothic", sans-serif; color: #1c2533; }
        .box { width: 100%; max-width: 360px; margin: 16px; padding: 32px 28px; background: #fff;
               border-radius: 10px; box-shadow: 0 2px 12px rgba(20, 35, 70, .08); }
        h1 { margin: 0 0 24px; font-size: 20px; color: #14264a; }
        label { display: block; margin: 14px 0 6px; font-size: 14px; }
        input { width: 100%; padding: 11px 12px; font-size: 15px; border: 1px solid #cfd6e2; border-radius: 6px; }
        input:focus { outline: 2px solid #2b4a8b; border-color: transparent; }
        button { width: 100%; margin-top: 22px; padding: 12px; font-size: 15px; color: #fff;
                 background: #14264a; border: 0; border-radius: 6px; cursor: pointer; }
        .error { margin: 0 0 8px; padding: 10px 12px; font-size: 14px; color: #a52a2a;
                 background: #fbeeee; border-radius: 6px; }
    </style>
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
