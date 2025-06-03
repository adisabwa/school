<?php

use Modules\Persyarikatan\Controllers\PersyarikatanController as Persyarikatan;

//-------------------------------------Persyarikatan -----------------------------------------------
$routes->group('persyarikatan', [
    'filter' => 'api-auth',
], static function ($routes) {    
    $routes->add('/', [Persyarikatan::class,'index']);
    $routes->add('get', [Persyarikatan::class, 'get']);
    $routes->add('store', [Persyarikatan::class, 'store'], [ 'filter' => 'api-validation:mu_kegiatan_persyarikatan']);
    $routes->add('dashboard', [Persyarikatan::class, 'dashboard']);    
    $routes->add('dashboard_count', [Persyarikatan::class, 'dashboard_count']);    
    $routes->add('get_before', [Persyarikatan::class, 'get_before']);
    $routes->add('delete/(:any)', [Persyarikatan::class, 'delete/$1']);

});

//-----------------------------------------------------------------------------------------------------
