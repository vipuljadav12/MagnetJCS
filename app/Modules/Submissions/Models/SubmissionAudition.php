<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionAudition extends Model {

    //
    protected $table='submissionaudition';
    protected $primaryKey='id';
    protected $fillable=[
    	'submission_id',
    	'data'
    ];

}
