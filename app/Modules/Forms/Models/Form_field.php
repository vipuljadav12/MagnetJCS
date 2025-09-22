<?php

namespace App\Modules\Form\Models;

use Illuminate\Database\Eloquent\Model;

class Form_field extends Model {

    //
    protected $table = "form_field";

    protected $primaryKey = "form_field_id";

    protected $fillable = [
    	'form_id','type_id','sort'
    ];

}