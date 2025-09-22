<?php

namespace App\Modules\WritingPrompt\Models;

use Illuminate\Database\Eloquent\Model;

class EmailActivityLog extends Model {

    protected $table='email_activity_log';
    protected $primaryKey='id';

    protected $fillable=[
      'user_id',
      'district_id',
      'submission_id',
      'module',
      'program_id',
      'email_body',
      'email_subject',
      'email_to',
      'status'
    ];
}
