<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Session;
use App\Modules\Form\Models\Form;
use App\Modules\Program\Models\Program;
use DB;

class SubmissionRecommendation extends Model {

    //
    protected $table='submission_recommendation';
    public $additional = [];
    public $date_fields = [];
    public $primaryKey='id';
    public $fillable=[
    	'submission_id',
        'config_value',
        'answer',
        'teacher_name',
        'teacher_email',
        'avg_score',
        'comment',
        'created_at',
    	'updated_at'
    ];


}
