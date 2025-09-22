<?php

namespace App\Modules\Priority\Models;

use Illuminate\Database\Eloquent\Model;

class PriorityDetail extends Model {

	protected $table = 'priority_details';

    public $fillable = [
    	'priority_id', 
    	'name', 
    	'description', 
    	'sibling', 
    	'majority_race_in_home_zone_school', 
    	'feeder', //'current_school'
    	'magnet_employee', //'district_employee'
    	'current_over_new',
    	'sort',
        'magnet_student',
    ];

}
