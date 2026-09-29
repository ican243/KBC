<?php
/**
 * 공통 틀 (헤더 / 푸터 / 모바일 하단 상담 버튼)
 *
 * @var array  $settings
 * @var string $title
 * @var string $description
 */
$brand = setting($settings, 'brand_name', 'KBC아카데미');

// 현재 메뉴 표시 (첫 번째 주소 조각 기준)
$segment = service('uri')->getSegment(1);
$menuOf  = ['courses' => 'courses', 'instructors' => 'academy', 'about' => 'academy', 'career' => 'career',
            'news' => 'news', 'faq' => 'news', 'contact' => 'contact', 'partner' => 'contact'];
$current = $menuOf[$segment] ?? '';
$gnb     = [
    'academy' => ['아카데미', site_url('/') . '#about'],
    'courses' => ['교육과정', site_url('courses')],
    'career'  => ['진로 연계', site_url('/') . '#career'],
    'news'    => ['소식', site_url('news')],
    'contact' => ['상담 문의', site_url('contact')],
];
?>
<!DOCTYPE html>
<html lang="ko" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? $brand) ?></title>
    <meta name="description" content="<?= esc($description ?? '') ?>">
    <?php if (ENVIRONMENT !== 'production'): ?>
    <meta name="robots" content="noindex, nofollow">
    <?php endif ?>
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= esc($title ?? $brand) ?>">
    <meta property="og:description" content="<?= esc($description ?? '') ?>">
    <meta property="og:url" content="<?= esc(current_url()) ?>">
    <link rel="stylesheet" href="<?= asset('assets/fonts/pretendard/pretendard.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/site.css') ?>">
</head>
<body>
<a class="skip" href="#main">본문 바로가기</a>

<header class="site-header"><div class="wrap">
    <a class="logo" href="<?= site_url('/') ?>"><i></i><?= esc($brand) ?></a>
    <nav class="gnb" aria-label="주 메뉴">
        <?php foreach ($gnb as $key => [$label, $url]): ?>
            <a href="<?= $url ?>"<?= $current === $key ? ' class="on" aria-current="page"' : '' ?>><?= $label ?></a>
        <?php endforeach ?>
    </nav>
    <a class="btn btn-live btn-sm" href="<?= site_url('contact') ?>">사전 상담 신청</a>
    <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-nav" aria-label="메뉴 열기">
        <span></span><span></span><span></span>
    </button>
</div>
    <nav id="mobile-nav" class="mobile-nav" aria-label="전체 메뉴" hidden>
        <a href="<?= site_url('/') ?>#about">아카데미 소개</a>
        <a href="<?= site_url('instructors') ?>">강사진</a>
        <a href="<?= site_url('courses') ?>">교육과정</a>
        <a href="<?= site_url('/') ?>#career">진로 연계</a>
        <a href="<?= site_url('news') ?>">공지·설명회</a>
        <a href="<?= site_url('faq') ?>">자주 묻는 질문</a>
        <a href="<?= site_url('contact') ?>">상담 문의</a>
        <a href="<?= site_url('partner') ?>">교육 공간·채용 협력 제안</a>
    </nav>
</header>

<main id="main">
    <?= $this->renderSection('content') ?>
</main>

<footer class="site-footer"><div class="wrap">
    <div>
        <b><?= esc($brand) ?></b><br>
        <?= esc(setting($settings, 'status_notice', '')) ?>
    </div>
    <div>
        <?php if (setting($settings, 'operator_name')): ?>
            <?= esc(setting($settings, 'operator_name')) ?>
            <?php if (setting($settings, 'representative')): ?> · 대표 <?= esc(setting($settings, 'representative')) ?><?php endif ?>
            <?php if (setting($settings, 'business_no')): ?> · 사업자등록번호 <?= esc(setting($settings, 'business_no')) ?><?php endif ?><br>
            <?php if (setting($settings, 'academy_reg_no')): ?>학원 등록번호 <?= esc(setting($settings, 'academy_reg_no')) ?><br><?php endif ?>
            <?php if (setting($settings, 'address')): ?><?= esc(setting($settings, 'address')) ?><br><?php endif ?>
            <?php if (setting($settings, 'phone')): ?>대표 연락처 <?= esc(setting($settings, 'phone')) ?><br><?php endif ?>
        <?php else: ?>
            운영 주체·등록 정보: 준비 중<br>
        <?php endif ?>
        <a href="<?= site_url('privacy') ?>"><b>개인정보 처리방침</b></a> · © <?= esc($brand) ?>
    </div>
</div></footer>

<div class="sticky-cta"><a class="btn btn-live btn-block" href="<?= site_url('contact') ?>">사전 상담 신청</a></div>

<script src="<?= asset('assets/js/site.js') ?>"></script>
</body>
</html>
