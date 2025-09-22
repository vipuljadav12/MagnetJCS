<?php

namespace App\Modules\Form\Models;

use Illuminate\Database\Eloquent\Model;

class PageForm extends Model {

    //
    protected $table = "page_form";

    protected $primaryKey = "page_form_id";

    protected $fillable = [
    	'form_id','page_content_id','layout'
    ];

}
