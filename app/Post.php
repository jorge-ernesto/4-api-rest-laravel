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

    /* Relacion de muchos a uno (Un post solo tiene un usuario y una categoria) */
    public function user(){
        return $this->belongsTo("App\User");
    }

    public function category(){
        return $this->belongsTo("App\Category");
    }
}
