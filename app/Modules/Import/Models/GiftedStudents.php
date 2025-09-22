<?php

namespace App\Modules\Import\Models;

use Illuminate\Database\Eloquent\Model;

class GiftedStudents extends Model {

   
    protected $table = 'agt_students';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'current_enrollment_status',
        'state_id_number',
        'last_name',
        'first_name',
        'gr',
        'school',
        'primary_exceptionality',
        'case_manager',
        'special_education_status',
        'enrichment_student',
        'district_id',
    ];

}
