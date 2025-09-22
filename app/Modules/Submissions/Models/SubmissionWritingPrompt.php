<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionWritingPrompt extends Model {

    //
    protected $table='submission_writing_prompt';
    protected $primaryKey='id';
    protected $fillable=[
    	'submission_id',
    	'data'
    ];

}
