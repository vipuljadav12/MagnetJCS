<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class IncorrectStudentBDay extends Authenticatable
{
    public $table = 'incorrect_student_bday';
    public $primaryKey = "id";
    public $fillable = [
        'student_id','birthday'
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
