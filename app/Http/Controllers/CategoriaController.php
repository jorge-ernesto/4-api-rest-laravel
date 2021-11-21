<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index(Request $request)
    {
        /* Obtenemos todo el request */
        // error_log('index');
        // error_log(json_encode($request->all()));

        /**
         * Cargamos array de objetos de categorias sin paginacion
         */
        $categories = App\Category::all()->load('posts');        

        /**
         * Validamos texto a buscar, la propiedad "where" de Laravel controla la información cuando se le pasa NULL 
         */    
        $search = $request->search;
        if($search == "undefined" || $search == "null" || trim($search) == ''){ //Angular envia data undefined o null con comillas al enviarlo por GET: this.url+'category?page='+page+'&search='+search
            $search = NULL;
        }

        /**
         * Cargamos array de objetos de categorias sin paginacion, usando Paginate y Load
         */
        $categories_ = App\Category::where('name', 'LIKE', '%'.$search.'%')
                                    ->paginate('10');
        $categories_->load('posts');          

        /**
         * Alternativa a Paginate y Load
         * @link https://laravel.io/forum/02-27-2015-pagination-does-not-work-with-eager-loading
         */ 
        // $categories = App\Category::with('posts')->paginate('2');            

        /* Validamos informacion a retornar */
        // echo "<pre>";
        // print_r($categories);
        // echo "</pre>";
        // error_log(json_encode($categories)); //Comentar esto ya que sino el log de PHP se ensuciaria demasiado, es llamado desde Angular cada segundo desde la function ngDoCheck

        return response()->json([
            "code"       => 200,
            "status"     => "success",
            "categories" => $categories,
            "categories_" => $categories_
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        /* Obtenemos todo el request */
        //$json       = $request->json;
        $json         = $request->input("json", null); //En caso no me llegara el valor seria null
        $params       = json_decode($json);            //Convierte json en un objeto de php
        $params_array = json_decode($json, true);      //Convierte json en un array de php

        $guardamos_categoria = false;

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
                "name" => "required|string|unique:categories"
            ]);

            if($validator->fails()){
                $data = array(
                    "status"  => "error",
                    "code"    => "400",
                    "message" => "El usuario no se ha creado",
                    "errors"  => $validator->errors()
                );
            }else{
                $guardamos_categoria = true;
            }
        }

        /* Guardamos categoria */
        if($guardamos_categoria){
            $category                   = new App\Category;
            $category->name             = $params_array['name'];
            $category->date_publication = $params_array['date_publication'];
            $category->save();

            $data = array(
                "status"  => "success",
                "code"    => "200",
                "message" => "La categoria se ha creado correctamente",
                "category"    => $category
            );
        }

        return response()->json($data, $data['code']);
    }

    public function show($id)
    {
        // $category = App\Category::find($id);
        $category = App\Category::select(DB::raw('id, name, DATE(date_publication) as date_publication'))->find($id);

        if(is_object($category)){
            $data = array(
                "code"     => 200,
                "status"   => "success",
                "message" => "Categoria encontrada",
                "category" => $category->load('posts')
            );
        }else{
            $data = array(
                "code"     => 400,
                "status"   => "error",
                "message" => "No se encontro categoria"
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
        /* Obtenemos todo el request */
        //$json       = $request->json;
        $json         = $request->input("json", null); //En caso no me llegara el valor seria null
        $params       = json_decode($json);            //Convierte json en un objeto de php
        $params_array = json_decode($json, true);      //Convierte json en un array de php

        /* Eliminamos elementos que se cargan en el load de la funcion show, sino lo hacemos array_map fallara */
        unset($params_array['posts']);        

        $actualizamos_categoria = false;

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
                "name" => "required|string|unique:categories,name,$id"
            ]);

            if($validator->fails()){
                $data = array(
                    "status"  => "error",
                    "code"    => "400",
                    "message" => "La categoria no se ha actualizado",
                    "errors"  => $validator->errors()
                );
            }else{
                $actualizamos_categoria = true;
            }
        }

        /* Actualizamos categoria */
        if($actualizamos_categoria){
            $category                   = App\Category::find($id);
            $category->name             = $params_array['name'];
            $category->date_publication = $params_array['date_publication'];
            $category->update();

            $data = array(
                "status"  => "success",
                "code"    => "200",
                "message" => "La categoria se ha actualizado correctamente",
                "category"    => $category
            );
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id)
    {
        /* Obtenemos category */
        $category = App\Category::find($id);        

        /* Validamos datos */
        if(is_object($category)){
            /* Eliminamos category */
            $category->delete();
            $data = array(
                "code"     => 200,
                "status"   => "success",
                "message" => "Categoria eliminada"
            );
        }else{
            $data = array(
                "code"     => 400,
                "status"   => "error",
                "message" => "No se encontro categoria"
            );
        }

        return response()->json($data, $data["code"]);
    }
}
