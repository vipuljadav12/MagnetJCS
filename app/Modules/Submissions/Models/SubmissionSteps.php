<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionSteps extends Model {

    //
    protected $table='submission_steps';
    protected $primaryKey='id';
    protected $fillable=[
    	'session_id',
    	'step_no'
    ];

}
