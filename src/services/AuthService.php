<?php

namespace src\services;

use Exception;
use PDO;
use PDOException;

class AuthService{

    public function __construct($connection){
        $this->db = $connection;

        try{
            $stmt = $this->db->prepare("SELECT
                name_table_user,
                name_colum_user,
                name_colum_pass,
                name_colum_login
                FROM rl_auth_user
                order by rl_auth_id DESC limit 1

            ");
            $stmt->execute();
            $data = $stmt->fetch();
            $this->name_table = $data['name_table_user'];
            $this->name_colum_user_id = $data['name_colum_user'];
            $this->name_colum_pass = $data['name_colum_pass'];
            $this->name_colum_login = $data['name_colum_login'];
        }catch(Exception $e){
            return 'error';
        }
    }
    private function compareSenha($entrada, $referencia){

    }

    private function getTokenUserIfHaveTokenValido($id_user){
        try{
            $stmt = $this->db->prepare("SELECT
                auth_token,
                TIME_TO_SEC(TIMEDIFF(auth_timeout, now())) as time_expiry
                from auth_table
                where auth_timeout > NOW() and
                auth_flag_ativo = 1 and
                auth_id_user = :id_user
                order by auth_pk_id DESC limit 1
            ");
            $stmt->execute([
                ":id_user"=>$id_user
            ]);
            $result = $stmt->fetch();
            return $result;
        }catch(PDOException $e){
            print_r($e);
            return 'error';
        }
    }

    private function getIdUserByToken($token){
        try{
            $stmt = $this->db->prepare("SELECT
                auth_id_user
                from auth_table
                where auth_timeout > NOW() and
                auth_flag_ativo = 1 and
                auth_token = :token
                order by auth_pk_id DESC limit 1
            ");
            $stmt->execute([
                ":token"=>$token
            ]);
            $result = $stmt->fetch();
            return $result == null?null:$result['auth_id_user'];
        }catch(PDOException $e){
            print_r($e);
            return null;
        }

    }

    private function createNewToken($user_id, $time_experided = 36000){
        $this->time_experided = 36000;
        $key = hash('sha512', md5(uniqid(rand(),true)));
        try{

            $string = "INSERT INTO `auth_table`
            (`auth_id_user`, `auth_token`, `auth_time_create`, `auth_time_expered`, `auth_timeout`) 
            VALUES (:id_user, :key, NOW(), :timer, DATE_ADD(NOW(), INTERVAL :timer SECOND))
            ";

            $stmt = $this->db->prepare($string);
            $stmt->execute([
                ":timer"=>$time_experided,
                ":id_user"=>$user_id,
                ":key"=>$key    
            ]);
            return $key;
        }catch(PDOException $e){
            print_r($e);
            return null;
        }

    }

    //-----------------------------------------------------------------------------------------------------

    public function getAuthTokenAccess($login, $pass){


        try{
            $string = "SELECT 
                $this->name_colum_pass as pass_user,
                $this->name_colum_user_id as id_user
                from $this->name_table 
                where $this->name_colum_login = :loginn
            ";
            $stmt = $this->db->prepare($string);
            $stmt->execute([":loginn"=>$login]);
            $result = $stmt->fetch();

            if($result == null){
                return [
                    "status" => 400,
                    "error"=> 'Usuario não encontrado'
                ];
            }

            if(strcmp($result['pass_user'], $pass)==0){

                $token = $this->getTokenUserIfHaveTokenValido($result["id_user"]);
                if($token != null){
                    return [
                        "status"=> 200,
                        "access_token" => $token['auth_token'],
                        "time_expiry" => round($token['time_expiry'])
                    ];
                }

                $token = $this->createNewToken($result["id_user"]);
                if($token == 'error'){
                    return [
                        "status" => 400,
                        "error"=> 'Não foi possivel gerar o Access Token'
                    ];
                }else{
                    return [
                        "status"=> 200,
                        "access_token" => $token,
                        "time_expiry" => $this->time_experided
                    ];
                }
            }else {
                return [
                    "status" => 400,
                    "error"=> 'Senha incorreta'
                ];
            }

        }catch(PDOException $e){
            return [
                "status" => 400,
                "error"=> 'Não foi possivel realizar a busca do usuario'
            ];
        }
        
    }

    public function authResourceOwner($token){
        $id = $this->getIdUserByToken($token);
        if($id == null){
            return [
                "status" => 401,
                "error"=> 'O token não foi autenticado'
            ];
        } else{
            return [
                "status" => 200,
                "id_user"=> $id
            ];
        }
    }

    public function revalidateToken($token, $key){//implementar com o remote_addr

    }

    public function closeAuth($key){
        try{
            $stmt = $this->db->prepare("UPDATE auth_table
                SET auth_flag_ativo = 0
                where auth_token = :keyy or
                auth_id_user = :keyy
            ");
            $stmt->execute([
                ":keyy"=>$key
            ]);
            return [
                "status" => 200,
                "messege"=> "Autenticação do usuário encerrada com sucesso"
            ];
        }catch(PDOException $e){
            print_r($e);
            return [
                "status" => 400,
                "messege"=> "Não foi possivel encerrar a Autenticação do usuário"
            ];
        }
    }
}