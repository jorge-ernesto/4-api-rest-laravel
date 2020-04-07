<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table      = "categories";
    protected $primaryKey = "id";
    public $timestamps    = true;

    protected $fillable = [
        "name"        
    ];

    /* Relacion de uno a muchos (Una categoria puede tener muchos posts) */
    public function posts(){
        return $this->hasMany("App\Post");
    }

}
