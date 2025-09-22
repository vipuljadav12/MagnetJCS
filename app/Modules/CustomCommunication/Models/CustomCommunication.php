<?php

namespace App\Modules\CustomCommunication\Models;

use Illuminate\Database\Eloquent\Model;

class CustomCommunication extends Model {

    protected $table='custom_communication';
    public $primaryKey='id';

    public $fillable=[
      'district_id' ,
      'template_name' ,
      'enrollment_id' ,
      'program' ,
      'grade' ,
      'submission_status' ,
      'mail_subject',
      'mail_body',
      'letter_subject',
      'status',
      'letter_body'
    ];

}
