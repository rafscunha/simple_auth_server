<?php

require 'vendor/autoload.php';
require_once 'config.php';

use src\services\AuthService;


$container = new \Slim\Container($configs);

$app = new \Slim\App($container);

require_once 'container.php';

require_once 'src/routes/routes_auth.php';

$app->run();