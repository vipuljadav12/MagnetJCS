<?php

namespace App\Modules\SetEligibility\Models;

use Illuminate\Database\Eloquent\Model;

class SetEligibilityExtraValue extends Model {

 	protected $table='seteligibility_extravalue';
    protected $primarykey='id';
    public $fillable=[
    	'program_id',
    	'application_id',
    	'eligibility_type',
    	'extra_values'
    ]; 

}
