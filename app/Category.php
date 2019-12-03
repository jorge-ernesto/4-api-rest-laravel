<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories'; //La tabla que utilizara de la bd es categories

    //Relacion de uno a muchos (Una categoria puede tener muchos posts)
    public function post(){
        return $this->hasMany("App\Post");
    }

}
