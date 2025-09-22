<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentCDI extends Model
{
    public $timestamps = false;
    protected $table='student_conduct_disciplinary';
    protected $primaryKey='id';
    protected $fillable=[
        'stateID',
    	'b_info',
        'c_info',
    	'd_info',
    	'e_info',
    	'susp',
    	'susp_days',
    ];
}
