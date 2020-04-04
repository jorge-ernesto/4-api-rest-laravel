<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;

class UserController extends Controller
{
    public function register(Request $request){
        /* Esto se envia por Postman */
        /* {"name":"Lilia","surname":"Sturman","email":"lilia@lilia.com","password":"lilia"} */

        //$json       = $request->json;
        $json         = $request->input("json", null); //En caso no me llegara el valor seria null
        $params       = json_decode($json);            //Convierte json en un objeto de php
        $params_array = json_decode($json, true);      //Convierte json en un array de php

        /* Validar datos */
        $es_validacion_correcta = false;
        if(!empty($params_array)):
            $params_array = array_map("trim", $params_array);

            $validate = Validator::make($params_array, [
                "name"     => "required|alpha",
                "surname"  => "required|alpha",
                "email"    => "required|email|unique:users", //El campo email no se podra repetir
                "password" => "required"
            ]);

            if($validate->fails()):
                $data = array(
                    "status"  => "error",
                    "code"    => "404",
                    "message" => "El usuario no se ha creado",
                    "errors"  => $validate->errors()
                );
            else:
                $es_validacion_correcta = true;                
            endif;
        else:
            $data = array(
                "status"  => "error",
                "code"    => "404",
                "message" => "Los datos enviados no son los correctos"
            );
        endif;

        /* Guardamos users */
        if($es_validacion_correcta):                                            
            $user           = new User();
            $user->name     = $params_array['name'];
            $user->surname  = $params_array['surname'];
            $user->role     = "ROLE_USER";     
            $user->email    = $params_array['email'];
            $password       = hash("MD5", $params_array['password']);
            $user->password = $password;             
            $user->save();

            $data = array(
                "status"  => "success",
                "code"    => "200",
                "message" => "El usuario se ha creado correctamente",
                "user"    => $user               
            );
        endif;
       
        /* Response en Json */
        return response()->json($data, $data['code']);
    }

    public function login(Request $request){        
        /* Esto se envia por Postman */
        $json         = $request->input("json", null);
        $params       = json_decode($json);
        $params_array = json_decode($json, true);

        /* Validar datos */
        $es_validacion_correcta = false;
        if(!empty($params_array)):
            $params_array = array_map("trim", $params_array);

            $validate = Validator::make($params_array, [                
                "email"    => "required|email",
                "password" => "required"
            ]);

            if($validate->fails()):
                $data = array(
                    "status"  => "error",
                    "code"    => "404",
                    "message" => "El usuario no se ha creado",
                    "errors"  => $validate->errors()
                );
            else:
                $es_validacion_correcta = true;                
            endif;
        else:
            $data = array(
                "status"  => "error",
                "code"    => "404",
                "message" => "Los datos enviados no son los correctos"                
            );
        endif;

        /* Devolver token o datos */
        if($es_validacion_correcta){            
            $email    = $params_array['email'];
            $password = hash("MD5", $params_array['password']);
                        
            $jwtAuth = new JwtAuth();
            $data    = $jwtAuth->signup($email, $password);
            if(!empty($params_array['getToken'])):
                $data = $jwtAuth->signup($email, $password, true);
            endif;
        }                                            
        
        /* Response en Json */
        return response()->json($data, $data['code']);
    }
}
