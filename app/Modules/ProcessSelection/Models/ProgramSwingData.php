<?php

namespace App\Modules\ProcessSelection\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramSwingData extends Model {

    protected $table='program_swing_data';
    protected $primarykey='id';
    public $fillable=[
    	'district_id',
    	'application_id',
    	'enrollment_id',
    	'program_name',
        'swing_percentage',
    	'user_id'
    ]; 

}
