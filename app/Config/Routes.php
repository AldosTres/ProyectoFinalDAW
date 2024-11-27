<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('index', 'Home::index');
$routes->get('login', 'Home::get_login_page');
$routes->post('login/check', 'Home::check_login');
$routes->get('register', 'Home::get_register_user_page');
$routes->post('user/register', 'Home::register_user');
$routes->post('tournament', 'Home::get_tournamente_info_page');
$routes->post('tournament/add-participant', 'Home::add_new_participant');
$routes->get('admin', 'Home::admin');
$routes->post('admin/tournament/upload', 'Home::upload_tournament');


/**
 CAMBIAR CUANDO SE PUEDA
 */

// $routes->post('participant/add', 'Home::get_add_participant_page');
// $routes->post('tournament/info', 'Home::get_tournamente_info_page');
// $routes->post('participant/add/submit', 'Home::add_new_participant');
// $routes->get('quick_view', 'Home::get_vista_rapida');
// $routes->get('admin/dashboard', 'Home::admin');
