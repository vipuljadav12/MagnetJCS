<?php

namespace App\Modules\Form\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model {

    //
    protected $table='form';
    protected $primarykey='id';
    protected $fillable=[

    	'district_id',
    	'name',
    	'url',
        'confirmation_style',
        'number_of_pages',
    	'description',
    	'thank_you_url',
    	'thank_you_msg',
    	'to_mail',
    	'show_logo',
    	'captcha',
        'form_source_code',
    	'status',
    	'no_of_pages'
    ]; 

}
