<?php

namespace App\Modules\DistrictConfiguration\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictConfiguration extends Model {

    //
    protected $table='district_global_setting';
    protected $primaryKey='id';
    protected $fillable=[
      'district_id',
      'enrollment_id',
      'name',
      'value',
    ];

}
