<?php

require 'vendor/autoload.php';
require_once 'config.php';

use src\services\AuthService;


$container = new \Slim\Container($configs);

$app = new \Slim\App($container);
/*
$container = $app->getContainer();

$container['connection'] = function ($c) {
    $pdo = new PDO("mysql:host=" .$c['db']['host'] . ";dbname=" . $c['db']['dbname'].";charset=UTF8",
        $c['db']['user'], $c['db']['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    //$pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
    return $pdo;
};
*/

require_once 'container.php';

require_once 'src/controller/controller_auth.php';
require_once 'src/middleware/middleware.php';

require_once 'src/routes/routes_auth.php';

$app->run();