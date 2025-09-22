<?php

namespace App\Modules\Waitlist\Models;

use Illuminate\Database\Eloquent\Model;

class WaitlistProcessLogs extends Model {

    //
    protected $table='waitlist_process_logs';
    public $primaryKey='id';
    public $fillable=[
    	'process_log_id',
        'program_id',
        'enrollment_id',
        'grade',
    	'application_id',
    	'version',
    	'program_name',
        'total_seats',
    	'withdrawn_student',
        'black_withdrawn',
    	'white_withdrawn',
        'other_withdrawn',
        'waitlisted',
        'additional_seats',
        'available_slots',
        'slots_to_awards',
        'zoned_schools',
        'generated_by'
   	];

}
