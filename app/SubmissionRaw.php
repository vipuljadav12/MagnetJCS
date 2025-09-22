<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Session;

class SubmissionRaw extends Model
{
    protected $table='submission_raw';
    protected $primaryKey='id';
    protected $fillable=[
         "application_id",
         "form_id",
         "formdata",
         'form_type'
    ];
}