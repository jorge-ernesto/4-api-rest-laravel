<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table      = "posts";
    protected $primaryKey = "id";
    public $timestamps    = true;

    protected $fillable = [
        "user_id",
        "category_id",
        "title",
        "content",
        "image"        
    ];

    /* RELACION DE MUCHOS A UNO */
    //Un post solo tiene un usuario
    public function user(){
        return $this->belongsTo("App\User", "user_id");
    }

    //Un post solo tiene una categoria
    public function category(){
        return $this->belongsTo("App\Category", "category_id");
    }
}
