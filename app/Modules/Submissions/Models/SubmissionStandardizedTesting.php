<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionStandardizedTesting extends Model {

    //
    protected $table='submission_standardized_testing';
    protected $primaryKey='id';
    protected $fillable=[
    	'submission_id',
    	'subject',
    	'method',
    	'data',
    	'choice'
    ];

}
