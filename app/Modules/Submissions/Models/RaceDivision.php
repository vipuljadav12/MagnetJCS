<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class RaceDivision extends Model {

    //
    protected $table='race_division';
    public $primaryKey='id';
    public $fillable=[
    	'actual_race',
    	'calculated_race'
    ];

}
