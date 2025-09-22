<?php

namespace App\Modules\WritingPrompt\Models;

use Illuminate\Database\Eloquent\Model;

class WritingPromptDetailLog extends Model {

    protected $table='writing_prompt_detail_log';
    protected $primaryKey='id';

    protected $fillable=[
      'wp_id',
      'writing_prompt',
      'writing_sample'
    ];
}
