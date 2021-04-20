<?php

require 'vendor/autoload.php';
require 'src/services/Session.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use src\services\Session;

$container = $app->getContainer();

$container['logger'] = function ($c) {
    $logger = new \Monolog\logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['connection'] = function ($c) {
    $pdo = new PDO("mysql:host=" .$c['db']['host'] . ";dbname=" . $c['db']['dbname'].";charset=UTF8",
        $c['db']['user'], $c['db']['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    //$pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
    return $pdo;
};

$container['session'] = function ($c){
    return new Session();
};