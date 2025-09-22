<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class LateSubmissionsStatusUniqueLog extends Model {

    //
    protected $table='late_submissions_status_unique_log';
    public $primaryKey='id';
    public $fillable=[
    	'submission_id',
    	'old_status',
    	'new_status',
    	'updated_by',
    	'version'
    ];

}
