<?php

namespace App\Modules\GiftedStudents\Models;

use Illuminate\Database\Eloquent\Model;

class GiftedStudents extends Model {

	protected $table = "gifted_students";

	protected $primaryKey = 'id';

    protected $fillable = [
        'stateID',
        'first_name',
        'last_name',
        'district_id',
        'enrollment_id',
        'admin'
    ];
}
