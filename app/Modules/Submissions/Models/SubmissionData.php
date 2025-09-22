<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Session;
use App\Modules\Form\Models\Form;
use App\Modules\Program\Models\Program;
use DB;

class SubmissionData extends Model {

    //
    protected $table='submission_data';
    public $additional = [];
    public $date_fields = [];
    public $primaryKey='id';
    public $fillable=[
    	'submission_id',
    	'config_name',
    	'config_value'
    ];


}
