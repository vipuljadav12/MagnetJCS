<?php

namespace App\Modules\Form\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model {

    //
    protected $table = "field";

    protected $primaryKey = "field_id";

    protected $fillable = [
    	'form_field_id','field_property','field_value'
    ];

}