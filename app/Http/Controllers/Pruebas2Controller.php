<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Pruebas2Controller extends Controller
{
    public function index(){
        $titulo = "Animales";

        $dataAnimales   = [];
        $dataAnimales[] = "Perro";
        $dataAnimales[] = "Gato";
        $dataAnimales[] = "Lince";

        return view('pruebas2.index', array(
            "titulo"       => $titulo,
            "dataAnimales" => $dataAnimales
        ));
    }
}
