<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionComment extends Model {

    //
    protected $table='submission_comments';
    protected $primaryKey='id';
    protected $fillable=[
    	'submission_id',
        'user_id',
    	'comment',
    	'submission_event'
    ];

}
