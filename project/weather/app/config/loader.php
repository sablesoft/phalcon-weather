<?php declare(strict_types=1);

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->componentsDir,
        $config->application->modelsDir
    ]
)->registerNamespaces(
    [
        'Weather\Controller' => APP_PATH . '/controllers/',
        'Weather\Component'  => APP_PATH . '/components/',
        'Weather\Model'      => APP_PATH . '/models/',
    ]
)->register();
