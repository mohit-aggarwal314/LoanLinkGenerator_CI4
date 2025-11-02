<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
$routes->get('/loans', 'Loans::index');
$routes->post('/loans/generate-link', 'Loans::generateLink');
$routes->get('/pay/(:any)', 'Loans::pay/$1'); // simulate payment page