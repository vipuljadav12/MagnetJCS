<?php

namespace App\Modules\ManualSelection\Models;

use Illuminate\Database\Eloquent\Model;

class SelectionPreReq extends Model {

	protected $table='submissions_pre_req';
    protected $primarykey='id';
    public $fillable=[
    	'enrollment_id',
    	'district_id',
		'program_id',
		'grade',
		'course_name'
    ]; 
}