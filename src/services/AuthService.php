<?php

namespace src\services;

use Exception;
use PDO;
use PDOException;

class AuthService{

    public function __construct($connection){
        $this->logger = $connection->logger;
        $this->db = $connection->connection;
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
            
            return null;
        }

    }

    private function createNewToken($user_id, $time_experided = 36000){
        $this->time_experided = $time_experided;
        $key = hash('sha512', md5(uniqid(rand(),true)));
        try{

            $string = "INSERT INTO `auth_table`
            (`auth_id_user`, `auth_token`, `auth_timeout`) 
            VALUES (:id_user, :key,  DATE_ADD(NOW(), INTERVAL :timer SECOND))
            ";

            $stmt = $this->db->prepare($string);
            $stmt->execute([
                ":id_user"=>$user_id,
                ":key"=>$key,
                ":timer"=>$time_experided
            ]);
            return $key;
        }catch(PDOException $e){
            
            return null;
        }

    }

    private function checkUsername($login){
        try{
            $string = "SELECT 
                senha as pass_user,
                pk_id as id_user
                from usuario 
                where login = :loginn
            ";
            $stmt = $this->db->prepare($string);
            $stmt->execute([":loginn"=>$login]);
            $result = $stmt->fetch();
            return $result;
        }catch(PDOException $e){
            
            return null;
        }
    }

    private function getNumTentativasLogin($login){
        try{
            
            $stmt = $this->db->prepare("SELECT
                if(flag_ativo = 0, 0, n_tentativas) as num
                from black_list_login
                where login = :loginn
                order by pk_id DESC limit 1
            ");
            $stmt->execute([":loginn"=>$login]);
            $result = $stmt->fetch();
            return $result!=null?$result['num']:0;
        }catch(PDOException $e){
            
            return null;
        }
    }

    private function equacaoBanLogin($n){
        if($n < 4){
            return 0;
        }else{
            //return round(pow((0.4211*exp(1)),(0.8198*($n-3))));
            return round(0.4211*exp(0.8198*($n-3)));
        }
    }

    private function getScopes($id){
        try{
            
            $stmt = $this->db->prepare("SELECT
                scope
                FROM scope
                WHERE fk_login = :id and
                flag_ativo = 1
                limit 1
            ");
            $stmt->execute([":id"=>$id]);
            $result = $stmt->fetch()['scope'];
            return $result;
        }catch(PDOException $e){
            
            return null;
        }
    }

    private function timeExpired($token){
        try{
            
            $stmt = $this->db->prepare("SELECT
                auth_timeout as time_out
                FROM auth_table
                WHERE auth_token = :token
            ");
            $stmt->bindParam(":token", $token);
            $stmt->execute();
            $result = $stmt->fetch()['time_out'];
            return $result;
        }catch(PDOException $e){
            
            return null;
        }
    }

    //-----------------------------------------------------------------------------------------------------
//******************************************************************************************** */
    public function getAuthTokenAccess($login, $pass){

        try{
            $result = $this->checkUsername($login);

            if($result == null){
                return [
                    "status" => 400,
                    "error"=> 'Usuario não encontrado'
                ];
            }
            $black_list = $this->checkBlackListLogin($login);
            if($black_list == 0){//Verifica se esta na black list de login

                if(strcmp($result['pass_user'], $pass)==0){

                    $this->closeAuth($result["id_user"]);
    
                    $token = $this->createNewToken($result["id_user"]);
                    if($token == 'error'){
                        return [
                            "status" => 400,
                            "error"=> 'Não foi possivel gerar o Access Token'
                        ];
                    }else{
                        $this->removeBlackListLogin($login);
                        $timeOut = $this->timeExpired($token);
                        $scope = $this->getScopes($result['id_user']);
                        return [
                            "status"=> 200,
                            "access_token" => $token,
                            "time_expiry" => $timeOut,
                            "scopes"=>$scope
                        ];
                    }
                }else {
                    $this->inputBlackListLogin($login);
                    return [
                        "status" => 400,
                        "error"=> 'Senha incorreta'
                    ];
                }
            }else{
                return [
                    "status"=>200,
                    "error"=> "É necessario espera $black_list min para realizar o login"
                ];
            }            

        }catch(PDOException $e){
            
            return [
                "status" => 400,
                "error"=> 'Não foi possivel realizar a busca do usuario'
            ];
        }
        
    }
//******************************************************************************************** */
    public function authResourceOwner($token){
        $id = $this->getIdUserByToken($token);
        if($id == null){
            return [
                "status" => 401,
                "error"=> 'O token não foi autenticado'
            ];
        } else{
            $scope = $this->getScopes($id);
            return [
                "status" => 200,
                "id_user"=> $id,
                "scope"=> $scope
            ];
        }
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
            
            return [
                "status" => 400,
                "messege"=> "Não foi possivel encerrar a Autenticação do usuário"
            ];
        }
    }

    public function inputBlackListLogin($login){
        $n_tentativas = $this->getNumTentativasLogin($login);
        try{
            $stmt = $this->db->prepare("INSERT INTO `black_list_login`
            (`login`, `n_tentativas`, `prox_login`) 
            VALUES 
            (:loginn, :n_tent,  DATE_ADD(NOW(), INTERVAL :timer MINUTE))
            ");

            $stmt->execute([
                ":loginn"=>$login,
                ":n_tent"=>$n_tentativas+1,
                ":timer"=>$this->equacaoBanLogin($n_tentativas+1)
                ]);
                return 0;
        }catch(PDOException $e){
            
            return 0;
        }
    }
    public function checkBlackListLogin($login){
        try{
            $stmt = $this->db->prepare("SELECT
                if((prox_login > NOW()) and (flag_ativo = 1), ROUND(TIME_TO_SEC(TIMEDIFF(prox_login, NOW()))/60), 0) as num
                from black_list_login
                where login = :loginn
                order by pk_id DESC limit 1
            ");
            $stmt->execute([":loginn"=>$login]);
            $result = $stmt->fetch();
            return $result!=null?$result['num']:null;
        }catch(PDOException $e){
            
            return null;
        }
    }
    public function removeBlackListLogin($login){
        try{
            $stmt = $this->db->prepare("UPDATE black_list_login
                SET flag_ativo = 0
                where login = :loginn
                order by pk_id DESC limit 1
            ");
            $stmt->execute([":loginn"=>$login]);
            return 0;
        }catch(PDOException $e){
            
            return null;
        }
    }
}