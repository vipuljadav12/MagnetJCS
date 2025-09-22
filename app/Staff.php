<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Session;

class Staff extends Model
{
    //
    protected $table='staff';
    public $additional = [];
    public $date_fields = [];
    public $primaryKey='id';
    public $createField  = ['first_name', 'last_name', 'email', 'staff_id'];
    public $fillable = 
    [
        'first_name', 
        'last_name', 
        'email', 
        'staff_id'
    ];
}