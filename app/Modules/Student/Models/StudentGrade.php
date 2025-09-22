<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGrade extends Model {
	protected $table = 'studentgrade';

	protected $fillable = [
		'stateID',
		'academicYear',
		'academicTerm',
		'GradeName',
		'courseTypeID',
		'sequence',
		'courseType',
		'courseFullName',
		'courseName',
		'sectionNumber',
		'fullsection_number',
		'numericGrade',
	];
}
