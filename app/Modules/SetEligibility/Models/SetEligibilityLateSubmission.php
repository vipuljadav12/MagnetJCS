<?php

namespace App\Modules\SetEligibility\Models;

use Illuminate\Database\Eloquent\Model;

class SetEligibilityLateSubmission extends Model {

 	protected $table='set_eligibility_late_submission';
    protected $primarykey='id';
    public $fillable=[

    	'district_id',
    	'program_id',
    	'eligibility_type',
        'eligibility_id',
    	'required',
    	'eligibility_value',
    	'status',
    ]; 

}
