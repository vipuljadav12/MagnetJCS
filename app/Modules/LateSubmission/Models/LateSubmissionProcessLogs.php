<?php

namespace App\Modules\LateSubmission\Models;

use Illuminate\Database\Eloquent\Model;

class LateSubmissionProcessLogs extends Model {

    //
    protected $table='late_submission_process_logs';
    public $primaryKey='id';
    public $fillable=[
    	'process_log_id',
        'program_id',
        'grade',
        'enrollment_id',
        'application_id',
        'version',
        'program_name',
        'total_seats',
        'withdrawn_student',
        'black_withdrawn',
        'white_withdrawn',
        'other_withdrawn',
        'waitlisted',
        'late_application_count',
        'additional_seats',
        'available_slots',
        'slots_to_awards',
        'zoned_schools',
        'generated_by'
   	];

}