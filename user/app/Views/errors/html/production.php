<?php
/**
 * 서버 오류 (운영 모드) - 원인은 화면에 보이지 않고 서버 로그에만 남는다.
 * DB 등이 멈춘 상황에서도 보여야 하므로 DB 를 쓰지 않는다.
 */
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>일시적인 오류 | KBC아카데미</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>" sizes="32x32">
    <link rel="stylesheet" href="<?= base_url('assets/css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/site.css') ?>">
</head>
<body>
<main class="error-page">
    <a class="logo" href="<?= base_url() ?>"><i></i>KBC아카데미</a>
    <p class="error-code">!</p>
    <h1>일시적인 오류가 발생했습니다</h1>
    <p>잠시 후 다시 시도해 주세요. 문제가 계속되면 상담 문의로 알려 주세요.</p>
    <div class="card-actions">
        <a class="btn btn-live" href="<?= base_url() ?>">홈으로</a>
        <a class="btn btn-white" href="<?= base_url('contact') ?>">상담 문의</a>
    </div>
</main>
</body>
</html>
