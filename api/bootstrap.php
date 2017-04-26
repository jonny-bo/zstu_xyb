<?php

date_default_timezone_set('UTC');

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

define('RUNTIME_ENV', 'API');
define('ROOT_DIR', __DIR__ . DIRECTORY_SEPARATOR . '..');

$debug = true;
if (API_ENV == 'prod') {
    ErrorHandler::register(0);
    ExceptionHandler::register(false);
    $debug = false;
}

$parameters = include __DIR__.'/config/paramaters.php';
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $parameters['host'] = 'https://'.$_SERVER['HTTP_HOST'];
} else {
    $parameters['host'] = 'http://'.$_SERVER['HTTP_HOST'];
}

$biz = new \Codeages\Biz\Framework\Context\Biz(array(
    'debug'              => $debug,
    'db.options'         => array(
        'dbname'   => $parameters['database_name'],
        'user'     => $parameters['database_user'],
        'password' => $parameters['database_password'],
        'host'     => $parameters['database_host'],
        'port'     => $parameters['database_port'],
        'driver'   => $parameters['database_driver'],
        'charset'  => 'UTF8'
    ),
    'cache_directory'    => ROOT_DIR . '/var/cache',
    'tmp_directory'      => ROOT_DIR . '/var/tmp',
    'log_directory'      => ROOT_DIR . '/var/logs',
    'plugin.directory'   => ROOT_DIR . '/plugins',
    'plugin.config_file' => ROOT_DIR . '/app/config/plugin_installed.php',
    'upload.public_directory' => ROOT_DIR . '/web/files',
    'upload.public_url_path'  => '/files',
    'upload.private_directory' => ROOT_DIR .'/var/data/private_files',
));

$biz['migration.directories'][] = dirname(__DIR__) . '/migrations';
$biz['user.password_encoder'] = function () {
    return new \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder('sha256');
};
$biz->register(new \Codeages\Biz\Framework\Provider\DoctrineServiceProvider());
$biz->register(new \Codeages\Biz\RateLimiter\RateLimiterServiceProvider());
$biz['subscribers'] = new \ArrayObject();
$biz->boot();
