<?php

namespace App\Modules\Enrollment\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model {

    public $fillable = [
    	'district_id', 
    	'school_year',
    	'confirmation_style',
    	'import_grades_by',
    	'begning_date',
    	'ending_date',
    	'perk_birthday_cut_off',
    	'kindergarten_birthday_cut_off',
    	'first_grade_birthday_cut_off',
    ];

}
