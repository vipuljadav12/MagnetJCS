<?php

namespace App\Modules\ZonedSchool\Models;

use Illuminate\Database\Eloquent\Model;

class ZonedAddressMaster extends Model {
	protected $table='zone_address_master';
    protected $primaryKey='id';
    protected $fillable=[
         "group_name",
         "user_id",
         "status",
         "district_id"
    ];
}
