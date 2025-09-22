<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Session;

class ZoneAPI extends Model
{
    //
    protected $table='zone_address';
    protected $primaryKey='id';
    protected $fillable=[
         "bldg_num",
         "prefix_dir",
         "street_name",
         "street_type",
         "suffix_dir",
         "unit_info",
         "city",
         "race",
         "state",
         "zip",
         "elementary_school",
         "intermediate_school",
         "middle_school",
         "high_school"
    ];
}