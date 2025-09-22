<?php

namespace App\Modules\School\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
 {

   public $table='grade';

    protected $fillable = [
        'name',
    ];

    public function school()
    {
        return $this->hasMany('App\Modules\School\Models\School');
    }

}
