<?php

namespace App\Modules\LateSubmission\Models;

use Illuminate\Database\Eloquent\Model;

class LateSubmissionAvailabilityLog extends Model {

    protected $table = "late_submission_availability_log";
    protected $fillable = [
    	"program_id",
    	"district_id",
    	"grade",
    	"withdrawn_seats",
    	"user_id",
    	"year"
    ];
}
