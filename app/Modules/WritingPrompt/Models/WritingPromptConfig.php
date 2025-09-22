<?php

namespace App\Modules\WritingPrompt\Models;

use Illuminate\Database\Eloquent\Model;

class WritingPromptConfig extends Model {

    protected $table='writing_prompt_config';
    protected $primaryKey='id';

    protected $fillable=[
      'district_id',
      'duration',
      'intro_txt',
      'mail_subject',
      'mail_body'
    ];
}
