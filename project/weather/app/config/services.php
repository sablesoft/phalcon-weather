<?php
/** @noinspection PhpUndefinedFieldInspection */
declare(strict_types=1);

use Phalcon\Config;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Url as UrlResolver;
use Phalcon\Cache\Adapter\Redis;
use Weather\Component\Cache;
use Weather\Component\Service;
use Weather\Component\SerializerFactory;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/** @var Config $config */
$config = $di->get('config');

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () use (&$config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () use (&$config) {
    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);
    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'path' => $config->application->cacheDir,
                'separator' => '_'
            ]);

            return $volt;
        },
        '.phtml' => PhpEngine::class

    ]);

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () use (&$config) {
    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    return new $class($params);
});

// redis adapter:
$di->setShared('redis', function() use (&$config) {
    $serializerFactory = new SerializerFactory();
    $options = [
        'defaultSerializer' => 'Json',
        'lifetime'   => $config->redis->lifetime,
        "host"       => $config->redis->host,
        "port"       => $config->redis->port,
        "auth"       => $config->redis->auth,
        "persistent" => $config->redis->persistent,
        "index"      => $config->redis->index
    ];

    return new Redis($serializerFactory, $options);
});

// custom service cache adapter:
$di->setShared('cache', function() use (&$config) {
    return new Cache($this->get('redis'));
});

// open weather api service:
$di->setShared('openWeather', function() use (&$config) {
    return \SableSoft\OpenWeather\Service::getInstance(
        $config->openWeather->apiKey,
        $config->openWeather->options->toArray()
    );
});

// main service:
$di->setShared('service', function() use (&$config) {
    return new Service(
        $this->get('openWeather'),
        $this->get('cache'),
        $config->service->toArray()
    );
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});
