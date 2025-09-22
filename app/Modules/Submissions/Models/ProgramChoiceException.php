<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramChoiceException extends Model {

    //
    protected $table='program_choice_exception';
    public $primaryKey='id';
    public $fillable=[
    	'program_id',
    	'grade',
        'display_name'
    ];

}
