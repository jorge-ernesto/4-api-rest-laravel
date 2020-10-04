<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App;
use Illuminate\Support\Facades\DB;

class PruebasController extends Controller
{
    public function post(Request $request)
    {
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
    }

    public function category(Request $request)
    {
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
    }
}
