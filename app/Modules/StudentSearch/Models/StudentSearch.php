<?php

namespace App\Modules\StudentSearch\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSearch extends Model {
	protected $table = 'priorities';
	protected $primaryKey='id';
    
    public $fillable = [
    	'name', 
    	'district_id',
    	'enrollment_id',
    	'status',
    ];

}
