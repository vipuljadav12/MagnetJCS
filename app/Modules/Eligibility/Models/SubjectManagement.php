<?php

namespace App\Modules\Eligibility\Models;
use App\Modules\School\Models\Grade;
use Illuminate\Database\Eloquent\Model;

class SubjectManagement extends Model
{
        protected $table="subject_management";
        protected $primaryKey="id";
        protected $fillable=[
            'application_id',
            'grade',
            'english',
            'reading',
            'science',
            'social_studies',
            'math'
        ];
}