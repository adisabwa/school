<?php

use Modules\Lowongan\Controllers\LowonganController;

// var_dump(LowonganController::class);exit;
helper('route');

//-------------------------------------Data Lowongan -----------------------------------------------
$routes->group('lowongan', static function ($routes) {
    addDefaultRoutes($routes, LowonganController::class, PREFIX_TABLE.'lowongan');
    $routes->add('generate-content-data', [ LowonganController::class, 'generateContentData' ]);
});

//-----------------------------------------------------------------------------------------------------