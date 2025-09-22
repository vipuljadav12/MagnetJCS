<?php

namespace App\Modules\WritingPrompt\Models;

use Illuminate\Database\Eloquent\Model;

class WritingPromptDetail extends Model {

    protected $table='writing_prompt_detail';
    protected $primaryKey='id';

    protected $fillable=[
      'wp_id',
      'writing_prompt',
      'writing_sample'
    ];
}
