<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// 로그인 (필터 없음)
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');

// 로그인 필요
$routes->group('', ['filter' => 'adminauth'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->post('logout', 'Auth::logout');

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
});
