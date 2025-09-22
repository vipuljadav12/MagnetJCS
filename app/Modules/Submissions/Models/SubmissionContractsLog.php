<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionContractsLog extends Model {

    //
    protected $table='contract_logs';
    protected $primaryKey='id';
    protected $fillable=[
    	'submission_id',
        'ip_address',
    	'city',
    	'country',
    	'state',
    	'browser'
    ];

}
