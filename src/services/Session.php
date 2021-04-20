<?php

namespace src\services;

use Exception;
use PDO;
use PDOException;

class Session{

    public function __construct(){
        session_start();
    }

    public function set($name, $value){
        $_SESSION[$name] = $value;
    }
    public function get($name){
        return $_SESSION[$name];
    }

    public function __destruct(){
        
    }

}