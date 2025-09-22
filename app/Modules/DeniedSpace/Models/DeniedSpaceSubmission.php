<?php

namespace App\Modules\DeniedSpace\Models;

use Illuminate\Database\Eloquent\Model;

class DeniedSpaceSubmission extends Model {

    protected $table='denied_updated_submissions';
    public $primaryKey='id';
    public $fillable=[
        'form_id',
        'enrollment_id',
        'submission_id',
        'choice',
        'program_id'
    ];

}
