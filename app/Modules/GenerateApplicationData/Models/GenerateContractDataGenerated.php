<?php

namespace App\Modules\GenerateApplicationData\Models;

use Illuminate\Database\Eloquent\Model;

class GenerateContractDataGenerated extends Model {

    protected $table='contract_data_generated';
	protected $primaryKey='id';
	protected $fillable=[
	    'enrollment_id',
	    'grade',
		'status',
		'file_name',
		'total_records',
	];

}
