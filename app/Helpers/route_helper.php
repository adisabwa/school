<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Registers default index routes for given controllers.
 *
 * @param RouteCollection $routes
 * @param array $controllers - associative (or numeric) array of routes => controllers
 */
function addDefaultRoutes(RouteCollection $routes, string $controller, string $table = '')
{
    $routes->add('/', [$controller, 'index']);
    $routes->add('get', [$controller, 'get']);
    $routes->add('reset_options', [$controller, 'resetOptions']);
    $routes->add('store', [$controller, 'store'], [ 'filter' => "api-validation:$table"]);
    $routes->add('store_many', [$controller, 'store'], [ 'filter' => "api-validation:$table,1"]);
    $routes->add('delete/(:any)', [$controller, 'delete/$1']);
    $routes->add('delete_many', [$controller, 'delete_many']);
    $routes->add('template', [$controller, 'template']);
    $routes->add('upload', [$controller, 'upload']);
    $routes->add('options', [$controller, 'options']);
    $routes->add('download_excel', [$controller, 'download_excel']);
}