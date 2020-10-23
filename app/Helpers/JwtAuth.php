<?php

namespace App\Helpers; //Namespace de la clase

use App;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;

class JwtAuth
{
    public $key;

    public function __construct(){
        $this->key = "Esto_es_una_clave_secreta";
    }

    public function signup($email, $password, $getToken = false){       
        $user = App\User::where("email", "=", $email)                        
                        ->first();
        // error_log(json_encode($user));

        if(empty($user) || !Hash::check($password, $user->password)){
            $data = array(
                "status"  => "error",
                "code"    => "400",
                "message" => "No se pudo autenticar al usuario ingresado"
            );            
        }else{
            $token = array(
                "sub"      => $user->id,
                "name"    => $user->name,
                "surname" => $user->surname,                
                "email"   => $user->email,                
                "iat"     => time(),
                "exp"     => time() * (7*24*60*60) //Token con duracion de una semana
            );             
            
            $jwt    = JWT::encode($token, $this->key, "HS256"); //Generamos el token con la libreria JWT       
            $decode = JWT::decode($jwt, $this->key, ["HS256"]);
            
            if(!$getToken){  
                $data = array(
                    "status"  => "success",
                    "code"    => "200",
                    "message" => "El usuario se ha logueado",
                    "token"    => $jwt               
                );                                                
            }else{                
                $data = array(
                    "status"  => "success",
                    "code"    => "200",
                    "message" => "El usuario se encontro",
                    "user"    => $decode               
                );        
            }
        }
        
        return $data;
    }

    public function checkToken($jwt, $getIdentity = false){
        $auth = false;
        $decode = NULL;

        try {
            $decode = JWT::decode($jwt, $this->key, ["HS256"]);
            // error_log("****** decode ******");
            // error_log(json_encode($decode));
        } catch (\Exception $e) {
            $auth = false;            
        } catch (\UnexpectedValueException $e) {
            $auth = false;            
        } catch (\DomainException $e) {
            $auth = false;            
        }

        if(!empty($decode) && is_object($decode) && isset($decode->sub)){
            $auth = true;
        }else{
            $auth = false;
        }

        if($getIdentity){
            return $decode;
        }

        return $auth;
    }
}
