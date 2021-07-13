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

//Ruta por defecto
Route::get('/', function () {
    return view('welcome');
});

//RUTAS DEL API
    /**
     * Metodos HTTP comunes
     *
     * GET   : Conseguir datos o recursos
     * POST  : Guardar datos o recursos o hacer logica y devolver algo
     * PUT   : Actualizar recursos o datos
     * DELETE: Elimianr datos o recursos
     *
     */

    //Rutas de prueba
    Route::get("/pruebas/post", "PruebasController@post");
    Route::get("/pruebas/category", "PruebasController@category");

    //Rutas de usuarios
    Route::post("/api/register"             , "UserController@register");
    Route::post("/api/login"                , "UserController@login");
    Route::put("/api/user/update"           , "UserController@update"); //Tiene el middleware de autenticacion en la funcion update
    Route::post("/api/user/upload"          , "UserController@upload")->middleware('api.auth'); //Tiene el middleware de autenticacion
    Route::get("/api/user/avatar/{filename}", "UserController@getImage");
    Route::get("/api/user/detail/{id}"      , "UserController@getUser");

    //Rutas de categorias
    Route::resource('/api/category', 'CategoriaController');

    //Rutas de entradas
    Route::resource('/api/post'            , 'PostController');
    Route::post("/api/post/upload"         , "PostController@upload");
    Route::get("/api/post/image/{filename}", "PostController@getImage");
    Route::get("/api/post/category/{id}"   , "PostController@getPostsByCategory");
    Route::get("/api/post/user/{id}"       , "PostController@getPostsByUser");
