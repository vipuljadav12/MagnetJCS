<?php

namespace App\Modules\Eligibility\Models;

use Illuminate\Database\Eloquent\Model;

class EligibilityContent extends Model {

    protected $table='eligibility_content';
    public $primaryKey='id';
    public $fillable=[
    	'eligibility_id',
    	'content'
    ];

}
