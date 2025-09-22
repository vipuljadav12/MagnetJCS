<?php

namespace App\Modules\Form\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model {

    //
    protected $table = "forms";

    protected $primaryKey = "form_id";

    protected $fillable = [
    	'form_title','form_description','availability_start','availability_end','not_availability_message','user_id','form_status'
    ];

}
