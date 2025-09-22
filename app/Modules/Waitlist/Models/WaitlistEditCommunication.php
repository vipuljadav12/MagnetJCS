<?php

namespace App\Modules\Waitlist\Models;

use Illuminate\Database\Eloquent\Model;

class WaitlistEditCommunication extends Model {

    protected $table='waitlist_edit_communication';
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
