<?php

namespace App\Modules\WritingPrompt\Models;

use Illuminate\Database\Eloquent\Model;

class WritingPromptLog extends Model {

    protected $table='writing_prompt_log';
    protected $primaryKey='id';

    protected $fillable=[
      'submission_id',
      'program_id',
      'start_time',
      'end_time'
    ];
}
