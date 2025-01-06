<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('index', 'Home::index');
$routes->get('login', 'Home::get_login_page');
$routes->post('login/check', 'Home::check_login');
$routes->get('logout', 'Home::logout');

$routes->get('register', 'Home::get_register_user_page');
$routes->post('user/register', 'Home::register_user');

/**
 * *Seccion sobre nosotros
 */

$routes->get('about-us', 'Home::get_about_us_page');

$routes->get('tournament/(:num)', 'Home::get_tournament_info_page/$1');
$routes->post('tournament/add-participant', 'Home::add_new_participant');

$routes->get('login-admin', 'Home::get_login_admin_page');
$routes->post('login-admin/check', 'Home::check_admin_login');
$routes->get('logout-admin', 'Home::admin_logout');


// check_admin_login
$routes->get('admin', 'Home::admin');
$routes->post('admin/tournament/upload', 'Home::upload_tournament');
$routes->get('admin/tournament/list/(:num)/(:num)', 'Home::get_tournaments/$1/$2');
$routes->get('admin/tournament/get-data-for-edit', 'Home::get_tournament_for_edit');
$routes->post('admin/tournament/update', 'Home::edit_tournament');
$routes->get('admin/tournament/change-status/(:num)', 'Home::change_tournament_status/$1');
$routes->get('admin/tournament/participants', 'Home::get_tournament_participants');
$routes->get('admin/tournament/participants/(:num)/change-status', 'Home::change_participant_status/$1');
$routes->get('admin/tournament/scoring-criteria', 'Home::get_scoring_criteria');
$routes->post('admin/tournament/(:num)/round/(:num)/participant/(:num)/scores', 'Home::upload_participant_scores/$1/$2/$3');
/**
   tournaments/bracket/(:num): Define la URL que responderá a la solicitud, donde (:num) es un comodín que captura un número, representando el ID del torneo.
   TournamentsController::getBracket/$1: Especifica que el método getBracket del controlador TournamentsController manejará la solicitud. El $1 pasa el valor capturado en (:num) al método.
 */
$routes->get('admin/tournament/bracket/(:num)', 'Home::get_tournament_bracket/$1');
$routes->post('admin/tournament/bracket/(:num)/add-participant', 'Home::add_participant_to_bracket/$1');
$routes->get('admin/users/list/(:num)/(:num)', 'Home::get_users/$1/$2');
$routes->get('admin/users/roles', 'Home::get_user_rol_types');
$routes->post('admin/users/change-rol', 'Home::change_user_rol');
$routes->get('admin/users/change-status', 'Home::change_user_status');

$routes->post('admin/events/upload', 'Home::upload_event');
$routes->get('admin/events/list/(:num)/(:num)', 'Home::get_events_by_filter/$1/$2');
$routes->get('admin/events/(:num)/details', 'Home::get_event_details/$1');
$routes->post('admin/events/(:num)/update', 'Home::edit_event/$1');
$routes->get('admin/events/(:num)/toggle-active', 'Home::change_event_active_status/$1');

$routes->get('admin/settings/round-types', 'Home::get_round_types');
