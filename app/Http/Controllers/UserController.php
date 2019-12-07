<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function pruebas(Request $request){
        return "Accion de pruebas de UserController";
    }

    public function register(Request $request){
        //1.Recoger los datos del usuario por post        
        //Formato Json
        //{"name":"Lilia","surname":"Sturman","email":"lilia@lilia.com","password":"lilia"}
        $json         = $request->input("json", null); //En caso no me llegara el valor seria null
        $params       = json_decode($json);            //Convierte json en un objeto de php
        $params_array = json_decode($json, true);      //Convierte json en un array de php
        // print_r($json);
        // print_r($params);
        // print_r($params_array);
        // die();
            
        //2.Validar datos
        $es_validacion_correcta = false;
        if(!empty($params_array)){
            $params_array = array_map("trim", $params_array);

            $validate = \Validator::make($params_array, [
                "name"     => "required|alpha",
                "surname"  => "required|alpha",
                "email"    => "required|email|unique:users", //El campo email sera unico en la tabal users
                "password" => "required"
            ]);

            if($validate->fails()){
                $data = array(
                    "status"  => "error",
                    "code"    => "404",
                    "message" => "El usuario no se ha creado",
                    "errors"  => $validate->errors()
                );
            }else{
                $es_validacion_correcta = true;                
            }
        }else{
            $data = array(
                "status"  => "error",
                "code"    => "404",
                "message" => "Los datos enviados no son los correctos"
            );
        }        

        if($es_validacion_correcta){
            //3.Cifrar contraseña
            //$pwd = password_hash($params_array['password'], PASSWORD_BCRYPT, ['cost' => 4]);
            $pwd = hash("MD5", $params_array['password']);

            //4.Crear el usuario
            $user = new User();
            $user->name     = $params_array['name'];
            $user->surname  = $params_array['surname'];
            $user->email    = $params_array['email'];
            $user->password = $pwd;
            $user->role     = "ROLE_USER";
            //print_r($user);
            //die();

            //5.Guardar el usuario
            $user->save();

            $data = array(
                "status"  => "success",
                "code"    => "200",
                "message" => "El usuario se ha creado correctamente",
                "user"    => $user               
            );
        }
       
        //5.Convierte array en json                
        return response()->json($data, $data['code']);
    }

    public function login(Request $request){
        return "Accion de login de usuarios";
    }
}
