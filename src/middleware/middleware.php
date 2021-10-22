<?php

namespace src\middleware;


class MiddleWare{

    public function getToken($request, $response, $next){
        if($request->getHeader('Authorization')){
            $token = substr($request->getHeader('Authorization')[0], 7);
            $token = preg_match("/^[0-9a-z]{0,128}$/",$token)?$token:'error';
            if ($token != 'error'){
                $request = $request->withAttribute('token', $token);
                return $response = $next($request, $response);
            }
            return $response->withStatus(401)->withJson(['message'=>"Token fora do padrão"]);
            
        }else{
            return $response->withStatus(404);
        }
    }
}
