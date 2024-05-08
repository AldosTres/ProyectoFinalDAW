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
$routes->post('get_add_participant_page', 'Home::get_add_participant_page');
$routes->post('get_tournamente_info_page', 'Home::get_tournamente_info_page');
$routes->post('add_new_participant', 'Home::add_new_participant');
