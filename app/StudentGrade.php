<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentGrade extends Model
{
    public $timestamps = false;
    protected $table='studentgrade';
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
