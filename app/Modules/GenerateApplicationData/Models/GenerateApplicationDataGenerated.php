<?php

namespace App\Modules\GenerateApplicationData\Models;

use Illuminate\Database\Eloquent\Model;

class GenerateApplicationDataGenerated extends Model {

    protected $table='application_data_generated';
	protected $primaryKey='id';
	protected $fillable=[
	    'enrollment_id',
		'first_program',
		'second_program',
	    'grade',
		'status',
		'file_name',
		'total_records',
	];

}
