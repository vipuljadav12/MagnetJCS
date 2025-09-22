<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionsStatusUniqueLog extends Model {

    //
    protected $table='submissions_status_unique_log';
    public $primaryKey='id';
    public $fillable=[
    	'submission_id',
    	'old_status',
    	'new_status',
    	'updated_by'
    ];

}
