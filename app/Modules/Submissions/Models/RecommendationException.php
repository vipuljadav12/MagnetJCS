<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationException extends Model {

    //
    protected $table='recommendation_exception';
    public $primaryKey='id';
    public $fillable=[
    	'program_id',
    	'eligibility_id',
        'grade',
    	'subject_teacher',
    	'user_id'
    ];

}
