<?php

namespace App\Modules\HeaderFooter\Models;

use Illuminate\Database\Eloquent\Model;

class HeaderFooter extends Model {

   	protected $table='district_config';
    protected $primaryKey='id';

    protected $fillable=[
      'district_id' ,
      'config_name' ,
      'config_value' ,
      'status'
    ];
    public $timestamps = false;

}
