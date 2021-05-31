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
    /**
     * Funcion para registrar usuario
     * 
     * Se envia un parametro llamado json por Postman, @param {"name":"Lilia","surname":"Sturman","email":"lilia@lilia.com","password":"conejitalinda777"}
     * Esto para registrar el usuario
     */   
    public function register(Request $request)
    {        
        /* Obtenemos todo el request */
        //$json       = $request->json;
        $json         = $request->input("json", null); //En caso no me llegara el valor seria null
        $params       = json_decode($json);            //Convierte json en un objeto de php
        $params_array = json_decode($json, true);      //Convierte json en un array de php
        
        $guardamos_usuario = false;

        /* Validamos datos */        
        if(empty($params_array)){
            $data = array(
                "status"  => "error",
                "code"    => "400",
                "message" => "Los datos enviados no son los correctos"
            );            
        }else{
            $params_array = array_map("trim", $params_array);
            $validator = Validator::make($params_array, [
                "name"     => "required|string",
                "surname"  => "required|string",
                "email"    => "required|email|unique:users", //El campo email no se podra repetir
                "password" => "required"
            ]);

            if($validator->fails()){
                $data = array(
                    "status"  => "error",
                    "code"    => "400",
                    "message" => "El usuario no se ha creado",
                    "errors"  => $validator->errors()
                );
            }else{
                $guardamos_usuario = true;
            }
        }        
             
        /* Guardamos usuario */
        if($guardamos_usuario){
            $user           = new App\User;
            $user->name     = $params_array['name'];
            $user->surname  = $params_array['surname'];
            $user->role     = "ROLE_USER";     
            $user->email    = $params_array['email'];        
            $password       = $this->encriptar($params_array['password']);
            $user->password = $password;             
            $user->save();
    
            $data = array(
                "status"  => "success",
                "code"    => "200",
                "message" => "El usuario se ha creado correctamente",
                "user"    => $user               
            );   
        }

        return response()->json($data, $data['code']);
    }  
    
    /**
     * Funcion para loguear usuario
     * 
     * Se envia un parametro llamado json por Postman, @param {"email":"lilia@lilia.com","password":"conejitalinda777","getToken":false}        
     * Esto para retornar el token del usuario autenticado
     * 
     * Se envia un parametro llamado json por Postman, @param {"email":"lilia@lilia.com","password":"conejitalinda777","getToken":true} 
     * Esto para retornar el token desencriptado
     */       
    public function login(Request $request)
    {    
        /* Obtenemos todo el request */
        //$json       = $request->json;
        $json         = $request->input("json", null);
        $params       = json_decode($json);
        $params_array = json_decode($json, true);

        $validamos_usuario = false;

        /* Validamos datos */
        if(empty($params_array)){
            $data = array(
                "status"  => "error",
                "code"    => "400",
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
                    "code"    => "400",
                    "message" => "El usuario no se ha validado",
                    "errors"  => $validator->errors()
                );
            }else{
                $validamos_usuario = true;
            }
        }      
        
        /* Validamos usuario */
        if($validamos_usuario){
            $email    = $params_array['email'];
            $password = $params_array['password'];
                        
            $jwtAuth = new JwtAuth();                
            if(!$params_array['getToken']){
                $data = $jwtAuth->signup($email, $password);            
            }else{
                $data = $jwtAuth->signup($email, $password, true);
            }
        }           
                
        return response()->json($data, $data['code']);
    }

    public function encriptar($password)
    {
        return Hash::make($password); 
        //return Hash("MD5", $password); 
        //return Hash("SHA256", $password); 
        //return pasword_hash($password, PASSWORD_BCRYPT, ['cost' => 4])
    }

    /**
     * Funcion para actualizar usuario
     * 
     * Se envia un header llamado Authorization por Postman, @param eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjksIm5hbWUiOiJMaWxpYSIsInN1cm5hbWUiOiJTdHVybWFuIiwiZW1haWwiOiJsaWxpYUBsaWxpYS5jb20iLCJpYXQiOjE2MDE0NTEwNjAsImV4cCI6OTY4NTU3NjAxMDg4MDAwfQ.eUeRhU7ivvH1-5J0xqfyVRl0XReNHwrVeR8zNXFMCYA
     * Este header tiene el token del usuario identificado
     * Luego de obtener el token podremos validar el usuario @return checkTocken 
     * Tambien podremos desencriptar el token @return checkTocken 
     * 
     * Se envia un parametro llamado json por Postman, @param {"name":"Lilia","surname":"Sturman","email":"lilia@lilia.com"}      
     * Esto para actualizar el usuario
     */     
    public function update(Request $request)
    {
        /* Comprobamos si el usuario esta identificado */
        $token      = $request->header("Authorization");            
        $token      = str_replace('"', '', $token);
        $jwtAuth    = new JwtAuth();              
        $checkToken = $jwtAuth->checkToken($token);

        /* Obtenemos todo el request */
        //$json       = $request->json;
        $json         = $request->input("json", null); //En caso no me llegara el valor seria null
        $params       = json_decode($json);            //Convierte json en un objeto de php
        $params_array = json_decode($json, true);      //Convierte json en un array de php
        
        $actualizamos_usuario = false;

        /* Validamos datos */
        if($checkToken && !empty($params_array)){
            /* Obtenemos usuario autenticado */
            $user = $jwtAuth->checkToken($token, true);                                

            $params_array = array_map("trim", $params_array);
            $validator = Validator::make($params_array, [
                "name"     => "required|string",
                "surname"  => "required|string",
                "email"    => "required|email|unique:users,email,$user->sub"
            ]);

            if($validator->fails()){
                $data = array(
                    "status"  => "error",
                    "code"    => "400",
                    "message" => "El usuario no se ha actualizado",
                    "errors"  => $validator->errors()
                );
            }else{
                $actualizamos_usuario = true;
            }                             
        }else{            
            $data = array(
                "status"  => "error",
                "code"    => "400",
                "message" => "EL usuario no esta identificado ó los datos enviados no son los correctos"
            );
        }
              
        /* Actualizamos usuario */
        if($actualizamos_usuario){
            $usuarioActualizado              = App\User::find($user->sub); //findOrFail cuando falla retorna una pagina web

            /* Eliminamos imagen anterior */
            \Storage::disk('public')->delete("users/$usuarioActualizado->image");

            $usuarioActualizado->name        = $params_array['name'];
            $usuarioActualizado->surname     = $params_array['surname'];        
            $usuarioActualizado->email       = $params_array['email'];
            $usuarioActualizado->description = $params_array['description'];
            $usuarioActualizado->image       = $params_array['image'];
            $usuarioActualizado->update();
            
            $data = array(
                "status"  => "success",
                "code"    => "200",
                "message" => "El usuario se ha actualizado correctamente",
                "user"    => $usuarioActualizado               
            );   
        }

        return response()->json($data, $data['code']);
    }   
      
    /**
     * Funcion para subir imagen de usuario
     * 
     * Se envia un header llamado Authorization por Postman, @param eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjksIm5hbWUiOiJMaWxpYSIsInN1cm5hbWUiOiJTdHVybWFuIiwiZW1haWwiOiJsaWxpYUBsaWxpYS5jb20iLCJpYXQiOjE2MDE0NTEwNjAsImV4cCI6OTY4NTU3NjAxMDg4MDAwfQ.eUeRhU7ivvH1-5J0xqfyVRl0XReNHwrVeR8zNXFMCYA
     * Este header tiene el token del usuario identificado
     * Luego de obtener el token podremos validar el usuario @return middleware ApiAuthMiddleware
     * 
     * Se envia un file llamado file0 por Postman      
     * Esto subira la imagen del usuario
     */
    public function upload(Request $request)
    {
        /* Recogemos la imagen de la peticion */        
        $image = $request->file('file0'); //name del campo del fronted se llamara file0

        $subimos_imagen = false;

        /* Validamos imagen */
        if(!$image){
            $data = array(
                "status"  => "error",
                "code"    => "400",
                "message" => "Error al subir imagen, los datos enviados no son los correctos"
            );
        }else{                        
            $validator = Validator::make($request->all(), [
                'file0' => 'required|image|mimes:jpg,jpeg,png,gif',                
            ]);    

            if($validator->fails()){
                $data = array(
                    "status"  => "error",
                    "code"    => "400",
                    "message" => "Error al subir imagen",
                    "errors"  => $validator->errors()
                );
            }else{
                /* Subimos imagen */
                $subimos_imagen = true;
            }
        }

        /* Subimos imagen */
        if($subimos_imagen){
            $image_name = time()."_".$image->getClientOriginalName();
            \Storage::disk('public')->put("users/$image_name", \File::get($image));

            $data = array(
                "status"  => "success",
                "code"    => "200",
                "message" => "La imagen se subio correctamente",
                "image"   => "$image_name"    
            );  
        }         

        //return response($data, $data['code'])->header('Content-Type', 'text-plain'); //De esta forma se suben imagenes de usuario
        return response()->json($data, $data['code']); //Para probar en postman
    }

    /**
     * Funcion para obtener imagenes
     */
    public function getImage($filename)
    {    
        // error_log($filename);
        $exists = \Storage::disk('public')->exists("users/$filename");
        
        if($exists){
            return \Storage::disk('public')->download("users/$filename");
            // return \Storage::disk('public')->get("users/$filename");
        }else{
            $data = array(
                "status"  => "error",
                "code"    => "404",
                "message" => "La imagen no existe"
            );

            return response()->json($data, $data['code']);
        }                      
    }

    /**
     * Funcion para obtener el usuario
     */
    public function detail($id)
    {
        $user = App\User::find($id); //findOrFail cuando falla retorna una pagina web
        // error_log(json_encode($user));
        
        if(is_object($user)){
            $data = array(
                "status"  => "success",
                "code"    => "200",
                "message" => "Usuario encontrado",
                "user"    => $user
            );
        }else{
            $data = array(
                "status"  => "error",
                "code"    => "400",
                "message" => "Usuario no encontrado"                
            );
        }        

        return response()->json($data, $data['code']);
    }
}
