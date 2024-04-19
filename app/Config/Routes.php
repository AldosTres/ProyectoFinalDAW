<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('index', 'Home::index');
$routes->get('get_login_page', 'Home::get_login_page');
$routes->post('check_login', 'Home::check_login');
$routes->post('register_user', 'Home::register_user');
$routes->get('get_register_user_page', 'Home::get_register_user_page');
$routes->get('get_upload_tournament_page', 'Home::get_upload_tournament_page');
$routes->post('upload_tournament', 'Home::upload_tournament');
$routes->get('get_add_participant_page', 'Home::get_add_participant_page');
