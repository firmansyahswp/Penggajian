<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Login::index');
$routes->post('login', 'Login::login_action');
$routes->get('/admin/dashboard', 'Admin\Dashboard::index');
$routes->get('/pegawai/dashboard', 'pegawai\Dashboard::index');

