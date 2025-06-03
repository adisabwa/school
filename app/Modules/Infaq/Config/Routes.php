<?php

use Modules\Infaq\Controllers\Infaq;
use Modules\Infaq\Controllers\Shadaqah\InfaqController as InfaqShadaqah;

//----------------------------------Section Tabungan-------------------------------------

// //----------------------------------Default Tabungan-------------------------------------
// $routes->group('infaq', static function ($routes) {
//     $routes->add('/', [Infaq::class,'get']);
//     $routes->add('get', [Infaq::class,'get']);
//     $routes->add('search', [Infaq::class,'search']);
//     $routes->add('store', [Infaq::class,'store'],[ 'filter' => 'api-validation:mu_infaq']);
//     $routes->add('template', [Infaq::class,'template']);
//     $routes->add('upload', [Infaq::class,'upload']);
//     $routes->add('preparation', [Infaq::class,'preparation']);
// });

//-------------------------------------Shadaqah Infaq -----------------------------------------------
$routes->group('infaq/shadaqah', [
    'filter' => 'api-auth',
], static function ($routes) {    
    $routes->add('/', [InfaqShadaqah::class,'index']);
    $routes->add('get', [InfaqShadaqah::class, 'get']);
    $routes->add('store', [InfaqShadaqah::class, 'store'], [ 'filter' => 'api-validation:mu_infaq_shadaqah']);
    $routes->add('dashboard', [InfaqShadaqah::class, 'dashboard']);    
    $routes->add('dashboard_count', [InfaqShadaqah::class, 'dashboard_count']);    
    $routes->add('get_before', [InfaqShadaqah::class, 'get_before']);
    $routes->add('delete/(:any)', [InfaqShadaqah::class, 'delete/$1']);

});

//-----------------------------------------------------------------------------------------------------
