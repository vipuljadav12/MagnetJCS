<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Session;
use DB;

class SubmissionsAttendance extends Model {

    //
    protected $table='submissions_attendance';
    public $additional = ['enrollment_id'];
    public $primaryKey='id';
    
    public $fillable=[
    	'student_id',
        'grade_level',
        'comment',
    	'attendance_date',
    	'attendance_mode_code',
        'attendance_code',
        'attendance_code_description',
    	'academic_year',
    ];

}