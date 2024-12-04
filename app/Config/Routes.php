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
$routes->post('tournament', 'Home::get_tournament_info_page');
$routes->post('tournament/add-participant', 'Home::add_new_participant');
$routes->get('admin', 'Home::admin');
$routes->post('admin/tournament/upload', 'Home::upload_tournament');
$routes->get('admin/tournament/list', 'Home::get_tournaments');
$routes->get('admin/tournament/get-data-for-edit', 'Home::get_tournament_for_edit');
$routes->post('admin/tournament/update', 'Home::edit_tournament');
$routes->get('admin/tournament/participants', 'Home::get_tournament_participants');
$routes->get('admin/users/list', 'Home::get_users');



// $routes->get('admin/dashboard', 'Home::admin');
