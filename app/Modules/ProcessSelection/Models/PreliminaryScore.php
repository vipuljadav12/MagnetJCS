<?php

namespace App\Modules\ProcessSelection\Models;

use Illuminate\Database\Eloquent\Model;

class PreliminaryScore extends Model {

    protected $table='submission_preliminary_score';
    protected $primarykey='id';
    public $fillable=[
    	'submission_id',
    	'score_value',
    	'thresold_val'
    ]; 

}
