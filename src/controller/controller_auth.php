<?php

namespace src\controller;

require 'vendor/autoload.php';
require 'src/services/AuthService.php';

use Exception;
use PDOException;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use src\services\AuthService;

$getAuthLogin = function (Request $request, Response $response, array $args){
    $login = $request->getParsedBodyParam('username');
    $pass = $request->getParsedBodyParam('password');

    $provider = new AuthService($this->connection);
    $result = $provider->getAuthTokenAccess($login, md5($pass));
    $status = $result["status"];
    unset($result["status"]);

    return $response->withStatus($status)->withJson($result);
};

$getAuthResourceOwner = function (Request $request, Response $response, array $args){
    $token = $request->getParsedBodyParam('access_token');

    $provider = new AuthService($this->connection);

    $result = $provider->authResourceOwner($token);
    $status = $result["status"];
    unset($result["status"]);
    return $response->withStatus($status)->withJson($result);

};

$closeAuth = function (Request $request, Response $response, array $args){
    $token = $request->getParsedBodyParam('access_token');
    $id_user = $request->getParsedBodyParam('user_id');
    
    $key = $token == null?$id_user:$token;

    $provider = new AuthService($this->connection);

    if($key == null){
        return $response->withStatus(400)->withJson(["error"=>"Não foi passado nenhum parametro para encerrar a autenticação"]);
    }else {
        $result = $provider->closeAuth($key);
        $status = $result["status"];
        unset($result["status"]);
        return $response->withStatus($status)->withJson($result);
    }
};


$getTest = function (Request $request, Response $response, array $args){

    $session = $this->get('session');
    $id = $session->get('user_id');
    $today = date("Y-m-d H:i:s");

    $string = "<h2>Você esta Autenticado: $id </h2></br>$today</b>";

    return $response->getBody()->write($string);

};