<?php

namespace App\Modules\LateSubmission\Models;

use Illuminate\Database\Eloquent\Model;

class LateSubmissionEditCommunication extends Model {

    protected $table='late_submission_edit_communication';
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
