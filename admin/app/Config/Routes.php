<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// 로그인 (필터 없음) - 1단계 비밀번호, 2단계 OTP
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('login/otp', 'Auth::otp');
$routes->post('login/otp', 'Auth::verifyOtp');

// 첫 로그인 등 강제 단계에서도 쓸 수 있는 주소
$routes->post('logout', 'Auth::logout', ['filter' => 'adminauth:password,setup']);

// 비밀번호 변경 (임시 비밀번호로 로그인했을 때 가장 먼저)
$routes->group('', ['filter' => 'adminauth:password'], static function ($routes) {
    $routes->get('account/password', 'Account::password');
    $routes->post('account/password', 'Account::changePassword');
});

// 2단계 인증 설정 (비밀번호 변경 다음)
$routes->group('', ['filter' => 'adminauth:setup'], static function ($routes) {
    $routes->get('account/2fa', 'Account::twoFactor');
    $routes->post('account/2fa', 'Account::enableTwoFactor');
    $routes->get('account/2fa/codes', 'Account::recoveryCodes');
});

// 모든 관리자 (대표 관리자 + 담당자)
$routes->group('', ['filter' => 'adminauth'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');

    // 상담 문의 / 협력 제안 (같은 구조) - 보기·처리
    foreach (['consults' => 'Consults', 'partners' => 'Partners'] as $path => $controller) {
        $routes->get($path, "{$controller}::index");
        $routes->get("{$path}/(:num)", "{$controller}::show/$1");
        $routes->post("{$path}/(:num)", "{$controller}::update/$1");
    }

    $routes->get('notifications', 'Notifications::index');
    $routes->get('stats', 'Stats::index');

    // 홈페이지 콘텐츠 (같은 구조)
    foreach (['courses' => 'Courses', 'instructors' => 'Instructors', 'notices' => 'Notices', 'events' => 'Events', 'faqs' => 'Faqs'] as $path => $controller) {
        $routes->get($path, "{$controller}::index");
        $routes->get("{$path}/new", "{$controller}::create");
        $routes->post($path, "{$controller}::store");
        $routes->post("{$path}/sort", "{$controller}::sort");
        $routes->get("{$path}/(:num)/edit", "{$controller}::edit/$1");
        $routes->post("{$path}/(:num)", "{$controller}::update/$1");
        $routes->post("{$path}/(:num)/toggle", "{$controller}::toggle/$1");
        $routes->post("{$path}/(:num)/delete", "{$controller}::delete/$1");
    }

    $routes->get('account', 'Account::index');
});

// 대표 관리자만 (개인정보 대량 반출·삭제, 사이트 설정, 계정 관리)
$routes->group('', ['filter' => 'adminauth:owner'], static function ($routes) {
    foreach (['consults' => 'Consults', 'partners' => 'Partners'] as $path => $controller) {
        $routes->get("{$path}/export", "{$controller}::export");
        $routes->post("{$path}/(:num)/delete", "{$controller}::delete/$1");
    }

    $routes->get('settings', 'Settings::index');
    $routes->post('settings', 'Settings::save');

    $routes->get('admins', 'Admins::index');
    $routes->get('admins/new', 'Admins::create');
    $routes->post('admins', 'Admins::store');
    $routes->get('admins/issued', 'Admins::issued');
    $routes->get('admins/(:num)/edit', 'Admins::edit/$1');
    $routes->post('admins/(:num)', 'Admins::update/$1');
    $routes->post('admins/(:num)/status', 'Admins::toggleStatus/$1');
    $routes->post('admins/(:num)/password', 'Admins::resetPassword/$1');
    $routes->post('admins/(:num)/2fa', 'Admins::resetTwoFactor/$1');
});
