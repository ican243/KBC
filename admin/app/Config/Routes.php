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
});
