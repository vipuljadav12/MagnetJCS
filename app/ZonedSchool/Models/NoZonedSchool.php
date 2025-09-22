<?php

namespace App\Modules\ZonedSchool\Models;

use Illuminate\Database\Eloquent\Model;

class NoZonedSchool extends Model {
	protected $table='no_zoned_school_found';
    protected $primaryKey='id';
    protected $fillable=[
         "street_address",
         "city",
         "zip"
    ];

}
