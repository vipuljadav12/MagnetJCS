<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionAcademicGradeCalculation extends Model {

    //
    protected $table='submission_academic_grade_calculation';
    public $traitField = "submission_id";
    public $additional = ['enrollment_id', 'application_id'];
    protected $primaryKey='id';
    public $fillable=[
    	'submission_id',
    	'config_name',
    	'gpa',
    	'given_score',
    ];

}
