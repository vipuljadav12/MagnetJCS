<?php

namespace App\Modules\Module\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model {

    use SoftDeletes;
    protected $table = 'modules';


    protected $fillable = ['name','slug','display_name','sort','deleted_at'];

    

    public function scopeIndex($query){
    	return $query->whereNull('deleted_at')->orderBy("sort")->get();
    }

    public function scopeShow($query,$id){
    	return $query->where('id',$id)->first();
    }

}
