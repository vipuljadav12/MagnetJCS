<?php

namespace App\Modules\SetEligibility\Models;

use Illuminate\Database\Eloquent\Model;

class SetEligibilityConfiguration extends Model {

 	protected $table='set_eligibility_configuration';
    protected $primarykey='id';
    public $fillable=[
    	'district_id',
    	'program_id',
    	'application_id',
    	'eligibility_type',
        'eligibility_id',
        'configuration_type',
    	'configuration_value'
    ]; 

}
