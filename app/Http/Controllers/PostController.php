<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App;
use Illuminate\Support\Facades\Validator;
use App\Helpers\JwtAuth;

class PostController extends Controller
{    
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $posts = App\Post::all()->load(['category', 'user']);
       
        return response()->json([
            "code"   => 200,
            "status" => "success",
            "posts"  => $posts
        ]);
    }
    
    public function create()
    {
        //
    }
    
    public function store(Request $request)
    {
        /* Obtenemos usuario autenticado */  
        $user = $this->getIdentity($request);   

        /* Obtenemos todo el request */
        //$json       = $request->json;
        $json         = $request->input("json", null); //En caso no me llegara el valor seria null
        $params       = json_decode($json);            //Convierte json en un objeto de php
        $params_array = json_decode($json, true);      //Convierte json en un array de php        

        $guardamos_post = false;

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
                "category_id" => "required",                
                "title"       => "required|string",
                "content"     => "required|string", 
                "image"       => "required",                               
            ]);

            if($validator->fails()){
                $data = array(
                    "status"  => "error",
                    "code"    => "400",
                    "message" => "El post no se ha creado",
                    "errors"  => $validator->errors()
                );
            }else{                
                $guardamos_post = true;
            }
        }

        /* Guardamos post */
        if($guardamos_post){                         
            $post              = new App\Post;
            $post->user_id     = $user->sub;
            $post->category_id = $params_array['category_id'];            
            $post->title       = $params_array['title'];            
            $post->content     = $params_array['content'];                                    
            $post->image       = $params_array['image'];                                    
            $post->save();
    
            $data = array(
                "status"  => "success",
                "code"    => "200",
                "message" => "El post se ha creado correctamente",
                "post"    => $post
            ); 
        }

        return response()->json($data, $data['code']);
    }
    
    public function show($id)
    {
        $post = App\Post::find($id);

        if(is_object($post)){
            $data = array(
                "code"    => 200,
                "status"  => "success",
                "message" => "Post encontrado",
                "post"    => $post->load(['category', 'user'])                
            );
        }else{
            $data = array(
                "code"    => 400,
                "status"  => "error",
                "message" => "No se encontro post"            
            );
        }
       
        return response()->json($data, $data["code"]);
    }
    
    public function edit($id)
    {
        //
    }
    
    public function update(Request $request, $id)
    {
        /* Obtenemos usuario autenticado */  
        $user = $this->getIdentity($request);

        /* Obtenemos todo el request */
        //$json       = $request->json;
        $json         = $request->input("json", null); //En caso no me llegara el valor seria null
        $params       = json_decode($json);            //Convierte json en un objeto de php
        $params_array = json_decode($json, true);      //Convierte json en un array de php

        $actualizamos_post = false;

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
                "category_id" => "required",                
                "title"       => "required|string",
                "content"     => "required|string"                
            ]);

            if($validator->fails()){
                $data = array(
                    "status"  => "error",
                    "code"    => "400",
                    "message" => "El post no se ha actualizado",
                    "errors"  => $validator->errors()
                );
            }else{
                $actualizamos_post = true;
            }
        }

        /* Actualizamos post */
        if($actualizamos_post){
            //$post = App\Post::find($id);                        
            $post = App\Post::where('id', $id)
                            ->where('user_id', $user->sub)
                            ->first();       

            if(empty($post)){
                $data = array(
                    "status"  => "error",
                    "code"    => "400",
                    "message" => "El post no se encontre o no tiene permisos para actualizarlo"
                );
            }else{                
                $post->category_id = $params_array['category_id'];
                $post->title       = $params_array['title'];            
                $post->content     = $params_array['content'];                                                
                $post->update();

                $data = array(
                    "status"  => "success",
                    "code"    => "200",
                    "message" => "El post se ha actualizado correctamente",
                    "post"    => $post
                ); 
            }            
        }

        return response()->json($data, $data['code']);
    }
    
    public function destroy(Request $request, $id)
    {
        /* Obtenemos usuario autenticado */  
        $user = $this->getIdentity($request);

        /* Obtenemos post */
        //$post = App\Post::find($id);        
        $post = App\Post::where('id', $id)
                        ->where('user_id', $user->sub)
                        ->first();       
        
        /* Validamos datos */
        if(is_object($post)){
            /* Eliminamos post */
            $post->delete();
            $data = array(
                "code"    => 200,
                "status"  => "success",
                "message" => "Post eliminado"
            );
        }else{
            $data = array(
                "code"    => 400,
                "status"  => "error",
                "message" => "No se encontro post o no tiene permisos para eliminarlo"
            );
        }
       
        return response()->json($data, $data["code"]);        
    }

    public function getIdentity($request){
        $token   = $request->header("Authorization");            
        $token   = str_replace('"', '', $token);
        $jwtAuth = new JwtAuth();              
        $user    = $jwtAuth->checkToken($token, true);
        return $user;
    }    
}
