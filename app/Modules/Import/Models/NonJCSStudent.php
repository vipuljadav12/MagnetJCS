<?php

namespace App\Modules\Import\Models;
use Illuminate\Database\Eloquent\Model;

class NonJCSStudent extends Model {
    protected $table='non_jcs_students';
    public $primaryKey='id';
    public $fillable=[
        'district_id',
        'enrollment_id',
        'ssid',
        'first_name',
        'last_name',
        'birthday',
        'current_school',
        'current_grade',
        'transfer_type',
        'approved_zoned_school',
    ];
}
