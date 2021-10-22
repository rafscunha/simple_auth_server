<?php
namespace src\controller;

use Exception;
use PDOException;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use src\services\AuthService;

class ControllerAuth{
    public function __construct($connection){
        $this->connection = $connection;
        $this->auth_connection = new AuthService($this->connection);
    }

    public function getAuthLogin(Request $request, Response $response, array $args){
        $login = $request->getParsedBodyParam('username');
        $pass = $request->getParsedBodyParam('password');
    
        $result = $this->auth_connection->getAuthTokenAccess($login, $pass);
        $status = $result["status"];
        unset($result["status"]);
    
        return $response->withStatus($status)->withJson($result);
    }

    public function closeAuth(Request $request, Response $response, array $args){
        
        if($request->getHeader('Authorization')){
            $key = substr($request->getHeader('Authorization')[0], 7);
        }else{
            return $response->withStatus(400)->withJson(["error"=>"Não foi passado nenhum parametro para encerrar a autenticação"]);
        }
        $result = $this->auth_connection->closeAuth($key);
            $status = $result["status"];
            unset($result["status"]);
            return $response->withStatus($status)->withJson($result);
    }

    public function getResourceOwner(Request $request, Response $response, array $args){
        //$token = $request->getParsedBodyParam('access_token');
        $token = $request->getAttribute('token');
    
        $result = $this->auth_connection->authResourceOwner($token);
        $status = $result["status"];
        unset($result["status"]);
        return $response->withStatus($status)->withJson($result);
    }

    public function validateResourceOwner(Request $request, Response $response, array $args){
        //$token = $request->getParsedBodyParam('access_token');
        $token = $request->getAttribute('token');
        $scope = $args['scope'];
    
        $result = $this->auth_connection->authResourceOwner($token);

        if ($result["status"] == 200){
            $array = explode(';', $result['scope']);
            if (in_array($scope, $array)){
                return $response->withStatus(200)->withJson(["scope"=>True]);
            }else{
                return $response->withStatus(200)->withJson(["scope"=>False]);
            }
        }else{
            return $response->withStatus($result["status"]);
        }

    }
}