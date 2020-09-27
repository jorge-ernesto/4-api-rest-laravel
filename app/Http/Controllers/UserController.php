<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Helpers\JwtAuth;

class UserController extends Controller
{
    public function register(Request $request){
        /* Esto se envia por Postman */
        /* {"name":"Lilia","surname":"Sturman","email":"lilia@lilia.com","password":"lilia"} */

        /* Obtenemos todo el request */
        //$json       = $request->json;
        $json         = $request->input("json", null); //En caso no me llegara el valor seria null
        $params       = json_decode($json);            //Convierte json en un objeto de php
        $params_array = json_decode($json, true);      //Convierte json en un array de php

        /* Validar datos */
        $es_validacion_correcta = false;
        if(empty($params_array)){
            $data = array(
                "status"  => "error",
                "code"    => "404",
                "message" => "Los datos enviados no son los correctos"
            );            
        }else{
            $params_array = array_map("trim", $params_array);

            $validator = Validator::make($params_array, [
                "name"     => "required|alpha",
                "surname"  => "required|alpha",
                "email"    => "required|email|unique:users", //El campo email no se podra repetir
                "password" => "required"
            ]);

            if($validator->fails()){
                $data = array(
                    "status"  => "error",
                    "code"    => "404",
                    "message" => "El usuario no se ha creado",
                    "errors"  => $validator->errors()
                );
            }else{
                $data = $this->guardamos_usuario($params_array);
            }
        }        
               
        return response()->json($data, $data['code']);
    }

    public function guardamos_usuario($params_array){                
        $user           = new App\User;
        $user->name     = $params_array['name'];
        $user->surname  = $params_array['surname'];
        $user->role     = "ROLE_USER";     
        $user->email    = $params_array['email'];        
        $password       = Hash("MD5", $params_array['password']); //Hash::make($params_array['password']);
        $user->password = $password;             
        $user->save();

        $data = array(
            "status"  => "success",
            "code"    => "200",
            "message" => "El usuario se ha creado correctamente",
            "user"    => $user               
        );   
        
        return $data;
    }

    public function login(Request $request){
        /* Esto se envia por Postman */
        /* {"email":"lilia@lilia.com","password":"lilia"} */

        /* Obtenemos todo el request */
        //$json       = $request->json;
        $json         = $request->input("json", null);
        $params       = json_decode($json);
        $params_array = json_decode($json, true);

        /* Validar datos */
        $es_validacion_correcta = false;
        if(empty($params_array)){
            $data = array(
                "status"  => "error",
                "code"    => "404",
                "message" => "Los datos enviados no son los correctos"                
            );            
        }else{
            $params_array = array_map("trim", $params_array);

            $validator = Validator::make($params_array, [
                "email"    => "required|email",
                "password" => "required"
            ]);

            if($validator->fails()){
                $data = array(
                    "status"  => "error",
                    "code"    => "404",
                    "message" => "El usuario no se ha validado",
                    "errors"  => $validator->errors()
                );
            }else{
                $data = $this->validamos_usuario($params_array);    
            }
        }                                  
                
        return response()->json($data);
    }

    public function validamos_usuario($params_array){
        $email    = $params_array['email'];
        $password = Hash("MD5", $params_array['password']); //Hash::make($params_array['password']);
                    
        $jwtAuth = new JwtAuth();                
        if(empty($params_array['getToken'])){
            $data = $jwtAuth->signup($email, $password);            
        }else{
            $data = $jwtAuth->signup($email, $password, true);
        }

        return $data;
    }
}
