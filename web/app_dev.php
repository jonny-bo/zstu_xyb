<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#checking-symfony-application-configuration-and-setup
// for more information
#umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
#if (isset($_SERVER['HTTP_CLIENT_IP'])
#    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
 #   || !(in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe8080::1', '::1', '112.74.36.71')) || php_sapi_name() === 'cli-server')
#) {
#    if (!file_exists(dirname(__DIR__) . '/var/tmp/dev.lock')) {
#        header('HTTP/1.0 403 Forbidden');
#        exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.'); 
#    }
#}

if ((strpos($_SERVER['REQUEST_URI'], '/api') === 0) || (strpos($_SERVER['REQUEST_URI'], '/app_dev.php/api') === 0)) {
    define('API_ENV', 'dev');
    include __DIR__.'/../api/index.php';
    exit();
}

/**
 * @var Composer\Autoload\ClassLoader $loader
 */
$loader = require __DIR__.'/../app/autoload.php';
Debug::enable();

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
