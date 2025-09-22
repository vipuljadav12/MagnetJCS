<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Session;

class SubmissionDocuments extends Model
{
    //
    protected $table='submission_documents';
    public $primaryKey='id';
    public $fillable=[
        "submission_id",
        "doc_grade",
        "doc_cdi",
        "grade_confirmed",
        "cdi_confirmed",
        "created_at",
        "updated_at",
    ];
}