<?php

namespace App\Modules\ReturningStudent\Models;

use Illuminate\Database\Eloquent\Model;

class ReturningStudent extends Model {
	protected $table = 'returning_students';
	protected $fillable = [
		'stateID',
		'birthday',
		'student_name',
		'next_grade',
		'race',
		'current_school',
		'current_signature_academy',
	];
}
