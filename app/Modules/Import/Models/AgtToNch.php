<?php

namespace App\Modules\Import\Models;

use Illuminate\Database\Eloquent\Model;

class AgtToNch extends Model {

   
    protected $table = 'agt_to_nch';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'student_id',
        'name',
        'user_id',
        'grade_level',
        'enrollment_id',
        'program_name',
        'created_at',
        'updated_at',
    ];

}
