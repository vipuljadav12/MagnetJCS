<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model {
	protected $table = 'student';
	protected $fillable = [
		'stateID',
		'student_id',
		'first_name',
		'last_name',
		'race',
		'gender',
		'birthday',
		'address',
		'city',
		'zip',
		'current_school',
		'current_grade',
		'IsHispanic',
		'nonHSVStudent',
		'state',
		'email',
	];

}
