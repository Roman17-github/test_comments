<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Comments::index');
$routes->get('/comments', 'Comments::index');
$routes->get('comments/feed', 'Comments::feed');

$routes->post('comments', 'Comments::create');
$routes->post('comments/delete/(:num)', 'Comments::delete/$1');