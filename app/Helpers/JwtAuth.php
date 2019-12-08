<?php

namespace App\Helpers;             //Agregado, el namespace de la clase

use Firebase\JWT\JWT;              //Agregado, para Json Web Token
use Illuminate\Support\Facades\DB; //Agregado, clase de Laravel
use App\User;                      //Agregado, tendremos acceso al modelo User

class JwtAuth{

    public $key;

    public function __construct(){
        $this->key = "Esto_es_una_clave_secreta";
    }

    public function signup($email, $password, $getToken = null){
        //Buscar si existe el usuario con sus credenciales
        $user = User::where("email", $email)
                    ->where("password", $password)
                    ->first();

        //Comprobar si son correctas
        $es_usuario_autenticado = false;
        if(is_object($user)){
            $es_usuario_autenticado = true;
        }

        //Generar el token con los datos del usuario identificado
        if($es_usuario_autenticado){
            $token = array(
                "sub"     => $user->id,
                "email"   => $user->email,
                "name"    => $user->name,
                "surname" => $user->surname,
                "iat"     => time(),
                "exp"     => time() * (7*24*60*60) //Token con duracion de una semana
            );
            $jwt = JWT::encode($token, $this->key, "HS256"); //Generamos el token con la libreria JWT
            $decode = JWT::decode($jwt, $this->key, ["HS256"]);
            
            //Devolver los datos decodificados o el token, en funcion de un parametro
            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data = $decode;
            }
        }else{
            $data = array(
                "status"  => "error",
                "message" => "No se pudo autenticar al usuario ingresado"
            );
        }
        
        return $data;
    }

}

?>