<?php
/**
 * 404 - 없는 페이지 (KBC 디자인)
 * 개발 모드에서만 원인 메시지를 함께 보여준다.
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
    <title>페이지를 찾을 수 없습니다 | KBC아카데미</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>" sizes="32x32">
    <link rel="stylesheet" href="<?= asset('assets/fonts/pretendard/pretendard.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/site.css') ?>">
</head>
<body>
<main class="error-page">
    <a class="logo" href="<?= site_url('/') ?>"><i></i>KBC아카데미</a>
    <p class="error-code">404</p>
    <h1>페이지를 찾을 수 없습니다</h1>
    <p>주소가 바뀌었거나 삭제된 페이지입니다. 아래에서 원하는 곳으로 이동해 주세요.</p>
    <div class="card-actions">
        <a class="btn btn-live" href="<?= site_url('/') ?>">홈으로</a>
        <a class="btn btn-white" href="<?= site_url('courses') ?>">교육과정</a>
        <a class="btn btn-white" href="<?= site_url('contact') ?>">상담 문의</a>
    </div>
    <?php if (ENVIRONMENT !== 'production' && ! empty($message) && $message !== '(null)'): ?>
        <p class="error-debug">개발 모드 안내: <?= esc($message) ?></p>
    <?php endif ?>
</main>
</body>
</html>
