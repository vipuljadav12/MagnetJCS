<?php

namespace App\Modules\LateSubmission\Models;

use Illuminate\Database\Eloquent\Model;

class LateSubmissionEditCommunicationLog extends Model {

    protected $table='late_submission_edit_communication_log';
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
