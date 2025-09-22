<?php

namespace App\Modules\Application\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationProgram extends Model {

    //
    public $timestamps = false;
	protected $table='application_programs';
	public $primary_key='id';
	public $fillable=[
		'application_id',
		'grade_id',
		'program_id',
	];
}
