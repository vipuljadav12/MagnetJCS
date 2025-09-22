<?php

namespace App\Modules\LateSubmission\Models;

use Illuminate\Database\Eloquent\Model;

class LateSubmissionIndividualAvailability extends Model {

    protected $table = "late_submission_individual_availability";
    protected $fillable = [
        "district_id",
    	"program_id",
    	"grade",
    	"waitlist_count",
    	"withdrawn_seats",
    ];
}
