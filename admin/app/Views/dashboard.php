<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>대시보드 | KBC아카데미 관리자</title>
    <style>
        body { margin: 0; background: #f3f5f9; font-family: -apple-system, "Malgun Gothic", sans-serif; color: #1c2533; }
        header { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px;
                 background: #14264a; color: #fff; }
        header strong { font-size: 16px; }
        header form { margin: 0; }
        header button { padding: 7px 14px; font-size: 13px; color: #14264a; background: #fff;
                        border: 0; border-radius: 5px; cursor: pointer; }
        main { max-width: 960px; margin: 32px auto; padding: 0 16px; }
    </style>
</head>
<body>
<header>
    <strong>KBC아카데미 관리자</strong>
    <form method="post" action="<?= site_url('logout') ?>">
        <?= csrf_field() ?>
        <button type="submit">로그아웃</button>
    </form>
</header>
<main>
    <h2><?= esc($adminName) ?>님, 환영합니다.</h2>
    <p>관리 메뉴는 순차적으로 추가됩니다.</p>
</main>
</body>
</html>
