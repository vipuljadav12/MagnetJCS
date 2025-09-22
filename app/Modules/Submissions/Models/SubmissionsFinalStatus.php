<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionsFinalStatus extends Model {

    //
    protected $table='submissions_final_status';
    public $primaryKey='id';
    public $fillable=[
    	'submission_id',
        'enrollment_id',
        'application_id',
    	'first_choice_final_status',
    	'second_choice_final_status',
        'third_choice_final_status',
    	'first_waitlist_number',
    	'second_waitlist_number',
        'third_waitlist_number',
        'incomplete_reason',
        'first_choice_eligibility_reason',
        'second_choice_eligibility_reason',
        'third_choice_eligibility_reason',
        'first_offered_rank',
        'second_offered_rank',
        'third_offered_rank',
    	'first_waitlist_for',
    	'second_waitlist_for',
        'third_waitlist_for',
        'offer_slug',
        'first_offer_update_at',
        'second_offer_update_at',
        'third_offer_update_at',
        'contract_status',
        'contract_signed_on',
        'contract_name',
        'offer_status_by',
        'contract_status_by',
        'contract_mode',
        'first_offer_status',
        'second_offer_status',
        'third_offer_status',
        'manually_updated',
        'communication_sent',
        'communication_text',
        'version',
        'last_date_online_acceptance',
        'last_date_offline_acceptance'
    ];

}
