<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

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

    public function testOrm(){
        $dataPost = Post::all();
        foreach($dataPost as $key=>$value){
            echo "******************".$value['title']."*******************<br><br>";            

            echo $dataPost[$key]                      . "<br>";
            echo $dataPost[$key]['title']             . "<br>";                                                
            echo $dataPost[$key]->title               . "<br><br>";             

            //Metodo para obtener datos relacionales
            echo $dataPost[$key]->User                . "<br>";
            echo $dataPost[$key]->User['description'] . "<br>";
            echo $dataPost[$key]->User->description   . "<br><br>";

            //Metodo para obtener datos relacionales
            echo $dataPost[$key]->Category            . "<br>";
            echo $dataPost[$key]->Category['name']    . "<br>";
            echo $dataPost[$key]->Category->name      . "<br><br>";            
        }

        $dataCategory = Category::all();
        foreach($dataCategory as $key=>$value){
            echo "******************".$value['name']."*******************<br>";

            $dataPost = $dataCategory[$key]->Post;
            foreach ($dataPost as $key2=>$value2) {
                echo $dataPost[$key2]['title']          . " - ".
                     $dataPost[$key2]['content']        . " - ".
                     $dataPost[$key2]->User['name']     . " - ".
                     $dataPost[$key2]->Category['name'] . "<br>";
            }
        }
        
        die();
    }
}
