<?php

namespace App\Modules\Waitlist\Models;

use Illuminate\Database\Eloquent\Model;

class WaitlistIndividualAvailability extends Model {

    protected $table = "waitlist_individual_availability";
    protected $fillable = [
        "district_id",
    	"program_id",
    	"grade",
    	"waitlist_count",
    	"withdrawn_seats",
    ];
}
