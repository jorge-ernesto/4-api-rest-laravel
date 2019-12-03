<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts'; //La tabla que utilizara de la bd es posts

    //Relacion de uno a muchos inversa (muchos a uno) (Un post solo tiene un usuario y categoria)
    public function user(){
        return $this->belongsTo("App\User", "user_id");
    }

    public function category(){
        return $this->belongsTo("App\Category", "category_id");
    }
}
