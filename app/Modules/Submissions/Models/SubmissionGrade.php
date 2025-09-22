<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionGrade extends Model {

    //
    public $timestamps = false;
    protected $table='submission_grade';
    public $traitField = "submission_id";
    public $additional = ['enrollment_id', 'application_id'];

    public $primaryKey='id';
    public $fillable=[
    	'submission_id',
        'stateID',
    	'academicYear',
        'GradeName',
    	'academicTerm',
    	'courseTypeID',
        'sequence',
    	'courseType',
        'courseFullName',
    	'courseName',
    	'sectionNumber',
        'fullsection_number',
        'actual_numeric_grade',
        'advanced_course_bonus',
    	'numericGrade',
    	'use_in_calculations',
    ];

}
