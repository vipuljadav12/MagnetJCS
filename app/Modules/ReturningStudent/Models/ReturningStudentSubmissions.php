<?php

namespace App\Modules\ReturningStudent\Models;

use Illuminate\Database\Eloquent\Model;

class ReturningStudentSubmissions extends Model {
	protected $table = 'returning_student_submissions';
	protected $fillable = [
		'student_id',
		'returning_customer',
		'reason',
		'birthday',
		'student_name',
		'next_grade',
		'race',
		'parent_first_name',
		'parent_last_name',
		'parent_email',
		'phone',
		'alternate_phone',
		'current_school',
		'current_signature_academy',
	];
}
