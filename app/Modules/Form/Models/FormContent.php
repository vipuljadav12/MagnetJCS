<?php

namespace App\Modules\Form\Models;

use Illuminate\Database\Eloquent\Model;

class FormContent extends Model 
{
    protected $table='form_content';
    protected $primarykey='id';
    protected $fillable=[
    	'form_id',
    	'build_id',
        'field_property',
        'field_value',
        'sort_option',
        'label',
        'sort'
    ]; 
}
