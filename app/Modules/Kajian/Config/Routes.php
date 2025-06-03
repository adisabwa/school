<?php

use Modules\Kajian\Controllers\KajianController as Kajian;

//-------------------------------------Kajian -----------------------------------------------
$routes->group('kajian', [
    'filter' => 'api-auth',
], static function ($routes) {    
    $routes->add('/', [Kajian::class,'index']);
    $routes->add('get', [Kajian::class, 'get']);
    $routes->add('store', [Kajian::class, 'store'], [ 'filter' => 'api-validation:mu_kajian']);
    $routes->add('dashboard', [Kajian::class, 'dashboard']);    
    $routes->add('dashboard_count', [Kajian::class, 'dashboard_count']);    
    $routes->add('get_before', [Kajian::class, 'get_before']);

});

//-----------------------------------------------------------------------------------------------------
