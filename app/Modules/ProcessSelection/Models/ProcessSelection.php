<?php

namespace App\Modules\ProcessSelection\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessSelection extends Model {

    protected $table='process_selection';
    protected $primarykey='id';
    public $fillable=[
    	'district_id',
    	'enrollment_id',
    	'application_id',
        'updated_by',
        'auto_decline_cron',
        'selected_programs',
        'processed_programs',
        'last_date_online_acceptance',
        'last_date_offline_acceptance',
    	'form_id',
    	'commited',
        'type',
        'version'
    ]; 

}
