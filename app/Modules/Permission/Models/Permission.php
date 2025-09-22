<?php

namespace App\Modules\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Role\Models\Role;
use App\Modules\Module\Models\Module;

class Permission extends Model {

    protected $table = 'permissions';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'slug',
        'display_name',
        'module_id',
        'status',
    ];

    public function scopeCreate($query,$data){
    	return $query->create($data);
    }

    public function modules(){
    	return $this->hasMany(Module::class,'id','module_id');
    }

    /*public function roles()
    {
        return $this->belongsToMany('Role','roles_permissions','role_id','permission_id');
    }*/

}
