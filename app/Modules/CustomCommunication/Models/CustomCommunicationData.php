<?php

namespace App\Modules\CustomCommunication\Models;

use Illuminate\Database\Eloquent\Model;

class CustomCommunicationData extends Model {

   protected $table='custom_communication_data';
    protected $primaryKey='id';

    protected $fillable=[
      'district_id',
      'template_id',
      'file_name',
      'total_count',
      'generated_by',
      'submission_id',
      'email_body',
      'email_subject',
      'email',
      'status',
      'type'
    ];

}
