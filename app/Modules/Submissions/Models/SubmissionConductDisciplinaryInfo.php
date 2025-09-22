<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionConductDisciplinaryInfo extends Model {

    public $timestamps = false;
    protected $table='submission_conduct_discplinary_info';
    public $traitField = "submission_id";
    public $additional = ['enrollment_id', 'application_id'];

    public $primaryKey='id';
    public $fillable=[
    	'submission_id',
    	'stateID',
    	'b_info',
    	'c_info',
    	'd_info',
    	'e_info',
    	'susp',
    	'susp_days',
        'class3_incident'
    ];
}
