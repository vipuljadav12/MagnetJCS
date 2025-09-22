<?php

namespace App\Modules\Enrollment\Models;

use Illuminate\Database\Eloquent\Model;

class EnrollmentRaceComposition extends Model {
    public $table = 'enrollment_race_composition';

    public $fillable = [
    	'enrollment_id', 
    	'black',
    	'white',
    	'other',
        'swing',
    ];

}
