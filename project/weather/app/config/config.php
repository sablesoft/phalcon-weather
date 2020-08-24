<?php declare(strict_types=1);

/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config([
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => getenv('MYSQL_HOST'),
        'username'    => getenv('MYSQL_USER'),
        'password'    => getenv('MYSQL_PASSWORD'),
        'dbname'      => getenv('MYSQL_DATABASE'),
        'charset'     => 'utf8',
    ],
    'redis' => [
        'lifetime'   => (int) getenv('REDIS_LIFETIME'),
        "host"       => getenv('REDIS_HOST'),
        "port"       => (int) getenv('REDIS_PORT'),
        "auth"       => getenv('REDIS_AUTH'),
        "persistent" => (bool) getenv('REDIS_PERSISTENT'),
        "index"      => getenv('REDIS_INDEX')
    ],
    'openWeather' => [
        'apiKey'   => getenv('OPENWEATHER_KEY'),
        'options' => [
            'timeout' => 5,
            'connect_timeout' => 5
        ]
    ],
    'service'   => [
        'fetchLimit' => getenv('SERVICE_FETCH_LIMIT') ?? 50
    ],
    'application' => [
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'componentsDir'  => APP_PATH . '/components/',
        'modelsDir'      => APP_PATH . '/models/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'cacheDir'       => BASE_PATH . '/cache/',
        'baseUri'        => '/',
    ]
]);
