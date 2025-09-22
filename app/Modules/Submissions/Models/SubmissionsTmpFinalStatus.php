<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionsTmpFinalStatus extends Model {

    //
    protected $table='tmp_submission_waitlist_status';
    public $primaryKey='id';
    public $fillable=[
    	'submission_id',
        'choice_type',
        'status',
    	'offer_slug',
        'reason'
    ];

}
