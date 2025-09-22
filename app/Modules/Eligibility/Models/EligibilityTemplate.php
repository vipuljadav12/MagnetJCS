<?php

namespace App\Modules\Eligibility\Models;

use Illuminate\Database\Eloquent\Model;

class EligibilityTemplate extends Model 
{
  protected $table='eligibility_template';
  protected $primaryKey='id';
  protected $fillable=[
  	'name',
  	'type',
  	'district_id',
  	'status'
  ];
}