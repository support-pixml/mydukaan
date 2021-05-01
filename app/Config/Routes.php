<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index'); 
$routes->setTranslateURIDashes(false);
// $routes->set404Override();
$routes->set404Override(function () {
    echo view('welcome_message');
});
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
// $routes->get('/api', 'ApiController::index');
$routes->post('/signup', 'ApiController::signup');
$routes->post('/signin', 'ApiController::signin');
$routes->get('/api/get_users', 'ApiController::get_users');
$routes->get('/api/get_categories', 'ApiController::get_categories');
$routes->post('/api/add_category', 'ApiController::add_category');
$routes->post('/api/update_category/(:alphanum)', 'ApiController::update_category/$1');
$routes->post('/api/add_product', 'ApiController::add_product');
$routes->get('/api/get_all_products', 'ApiController::get_all_products');
$routes->post('/api/place_order', 'ApiController::place_order');
$routes->get('/api/get_products', 'ApiController::get_products');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
