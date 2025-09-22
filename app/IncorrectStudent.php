<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class IncorrectStudent extends Authenticatable
{
    public $table = 'incorrect_student';
    public $primaryKey = "id";
    public $fillable = [
        'student_id','status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
