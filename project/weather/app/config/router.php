<?php declare(strict_types=1);

use Phalcon\Mvc\RouterInterface;

/** @var RouterInterface $router */
$router = $di->getRouter();

$router->add('/db/alias', [
    'namespace'  => '\Weather\Controller',
    'controller' => 'db',
    'action'    => 'alias'
]);

$router->add('/db/city', [
    'namespace'  => '\Weather\Controller',
    'controller' => 'db',
    'action'    => 'city'
]);

$router->add('/', [
    'namespace'  => '\Weather\Controller',
    'controller' => 'index',
    'action'    => 'help'
]);

$router->add('/city/{name}', [
    'namespace'  => '\Weather\Controller',
    'controller' => 'index',
    'action'    => 'city'
]);

$router->add('/alias/{alias}/name/{name}', [
    'namespace'  => '\Weather\Controller',
    'controller' => 'index',
    'action'    => 'alias'
]);

$router->add('/geo/{latitude}/{longitude}', [
    'namespace'  => '\Weather\Controller',
    'controller' => 'index',
    'action'    => 'geo'
]);

$router->handle($_SERVER['REQUEST_URI']);
