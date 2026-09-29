<?php
/**
 * 관리자 공통 틀 (상단 메뉴 / 알림 메시지)
 *
 * @var string $title
 */
$current = service('uri')->getSegment(1);
$menu    = [
    ''              => '대시보드',
    'consults'      => '상담 문의',
    'partners'      => '협력 제안',
    'courses'       => '교육과정',
    'notices'       => '공지',
    'events'        => '설명회',
    'faqs'          => 'FAQ',
    'settings'      => '사이트 설정',
    'notifications' => '알림 기록',
    'account'       => '내 계정',
];
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= esc($title ?? '관리자') ?> | KBC아카데미 관리자</title>
    <link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>">
</head>
<body>
<header class="top"><div class="in">
    <strong>KBC아카데미 관리자</strong>
    <nav>
        <?php foreach ($menu as $seg => $label): ?>
            <a href="<?= site_url($seg) ?>"<?= $current === $seg ? ' class="on"' : '' ?>><?= $label ?></a>
        <?php endforeach ?>
    </nav>
    <form method="post" action="<?= site_url('logout') ?>">
        <?= csrf_field() ?>
        <?= esc(session('admin_name')) ?>
        <button type="submit">로그아웃</button>
    </form>
</div></header>

<main>
    <?php if (session('message')): ?><p class="flash flash-ok"><?= esc(session('message')) ?></p><?php endif ?>
    <?php if (session('error')): ?><p class="flash flash-err"><?= esc(session('error')) ?></p><?php endif ?>

    <?= $this->renderSection('content') ?>
</main>
<script src="<?= asset('assets/js/admin.js') ?>"></script>
</body>
</html>
