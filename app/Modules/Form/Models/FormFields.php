<?php

namespace App\Modules\Form\Models;

use Illuminate\Database\Eloquent\Model;

class FormFields extends Model 
{
    protected $table='form_fields';
    protected $primarykey='id';
    protected $fillable=[
    	'name',
        'icon',
        'type'
    ]; 

}
