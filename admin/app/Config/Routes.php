<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// 로그인 (필터 없음) - 1단계 비밀번호, 2단계 OTP
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('login/otp', 'Auth::otp');
$routes->post('login/otp', 'Auth::verifyOtp');

// 로그인은 했지만 2단계 인증 설정 전에도 쓸 수 있는 주소
$routes->group('', ['filter' => 'adminauth:setup'], static function ($routes) {
    $routes->get('account/2fa', 'Account::twoFactor');
    $routes->post('account/2fa', 'Account::enableTwoFactor');
    $routes->get('account/2fa/codes', 'Account::recoveryCodes');
    $routes->post('logout', 'Auth::logout');
});

// 로그인 + 2단계 인증 완료
$routes->group('', ['filter' => 'adminauth'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');

    // 상담 문의 / 협력 제안 (같은 구조)
    foreach (['consults' => 'Consults', 'partners' => 'Partners'] as $path => $controller) {
        $routes->get($path, "{$controller}::index");
        $routes->get("{$path}/export", "{$controller}::export");
        $routes->get("{$path}/(:num)", "{$controller}::show/$1");
        $routes->post("{$path}/(:num)", "{$controller}::update/$1");
        $routes->post("{$path}/(:num)/delete", "{$controller}::delete/$1");
    }

    // 알림 발송 기록
    $routes->get('notifications', 'Notifications::index');

    // 내 계정
    $routes->get('account', 'Account::index');
    $routes->get('account/password', 'Account::password');
    $routes->post('account/password', 'Account::changePassword');
});
