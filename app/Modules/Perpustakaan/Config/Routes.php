<?php

use Modules\Perpustakaan\Controllers\Admin\BukuController;
use Modules\Perpustakaan\Controllers\Admin\PeminjamanController;

helper('route');

//-------------------------------------Perpustakaan -----------------------------------------------
$routes->group('perpustakaan', [
    'filter' => 'api-auth:perpustakaan.all',
], static function ($routes) {    
    
    $routes->group('admin', static function ($routes) {
        //-------------------------------------Data Buku -----------------------------------------------
        $routes->group('buku', static function ($routes) {
            addDefaultRoutes($routes, BukuController::class, PREFIX_TABLE.'lib_buku');
        });
        
        //-------------------------------------Data Peminjaman -----------------------------------------------
        $routes->group('peminjaman', static function ($routes) {
            addDefaultRoutes($routes, PeminjamanController::class, PREFIX_TABLE.'lib_peminjaman');
        });
    });
});
//-----------------------------------------------------------------------------------------------------