<?php

namespace App\Helpers; //Namespace de la clase

use App;
use Firebase\JWT\JWT;

class JwtAuth
{
    public $key;

    public function __construct(){
        $this->key = "Esto_es_una_clave_secreta";
    }

    public function signup($email, $password, $getToken = null){
        /* Verificar usuario */
        $user = App\User::table('users')
                        ->where("email", "=", $email)
                        ->where("password", "=", $password)
                        ->get();

        if(is_object($user)):
            $token = array(
                "sub"     => $user->id,
                "name"    => $user->name,
                "surname" => $user->surname,                
                "email"   => $user->email,                
                "iat"     => time(),
                "exp"     => time() * (7*24*60*60) //Token con duracion de una semana
            );
            $jwt = JWT::encode($token, $this->key, "HS256"); //Generamos el token con la libreria JWT
            $decode = JWT::decode($jwt, $this->key, ["HS256"]);
            
            if(is_null($getToken)):
                $data = $jwt;
            else:
                $data = $decode;
            endif;
        else:
            $data = array(
                "status"  => "error",
                "message" => "No se pudo autenticar al usuario ingresado"
            );
        endif;
        
        return $data;
    }
}
