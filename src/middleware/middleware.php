<?php

namespace src\middleware;

use Exception;
use src\services\AuthService;

require 'vendor/autoload.php';

$rotinaAuth = function ($request, $response, $next){

    $session = $this->get('session');
    $token = substr($request->getHeader('Authorization')[0], 7);

    $provider = new AuthService($this->connection);
    $resourceOwner = $provider->authResourceOwner($token);
    $resourceOwner['id_user'] = $resourceOwner['status'] == 200?$resourceOwner['id_user']:null;

    if($resourceOwner['id_user'] != null){
        $session = $this->get('session');
        $session->set('user_id', $resourceOwner['id_user']);
        return $response = $next($request, $response);
    }else{
        return $response->withStatus(401)->withJson(["error"=>"Não foi possivel autenticar o usuário"]);
    }
};