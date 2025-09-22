<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionTestScore extends Model {

    //
    protected $table='submission_test_score';
    protected $primaryKey='id';
    protected $fillable=[
    	'submission_id',
    	'program_id',
    	'program_id',
    	'test_score_name',
    	'test_score_value',
    	'test_score_rank'
    ];

}
