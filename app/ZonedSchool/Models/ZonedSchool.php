<?php

namespace App\Modules\ZonedSchool\Models;

use Illuminate\Database\Eloquent\Model;

class ZonedSchool extends Model {
	 protected $table='zone_address';
    protected $primaryKey='id';
    protected $fillable=[
         "bldg_num",
         "prefix_dir",
         "prefix_type",
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

    public function scopegetZonedSchoolList($query,$all,$flag){
        $query->where('zip','!=','');
        
        if($flag == 1){
            if(isset($all['order'][0]['column']) && $all['order'][0]['column']!=''){
                $sort_col=$all['order'][0]['column'];
                $sort_dir=$all['order'][0]['dir'];

                if($sort_col==0){
                    return $query->orderBy('bldg_num',$sort_dir)->skip($all['start'])->take($all['length'])->get();
                }
                if($sort_col==1){
                    return $query->orderBy('street_name',$sort_dir)->skip($all['start'])->take($all['length'])->get();
                }
                if($sort_col==2){
                    return $query->orderBy('street_type',$sort_dir)->skip($all['start'])->take($all['length'])->get();
                }
                if($sort_col==3){
                    return $query->orderBy('city',$sort_dir)->skip($all['start'])->take($all['length'])->get();
                }
                if($sort_col==4){
                    return $query->orderBy('zip',$sort_dir)->skip($all['start'])->take($all['length'])->get();
                }
            }else{
                return $query->skip($all['start'])->take($all['length'])->get();
            }
        }else{
            return $query->count();
        }
    }

}
