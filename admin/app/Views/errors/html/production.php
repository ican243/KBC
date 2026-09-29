<?php
/**
 * 관리자 서버 오류 (운영 모드) - 원인은 서버 로그에만 남는다.
 */
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>일시적인 오류가 발생했습니다 | KBC아카데미 관리자</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>
<body>
<div class="box">
    <h1>일시적인 오류가 발생했습니다</h1>
    <p class="hint">잠시 후 다시 시도해 주세요.</p>
    <p><a href="<?= base_url() ?>">관리자 홈으로 →</a></p>
</div>
</body>
</html>
