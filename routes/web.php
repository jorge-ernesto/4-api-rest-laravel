<?php

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

Route::get('/', function () {
    return view('welcome');
});

//Rutas de prueba
Route::get('/rata', function () {
    return "<h1>hola mundo</h1>";
});

Route::get('/pruebas/{nombre?}', function ($nombre = null) {
    $texto = "Texto desde una ruta<br>";
    $texto .= "Nombre: $nombre";
    return $texto;
});

Route::get('/pruebas2/{nombre?}', function ($nombre = null) {
    $texto = "Texto desde una ruta<br>";
    $texto .= "Nombre: $nombre";

    return view('pruebas2', array(
        "texto" => $texto
    ));
});

Route::get("/animales", "Pruebas2Controller@index");
Route::get("/test-orm", "Pruebas2Controller@testOrm");

//Rutas del API
/**
 * GET    : Conseguir datos o recursos
 * POST   : Guardar datos o recursos o hacer logica y devolver algo
 * PUT    : Actualizar recursos o datos
 * DELETE : Elimianr datos o recursos 
*/

//Rutas de prueba
Route::get("/usuario/pruebas", "UserController@pruebas");
Route::get("/categoria/pruebas", "CategoryController@pruebas");
Route::get("/entrada/pruebas", "PostController@pruebas");

//Rutas del controladro de Usuarios
Route::post("/api/register", "UserController@register");
Route::post("/api/login", "UserController@login");