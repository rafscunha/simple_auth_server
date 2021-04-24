<?php

namespace src\routes;


require 'vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/auth/login', $getAuthLogin);

$app->post('/auth/resource', $getAuthResourceOwner);

$app->post('/auth/close', $closeAuth);

$app->get('/teste', $getTest)->add($rotinaAuth);

$app->get('/lol[/{a}[/{b}]]', function(Request $request, Response $response, array $args){
    $a = $args['a'];
    $b = $args['b'];
    $c = $request->get("c");
    $d = $request->get("d");

    print_r("primeiro: $a \n segundo: $b");
    return $response;
});
