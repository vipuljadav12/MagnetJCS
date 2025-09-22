<?php

namespace App\Modules\Permission\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model {

    protected $table = 'roles_permissions';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'role_id',
        'permission_id',
    ];

    public function scopeCreate($query,$data){
    	return $query->create($data);
    }

}
