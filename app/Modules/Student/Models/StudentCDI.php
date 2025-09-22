<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Student\Models\Student;

class StudentCDI extends Model {
	protected $table = 'student_conduct_disciplinary';
	protected $fillable = [
		'stateID',
		'student_id',
		'b_info',
		'c_info',
		'd_info',
		'e_info',
		'susp',
		'susp_days',
	];
}
