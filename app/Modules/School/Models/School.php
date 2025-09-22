<?php

namespace App\Modules\School\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
 {

   public $table='school';

    public $fillable = [
        'name','grade_id','district_id','magnet','zoning_api_name','sis_name',
    ];

    public function grade()
    {
        return $this->belongsTo('App\Modules\School\Models\Grade');
    }
}
