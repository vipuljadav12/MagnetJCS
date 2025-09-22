<?php

namespace App\Modules\Configuration\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model {

    //
    protected $table='district_config';
    public $primaryKey='id';

    public $fillable=[
      'district_id',
      'config_name',
      'display_name',
      'config_type',
      'config_value' ,
      'status'
    ];
    public $timestamps = false;

}
