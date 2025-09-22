<?php

namespace App\Modules\Role\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

    protected $table = 'roles';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'status',
    ];

    public function permissions()
    {
        return $this->belongsToMany('\App\Modules\Permission\Models\Permission','roles_permissions','role_id','permission_id');
    }

    public function scopeIndex($query){
        // return $query->where('status','!=','T')->get();
        return $query->whereNotIn('id',[1])->where('status','!=','T')->get();
    }
}
