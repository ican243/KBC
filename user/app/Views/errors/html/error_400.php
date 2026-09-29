<?php
/**
 * 400 - 잘못된 요청 (허용되지 않은 문자가 든 주소 등)
 *
 * @var string $message
 */
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>잘못된 요청 | KBC아카데미</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>" sizes="32x32">
    <link rel="stylesheet" href="<?= base_url('assets/css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/site.css') ?>">
</head>
<body>
<main class="error-page">
    <a class="logo" href="<?= base_url() ?>"><i></i>KBC아카데미</a>
    <p class="error-code">400</p>
    <h1>잘못된 요청입니다</h1>
    <p>주소를 다시 확인해 주세요.</p>
    <div class="card-actions">
        <a class="btn btn-live" href="<?= base_url() ?>">홈으로</a>
    </div>
    <?php if (ENVIRONMENT !== 'production' && ! empty($message) && $message !== '(null)'): ?>
        <p class="error-debug">개발 모드 안내: <?= esc($message) ?></p>
    <?php endif ?>
</main>
</body>
</html>
