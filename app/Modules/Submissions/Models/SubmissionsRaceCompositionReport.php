<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionsRaceCompositionReport extends Model {

    //
    protected $table='submissions_race_composition_report';
    public $primaryKey='id';
    public $fillable=[
    	'program_group',
        'application_id',
        'enrollment_id',
        'version',
        'type',
        'black',
    	'white',
    	'other'
    ];

}
