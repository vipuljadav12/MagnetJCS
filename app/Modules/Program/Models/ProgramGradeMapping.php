<?php

namespace App\Modules\Program\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramGradeMapping extends Model {

    //
    protected $table='program_grade_mapping';
    public $primaryKey='id';
    public $fillable=[
        'district_id',
        'enrollment_id',
        'program_id',
        'grade'
    ];

}