<?php

namespace App\Modules\DeniedSpace\Models;

use Illuminate\Database\Eloquent\Model;

class DeniedSpace extends Model {

    //
    protected $table='expire_waitlist_settings';
    public $primaryKey='id';
    public $fillable=[
        'form_id',
        'enrollment_id',
        'district_id',
        'waitlist_end_date',
        'letter_body',
      	'mail_subject',
      	'mail_body',
      	'cron_executed'
    ];

}
