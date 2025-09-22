<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentGradeLatest extends Model
{
    public $timestamps = false;
    protected $table='studentgrade_latest';
    protected $primaryKey='id';
    protected $fillable=[
        'stateID',
    	'academicYear',
        'GradeName',
    	'academicTerm',
    	'courseTypeID',
    	'courseType',
    	'courseName',
        'courseFullName',
    	'sectionNumber',
    	'numericGrade',
    	'use_in_calculations',
    ];
}
