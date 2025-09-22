<?php

namespace App\Modules\EditCommunication\Models;

use Illuminate\Database\Eloquent\Model;

class EditCommunication extends Model {

    protected $table='edit_communication';
    protected $primaryKey='id';

    protected $fillable=[
      'status',
      'letter_body',
      'application_id',
      'mail_body',
      'mail_subject',
      'district_id',
      'created_by'
    ];
}
