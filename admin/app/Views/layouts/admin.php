<?php
/**
 * 관리자 공통 틀 (왼쪽 메뉴 / 상단바 / 알림 메시지 / 확인 창)
 *
 * @var string $title
 */
$current = service('uri')->getSegment(1);
$owner   = is_owner();

// 묶음 => [주소 => [이름, 아이콘, 대표 관리자 전용]]
$menu = [
    '' => [
        ''              => ['대시보드', 'dashboard'],
    ],
    '문의' => [
        'consults'      => ['상담 문의', 'chat'],
        'partners'      => ['협력 제안', 'briefcase'],
        'notifications' => ['알림 기록', 'mail'],
        'stats'         => ['통계', 'chart'],
    ],
    '홈페이지' => [
        'courses'       => ['교육과정', 'book'],
        'instructors'   => ['강사진', 'users'],
        'notices'       => ['공지', 'megaphone'],
        'events'        => ['설명회', 'calendar'],
        'faqs'          => ['FAQ', 'help'],
        'settings'      => ['사이트 설정', 'sliders', true],
    ],
    '계정' => [
        'admins'        => ['관리자 계정', 'shield', true],
        'account'       => ['내 계정', 'user'],
    ],
];
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= esc($title ?? '관리자') ?> | KBC아카데미 관리자</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>" sizes="32x32">
    <link rel="icon" href="<?= base_url('favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>">
</head>
<body>
<div class="shell">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <a href="<?= site_url('/') ?>">KBC아카데미 관리자</a>
            <button type="button" class="sidebar-close" data-sidebar="close" aria-label="메뉴 닫기"><?= icon('close') ?></button>
        </div>
        <nav class="side-nav">
            <?php foreach ($menu as $group => $items): ?>
                <?php if ($group !== ''): ?><div class="side-group"><?= $group ?></div><?php endif ?>
                <?php foreach ($items as $seg => $item): ?>
                    <?php [$label, $ic] = $item;
                    if (! empty($item[2]) && ! $owner) {
                        continue;
                    } ?>
                    <a href="<?= site_url($seg) ?>" class="side-link<?= $current === $seg ? ' on' : '' ?>"<?= $current === $seg ? ' aria-current="page"' : '' ?>>
                        <?= icon($ic) ?><span><?= $label ?></span>
                    </a>
                <?php endforeach ?>
            <?php endforeach ?>
        </nav>
        <a class="side-link side-foot" href="<?= esc(public_url(), 'attr') ?>" target="_blank" rel="noopener"><?= icon('external') ?><span>홈페이지 보기</span></a>
    </aside>
    <div class="side-backdrop" data-sidebar="close"></div>

    <div class="main">
        <header class="topbar">
            <button type="button" class="sidebar-toggle" data-sidebar="open" aria-label="메뉴 열기" aria-controls="sidebar"><?= icon('menu') ?></button>
            <form class="topbar-user" method="post" action="<?= site_url('logout') ?>">
                <?= csrf_field() ?>
                <span><?= esc(session('admin_name')) ?> <small><?= $owner ? '대표 관리자' : '담당자' ?></small></span>
                <button class="btn btn-line btn-sm" type="submit">로그아웃</button>
            </form>
        </header>

        <main class="content">
            <?php if (session('message')): ?><p class="flash flash-ok"><?= esc(session('message')) ?></p><?php endif ?>
            <?php if (session('error')): ?><p class="flash flash-err"><?= esc(session('error')) ?></p><?php endif ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>

<!-- 공통 확인 창: data-confirm 이 있는 폼·버튼은 브라우저 기본 확인창 대신 이 창을 쓴다 (admin.js) -->
<dialog class="confirm" id="confirmDialog" aria-labelledby="confirmText">
    <div class="confirm-box">
        <p id="confirmText"></p>
        <div class="confirm-foot">
            <button type="button" class="btn btn-line" data-confirm-cancel>취소</button>
            <button type="button" class="btn btn-danger" data-confirm-ok>확인</button>
        </div>
    </div>
</dialog>

<script src="<?= asset('assets/js/admin.js') ?>"></script>
</body>
</html>
