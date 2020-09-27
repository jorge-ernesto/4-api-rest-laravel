<?php

use Illuminate\Support\Facades\Route;

use App\Post;
use App\Category;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Ruta por defecto */
Route::get('/', function () {
    return view('welcome');
});

Route::post("/api/register", "UserController@register");
Route::post("/api/login"   , "UserController@login");

/* Ruta para pruebas */
Route::get('/test', function(){
    /* Post */
    $dataPost  = App\Post::all();    
    $dataPost2 = DB::select('select * from posts');
    $dataPost3 = DB::table('posts')
                    ->get();

    foreach($dataPost as $key=>$post){
        echo "<h3>{$post->title}         </h3>";
        echo "<p> {$post->user->name}    </p>";
        echo "<p> {$post->category->name}</p>";
        echo "<hr>";
    }
    /* Fin Post */

    /* Category */
    $dataCategory  = App\Category::all();
    $dataCategory2 = DB::select('select * from categories');
    $dataCategory3 = DB::table('categories')
                        ->get();

    foreach($dataCategory as $key=>$category){
        echo "<h1>{$category->name}</h1>";

        foreach($category->posts as $key=>$post){
            echo "<h3>{$post->title}         </h3>";
            echo "<p> {$post->user->name}    </p>";
            echo "<p> {$post->category->name}</p>";
        }
        echo "<hr>";
    }
    /* Fin Category */
});

/**
 Rutas del API
 GET   : Conseguir datos o recursos
 POST  : Guardar datos o recursos o hacer logica y devolver algo
 PUT   : Actualizar recursos o datos
 DELETE: Elimianr datos o recursos  
*/
