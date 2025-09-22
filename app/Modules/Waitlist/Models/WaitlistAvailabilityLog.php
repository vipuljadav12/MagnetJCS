<?php

namespace App\Modules\Waitlist\Models;

use Illuminate\Database\Eloquent\Model;

class WaitlistAvailabilityLog extends Model {

    protected $table = "waitlist_availability_log";
    protected $fillable = [
    	"program_id",
    	"district_id",
    	"grade",
    	"withdrawn_seats",
    	"user_id",
    	"year"
    ];
}
