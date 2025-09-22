<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionsStatusLog extends Model {

    //
    protected $table='submissions_status_log';
    public $primaryKey='id';
    public $fillable=[
    	'submission_id',
        'enrollment_id',
    	'old_status',
    	'new_status',
    	'comment',
    	'updated_by'
    ];

}
