<?php

namespace App\Modules\Form\Models;

use Illuminate\Database\Eloquent\Model;

class FormBuild extends Model 
{
    protected $table='form_build';
    protected $primarykey='id';
    protected $fillable=[
    	'form_id',
        'page_id',
    	'field_id',
        'type',
        'placeholder',
        'label',
        'sort'
    ]; 

}
