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
/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->post('/signup', 'ApiController::signup');
$routes->post('/signin', 'ApiController::signin');
$routes->get('/api/get_users', 'ApiController::get_users');
$routes->post('/api/delete_user/(:alphanum)', 'ApiController::delete_user/$1');
$routes->post('/api/update_user', 'ApiController::update_user');
$routes->get('/api/get_categories', 'ApiController::get_categories');
$routes->post('/api/add_category', 'ApiController::add_category');
$routes->post('/api/update_category', 'ApiController::update_category');
$routes->post('/api/delete_category/(:alphanum)', 'ApiController::delete_category/$1');
$routes->post('/api/add_product', 'ApiController::add_product');
$routes->post('/api/add_product_stock', 'ApiController::add_product_stock');
$routes->post('/api/update_product/(:alphanum)', 'ApiController::update_product/$1');
$routes->post('/api/delete_product/(:alphanum)', 'ApiController::delete_product/$1');
$routes->get('/api/get_all_products', 'ApiController::get_all_products');
$routes->post('/api/place_order', 'ApiController::place_order');
$routes->get('/api/get_products', 'ApiController::get_products');
$routes->post('/api/get_orders', 'ApiController::get_orders');
$routes->post('/api/delete_order/(:alphanum)', 'ApiController::delete_order/$1');
$routes->get('/api/export_products', 'ApiController::export_products');
$routes->get('/api/demo_export_products', 'ApiController::demo_export_products');
$routes->post('/api/import_products', 'ApiController::import_products');
$routes->post('/api/export_orders', 'ApiController::export_orders');
$routes->post('/api/get_customer_data/(:alphanum)', 'ApiController::get_customer_data/$1');
$routes->post('/api/get_searched_products', 'ApiController::get_searched_products');
$routes->post('/api/get_temp_orders', 'ApiController::get_temp_orders');
$routes->post('/api/confirm_temp_order/(:alphanum)', 'ApiController::confirm_temp_order/$1');
$routes->post('/api/delete_temp_order/(:alphanum)', 'ApiController::delete_temp_order/$1');
$routes->post('/api/edit_temp_order', 'ApiController::edit_temp_order');
$routes->get('/api/get_product_stocks_details/(:alphanum)', 'ApiController::get_product_stocks_details/$1');
$routes->get('/print_order/(:alphanum)', 'ApiController::print_order/$1');
$routes->get('/print_temp_order/(:alphanum)', 'ApiController::print_temp_order/$1');
$routes->post('/api/edit_reserve_order', 'ApiController::edit_reserve_order');



// admin panel routes
$routes->get('/cpanel-login', 'AdminController::index');
$routes->post('/check-login', 'AdminController::signin');
$routes->get('/signout', 'AdminController::logout');
$routes->get('/dashboard', 'AdminController::dashboard');
$routes->get('/customers', 'AdminController::customers');
$routes->post('/save_customer', 'AdminController::save_customer');
$routes->post('/check_url_title', 'AdminController::check_url_title');
$routes->get('/get_customers', 'AdminController::get_customers');
$routes->post('/delete_customer', 'AdminController::delete_customer');
$routes->post('/remove_customer_logo', 'AdminController::remove_customer_logo');
$routes->post('/get_customer_data', 'AdminController::get_customer_data');
$routes->get('/customer_plans/(:alphanum)', 'AdminController::customer_plans/$1');
$routes->get('/get_customer_plan/(:alphanum)', 'AdminController::get_customer_plan/$1');
$routes->post('/save_customer_plan/(:alphanum)', 'AdminController::save_customer_plan/$1');
$routes->post('/get_plan_data', 'AdminController::get_plan_data');
$routes->post('/delete_customer_plan', 'AdminController::delete_customer_plan');

$routes->get('/duplicate_image', 'AdminController::duplicate_image');



$routes->get('(.*)', 'Home::index');


// $routes->set404Override();
// $routes->set404Override(function () {
//     echo 'hi';
// });
// $routes->set404Override('App\Controllers\Home::index');
$routes->setAutoRoute(true);

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
