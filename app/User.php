<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table      = "users";
    protected $primaryKey = "id";
    public $timestamps    = true;
    
    protected $fillable = [
        'name',
        'surname',
        'role',        
        'email',
        'password',
        'description',
        'image'
    ];
    
    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    /* RELACION DE UNO A MUCHOS */
    //Un usuario puede tener muchos posts
    public function posts(){
        return $this->hasMany("App\Post");
    }
}
