<?php
/**
 * 관리자 404
 *
 * @var string $message
 */
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>페이지를 찾을 수 없습니다 | KBC아카데미 관리자</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>" sizes="32x32">
    <link rel="stylesheet" href="<?= asset('assets/css/auth.css') ?>">
</head>
<body>
<div class="box">
    <h1>페이지를 찾을 수 없습니다</h1>
    <p class="hint">주소가 바뀌었거나 삭제된 항목입니다.</p>
    <?php if (ENVIRONMENT !== 'production' && ! empty($message) && $message !== '(null)'): ?>
        <p class="hint">개발 모드 안내: <?= esc($message) ?></p>
    <?php endif ?>
    <p><a href="<?= site_url('/') ?>">관리자 홈으로 →</a></p>
</div>
</body>
</html>
