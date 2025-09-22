<?php

namespace App\Modules\District\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model {

    //
    protected $table='district';
    protected $primaryKey='id';

    protected $fillable=[
      'district_slug' ,
      'name' ,
      'short_name' ,
      'address' ,
      'city' ,
      'state' ,
      'zipcode',
      'district_logo',
      'magnet_program_logo',
      'district_url',
      'theme_color',
      'magnet_point_contact',
      'magnet_point_contact_title',
      'magnet_point_contact_email',
      'magnet_point_contact_phone',
      'desegregation_compliance',
      'zone_requirements',
      'birthday_cutoff_requirement',
      'sis_connection',
      'sis_api_url',
      'sis_username',
      'sis_password',
      'sis_application_key',
      'school_sis_contact' ,
      'school_sis_contact_title',
      'school_sis_contact_email',
      'school_sis_contact_phone',
      'zone_api',
      'internal_zone_api_url',
      'internal_zone_point_contact',
      'internal_zone_point_title',
      'internal_zone_point_email',
      'internal_zone_point_phone',
      'external_organization_name',
      'external_organization_url',
      'external_organization_point_contact',
      'external_organization_point_title',
      'external_organization_point_email',
      'external_organization_point_phone',
      'billing_start_date',
      'billing_end_date',
      'notify_renewal_date',
      'lottery_number_display',
      'zone_api_existing',
      'mcpss_zone_api',
      'district_timezone',
      'status',
      'feeder', //'current_school',
      'sibling',
      'magnet_employee', //'district_employee',
      'current_over_new',
      'magnet_student',
    ];

}
