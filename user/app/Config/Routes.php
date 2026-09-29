<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// 교육 상담 신청
$routes->post('consult', 'Consult::submit');
$routes->get('consult/done', 'Consult::done');

// 기관 협력 제안
$routes->get('partner', 'Partner::index');
$routes->post('partner', 'Partner::submit');
$routes->get('partner/done', 'Partner::done');

// 안내 페이지
$routes->get('privacy', 'Pages::privacy');
