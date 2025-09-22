<?php

namespace App\Modules\Application\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationConfiguration extends Model {

    //
    public $timestamps = false;
	protected $table='application_configuration';
	public $primary_key='id';
	public $traitField = "application_id";
	public $fillable=[
		'application_id',
		'active_screen',
		'active_email',
		'active_email_subject',
		'pending_screen',
		'pending_email',
		'pending_email_subject',
		'active_screen_title',
		'active_screen_subject',
		'pending_screen_title',
		'pending_screen_subject',
		'grade_cdi_welcome_text',
		'grade_cdi_confirm_text'
	];
}
