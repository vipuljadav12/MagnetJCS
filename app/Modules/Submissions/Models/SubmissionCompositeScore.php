<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionCompositeScore extends Model {

    //
    protected $table='submission_composite_score';
    public $traitField = "submission_id";
    public $additional = ['enrollment_id', 'application_id'];

    public $primaryKey='id';
    public $fillable=[
    	'submission_id',
    	'score'
    ];

}
