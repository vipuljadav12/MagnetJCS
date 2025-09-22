<?php

namespace App\Modules\SetAvailability\Models;

use Illuminate\Database\Eloquent\Model;

class WaitlistAvailability extends Model {

    protected $table = "waitlist_availability";
    protected $fillable = [
    	"program_id",
    	"district_id",
    	"grade",
    	"withdrawn_seats",
    	"last_date_waitlist_online_acceptance",
    	"last_date_waitlist_offline_acceptance",
    	"year"
    ];
}
