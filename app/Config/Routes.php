<?php
namespace Config;

$routes = Services::routes();
/*
* --------------------------------------------------------------------
* Router Setup
* --------------------------------------------------------------------
*/
$routes->setDefaultController('Vue');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(false);

/*
* --------------------------------------------------------------------
* Route Definitions
* --------------------------------------------------------------------
*/
// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->setDefaultNamespace('App\Controllers');

$routes->get('/', 'Vue');
$routes->get('/p', 'Vue::index');
$routes->get('/p/(:any)', 'Vue::index');

//----------------------Authenthication-----------------------------//
$routes->group('auth', static function ($routes) {
    $routes->add('/', 'Auth::login');
    // $routes->add('login', 'Auth::login');
    $routes->add('g_login', 'Auth::g_login');
    // $routes->add('login/(:any)', 'Auth::login/$1');
    $routes->add('logout', 'Auth::logout');
    $routes->add('user', 'Auth::user');
    $routes->add('forbidden', 'Auth::forbidden');
    $routes->add('unauthorized', 'Auth::unauthorized');
    $routes->add('change_role', 'Auth::change_role');
    $routes->add('reset', 'Auth::reset');
});
//-----------------------------------------------------------------------//

//----------------------------File Controller------------------------------
$routes->add('get-files', 'FileController::getFile');

//-----------------------------------------------------------------------//


//-----------------------------Pengaturan Kolom Data------------------------
$routes->group('kolom', static function ($routes) {
    $routes->add('preparation', 'Kolom::preparation');
});
//-----------------------------------------------------------------------//

