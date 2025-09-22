<?php

namespace App\Modules\Waitlist\Models;

use Illuminate\Database\Eloquent\Model;

class WaitlistEditCommunicationLog extends Model {

    protected $table='waitlist_edit_communication_log';
    protected $primaryKey='id';

    protected $fillable=[
      'communication_type',
      'district_id',
      'application_id',
      'email',
      'mail_body',
      'mail_subject',
      'status',
      'submission_id',
      'total_count',
      'file_name',
      'generated_by'
    ];
}
