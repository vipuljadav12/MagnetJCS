<?php

namespace App\Modules\Permission\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionModule extends Model {

    protected $table = 'permision_module';
    

    protected $fillable = [
        'name',
        'status',
    ];


}
