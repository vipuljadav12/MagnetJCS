<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionsSelectionReportMaster extends Model {

    //
    protected $table='submissions_select_master_report';
    public $primaryKey='id';
    public $fillable=[
    	'submission_id',
        'application_id',
        'enrollment_id',
        'type',
        'version',
        'first_choice',
    	'second_choice',
    	'priority',
    	'committee_score',
    	'composite_score',
        'score_type',
        'final_status',
        'race_composition_update',
        'available_seats'
    ];

}
