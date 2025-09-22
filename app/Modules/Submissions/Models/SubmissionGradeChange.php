<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionGradeChange extends Model {

    //
    protected $table='grade_change';
    protected $primaryKey='id';
    protected $fillable=[
    	'submission_id',
        'old_grade',
    	'new',
    	'offer_slug',
    	'old_contract_file_name',
    	'old_contract_date',
    	'updated_by'
    ];

}
