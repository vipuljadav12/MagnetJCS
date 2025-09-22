<?php

namespace App\Modules\AuditTrailData\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrailData extends Model {

   	protected $table = "audit_trail_data";
   	protected $fillable = [
   		"user_id",
   		"old_values",
   		"new_values",
   		'module',
   		"changed_fields",
      "enrollment_id",
      "application_id"
   	];
   	public function getOldValuesAttribute($value='')
   	{
   		return json_decode($value);
   	}
   	public function getNewValuesAttribute($value='')
   	{
   		return json_decode($value);
   	}
   	public function getChangedFieldsAttribute($value='')
   	{
   		return json_decode($value);
   	}
   	public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
    /*public function scopeGetModules($query)
    {
    	return $query->first();
    }*/
}
