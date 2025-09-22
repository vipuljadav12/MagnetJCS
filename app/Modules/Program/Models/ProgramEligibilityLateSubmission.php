<?php
namespace App\Modules\Program\Models;


use Illuminate\Database\Eloquent\Model;

class ProgramEligibilityLateSubmission extends Model
{
    protected $table = 'program_eligibility_late_submission';
    public $primaryKey = 'id';
    public $traitField = "program_id";
    public $fillable = [
        'program_id',
        'eligibility_type',
        'determination_method',
        'eligibility_define',
        'assigned_eigibility_name',
        'weight',
        'grade_lavel_or_recommendation_by',
        'status',
        'created_at',
        'updated_at',
        'status',
    ];

}