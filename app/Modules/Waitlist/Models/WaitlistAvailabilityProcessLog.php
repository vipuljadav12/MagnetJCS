<?php

namespace App\Modules\Waitlist\Models;

use Illuminate\Database\Eloquent\Model;

class WaitlistAvailabilityProcessLog extends Model {

    protected $table = "availability_process_logs";
    protected $fillable = [
    	"program_id",
    	"grade",
    	"waitlist_count",
    	"withdrawn_seats",
    	"offered_count",
    	"total_capacity",
    	"version",
        "type"
    ];
}
