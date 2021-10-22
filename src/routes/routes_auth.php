<?php

namespace src\routes;


require 'vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use src\middleware\MiddleWare;
use src\controller\ControllerAuth;

$app->group('/auth', function() use ($app){
    $app->post('/access', ControllerAuth::class.':getAuthLogin');
    $app->map(['GET', 'POST'],'/close', ControllerAuth::class.':closeAuth');

    $app->group('/validate', function() use ($app){    
        $app->get('/access', ControllerAuth::class.':getResourceOwner');
        $app->get('/resource/{scope}', ControllerAuth::class.':validateResourceOwner');
    })->add(MiddleWare::class.':getToken');
});
