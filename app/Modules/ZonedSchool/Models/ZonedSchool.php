<?php

namespace App\Modules\ZonedSchool\Models;

use Illuminate\Database\Eloquent\Model;

class ZonedSchool extends Model {
	protected $table='zone_address';
    protected $primaryKey='id';
    protected $fillable=[
         "bldg_num",
         "zone_master_id",
         "prefix_dir",
         "prefix_type",
         "street_name",
         "street_type",
         "suffix_dir",
         "suffix_dir_full",
         "unit_info",
         "city",
         "race",
         "state",
         "zip",
         "elementary_school",
         "intermediate_school",
         "middle_school",
         "high_school",
         "added_by",
         "user_id",
         "district_id"
    ];

    public function scopegetZonedSchoolList($query,$all,$flag, $master_address_id = 1){
        $query->where('zip','!=','')->where('zone_master_id',$master_address_id);

        if($all['search']['value']!=''){
            $search = $all['search']['value'];
            $query->where('street_name','like',$search.'%')->orWhere('street_type','like',$search.'%')->orWhere('suffix_dir',$search)->orWhere('suffix_dir_full', $search)->orWhere('unit_info','like',$search.'%')->orWhere('city','like',$search.'%')->orWhere('zip','like',$search.'%');
        }
        
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
