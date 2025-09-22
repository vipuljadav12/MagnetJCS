<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionManualEmail extends Model {

    //
    protected $table='submissions_manual_email_sent';
    public $primaryKey='id';
    public $fillable=[
    	'submission_id',
        'process_type'
    ];

}
