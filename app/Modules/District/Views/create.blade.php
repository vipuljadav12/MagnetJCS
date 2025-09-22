@extends('layouts.admin.app')
@section('title')Add District | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Add District</div>
        <div class=""><a href="{{url('admin/District')}}" class="btn btn-sm btn-secondary" title="">Back</a></div>
    </div>
</div>
@include("layouts.admin.common.alerts")
<form action="{{url('admin/District/store')}}" method="post" name="add_district" enctype= "multipart/form-data">
    {{csrf_field()}}
    <div class="">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="tabbing1-tab" data-toggle="tab" href="#tabbing1" role="tab" aria-controls="tabbing1" aria-selected="true">General</a></li>
            <li class="nav-item"><a class="nav-link" id="tabbing2-tab" data-toggle="tab" href="#tabbing2" role="tab" aria-controls="tabbing2" aria-selected="true">API</a></li>
            <li class="nav-item"><a class="nav-link" id="tabbing3-tab" data-toggle="tab" href="#tabbing3" role="tab" aria-controls="tabbing3" aria-selected="true">Settings</a></li>
        </ul>
        <div class="tab-content bordered" id="myTabContent">
            <div class="tab-pane fade show active" id="tabbing1" role="tabpanel" aria-labelledby="tabbing1-tab">
                <div class="">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="card shadow">
                                <div class="card-header">District Information</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">District Full Name : </label>
                                        <div class=""><input type="text" class="form-control"name="name" value="{{old('name')}}"></div>
                                        @if($errors->first('name'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('name')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">District Short Name : </label>
                                        <div class=""><input type="text" class="form-control" name="short_name" value="{{old('short_name')}}"></div>
                                        @if($errors->first('short_name'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('short_name')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Address : </label>
                                        <div class=""><input type="text" class="form-control" name="address" value="{{old('address')}}"></div>
                                        @if($errors->first('address'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('address')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">City : </label>
                                        <div class=""><input type="text" class="form-control" name="city" value="{{old('city')}}"></div>
                                        @if($errors->first('city'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('city')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">State : </label>
                                        <div class=""><input type="text" class="form-control" name="state" value="{{old('state')}}"></div>
                                        @if($errors->first('state'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('state')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">ZIP Code : </label>
                                        <div class=""><input type="text" class="form-control" id="zipcode" name="zipcode" value="{{old('zipcode')}}" maxlength="6"></div>
                                        @if($errors->first('zipcode'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('zipcode')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">District Logo : </label>
                                        <div class="row">
                                            <div class="col-12 col-md-7"><input type="file" class="form-control" id="district_logo" name="district_logo" value="{{old('district_logo')}}" accept="image/*"></div>
                                            <div class="col-12 col-md-5">
                                                <div>
                                                    <img src="{{url('/resources/assets/admin/images/spacer-logo.png')}}" alt="Logo" title="" width="400"  id="district_logo_img" class="img-thumbnail mr-3" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($errors->first('district_logo'))
                                        <div class="mb-1 text-danger">
                                            {{ $errors->first('district_logo')}}
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label class="control-label">Magnet Program Logo : </label>
                                        <div class="row">
                                            <div class="col-12 col-md-7"><input type="file" class="form-control" name="magnet_program_logo" value="{{old('magnet_program_logo')}}" accept="image/*"></div>
                                            <div class="col-12 col-md-5">
                                                <div>
                                                    <img src="{{url('/resources/assets/admin/images/spacer-logo.png')}}" alt="Logo" title="" width="400" id="magnet_prog_img" class="img-thumbnail mr-3">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($errors->first('magnet_program_logo'))
                                        <div class="mb-1 text-danger">
                                            {{ $errors->first('magnet_program_logo')}}
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label class="control-label">District URL : </label>
                                        <div class="d-flex align-items-end"><input type="text" class="form-control" id="district_slug" name="district_slug" value="{{old('district_slug')}}">.{{Request::getHost()}}</div>
                                        @if($errors->first('district_slug'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('district_slug')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Theme Color : </label>
                                        <div class="">
                                            <div class="input-group mb-3">
                                                <input id="cp2" type="text" class="form-control" value="{{old('theme_color')}}" name="theme_color" maxlength="7">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i id="chgcolor"  class="color-box" style="background-color: #5c6771;"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        @if($errors->first('theme_color'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('theme_color')}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="card shadow">
                                <div class="card-header">District Magnet Program</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">District Magnet Program Point of Contact : </label>
                                        <div class=""><input type="text" class="form-control" name="magnet_point_contact" value="{{old('magnet_point_contact')}}"></div>
                                        @if($errors->first('magnet_point_contact'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('magnet_point_contact')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Title : </label>
                                        <div class=""><input type="text" class="form-control" name="magnet_point_contact_title" value="{{old('magnet_point_contact_title')}}"></div>
                                        @if($errors->first('magnet_point_contact_title'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('magnet_point_contact_title')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Email : </label>
                                        <div class=""><input type="email" class="form-control" name="magnet_point_contact_email" value="{{old('magnet_point_contact_email')}}"></div>
                                        @if($errors->first('magnet_point_contact_email'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('magnet_point_contact_email')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Phone # : </label>
                                        <div class=""><input type="text" class="form-control" name="magnet_point_contact_phone" value="{{old('magnet_point_contact_phone')}}"></div>
                                        @if($errors->first('magnet_point_contact_phone'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('magnet_point_contact_phone')}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow">
                                <div class="card-header">District Timezone</div>
                                <div class="card-body">
                                    <label class="control-label">Select Timezone : </label>
                                        <div class="">
                                            @php
                                                $timezones = config('variables.timezones');
                                                $select_timezone = "US/Central";
                                            @endphp
                                            <select class="form-control custom-select" name="district_timezone" id="district_timezone">
                                                @foreach($timezones as $key=>$value)
                                                    <option value="{{$key}}" @if($key==$select_timezone) selected @endif>{{$value}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                </div>
                            </div>
                            <div class="card shadow">
                                <div class="card-header">Billing Details</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">Billing Start Date : </label>
                                        <div class=""><input type="text" class="form-control mydatepicker" value="{{old('billing_start_date')}}" name="billing_start_date" data-date-format="mm/dd/yy"></div>
                                    </div>
                                    @if($errors->first('billing_start_date'))
                                        <div class="mb-1 text-danger">
                                            {{$errors->first('billing_start_date')}}
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label class="control-label">Billing End Date : </label>
                                        <div class=""><input type="text" class="form-control mydatepicker01" value="{{old('billing_end_date')}}" name="billing_end_date"></div>
                                        @if($errors->first('billing_start_date'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('billing_start_date')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Notify Renewal Date : </label>
                                        <div class="">
                                            <select class="form-control custom-select" name="notify_renewal_date">
                                                <option value="1 Week" {{old('notify_renewal_date')=='1 Weeks'?'selected':''}}>1 Week</option>
                                                <option value="2 Weeks" {{old('notify_renewal_date')=='2 Weeks'?'selected':''}}>2 Weeks</option>
                                                <option value="3 Weeks" {{old('notify_renewal_date')=='3 Weeks'?'selected':''}}>3 Weeks</option>
                                                <option value="4 Weeks" {{old('notify_renewal_date')=='4 Weeks'?'selected':''}}>4 Weeks</option>
                                            </select>
                                        </div>
                                        @if($errors->first('notify_renewal_date'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('notify_renewal_date')}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tabbing2" role="tabpanel" aria-labelledby="tabbing2-tab">
                <div class="">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="card shadow">
                                <div class="card-header">Student Information System (SIS) API</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">Select SIS Connection : </label>
                                        <div class="">
                                            <select class="form-control custom-select" name="sis_connection" id="sis_connection">
                                                <option value="iNow" {{old('sis_connection')=='iNoew'?'selected':''}}>iNow</option>
                                                <option value="Power Schools" {{old('sis_connection')=='Power Schools'?'selected':''}}>Power Schools</option>
                                            </select>
                                        </div>
                                        @if($errors->first('sis_connection'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('sis_connection')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">API URL : </label>
                                        <div class=""><input type="text" class="form-control" value="{{old('sis_api_url')}}" name="sis_api_url"></div>
                                        @if($errors->first('sis_api_url'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('sis_api_url')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Username : </label>
                                        <div class=""><input type="text" class="form-control" value="{{old('sis_username')}}" name="sis_username"></div>
                                        @if($errors->first('sis_username'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('sis_username')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Password : </label>
                                        <div class=""><input type="text" class="form-control" value="{{old('sis_password')}}" name="sis_password"></div>
                                        @if($errors->first('sis_password'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('sis_password')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group sis_api_application_key">
                                        <label class="control-label">Application Key : </label>
                                        <div class=""><input type="text" class="form-control" value="{{old('sis_application_key')}}" name="sis_application_key"></div>
                                        @if($errors->first('sis_application_key'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('sis_application_key')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">School SIS Point of Contact : </label>
                                        <div class=""><input type="text" class="form-control" value="{{old('school_sis_contact')}}" name="school_sis_contact"></div>
                                        @if($errors->first('school_sis_contact'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('school_sis_contact')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Title : </label>
                                        <div class=""><input type="text" name="school_sis_contact_title" class="form-control" value="{{old('school_sis_contact_title')}}"></div>
                                        @if($errors->first('school_sis_contact_title'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('school_sis_contact_title')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Email : </label>
                                        <div class=""><input type="text" name="school_sis_contact_email" class="form-control" value="{{old('school_sis_contact_email')}}"></div>
                                        @if($errors->first('school_sis_contact_email'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('school_sis_contact_email')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Phone # : </label>
                                        <div class=""><input type="text" class="form-control" value="{{old('school_sis_contact_phone')}}" name="school_sis_contact_phone"></div>
                                        @if($errors->first('school_sis_contact_phone'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('school_sis_contact_phone')}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="card shadow">
                                <div class="card-header">Internal District Zone API</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">URL : </label>
                                        <div class="d-flex align-items-end"><input type="text" name="internal_zone_api_url" class="form-control" value="{{old('internal_zone_api_url')}}"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Internal District Zone Point of Contact : </label>
                                        <div class=""><input type="text" class="form-control" name="internal_zone_point_contact" value="{{old('internal_zone_point_contact')}}"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Title : </label>
                                        <div class=""><input type="text" class="form-control" value="{{old('internal_zone_point_title')}}" name="internal_zone_point_title"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Email : </label>
                                        <div class=""><input type="text" class="form-control" value="{{old('internal_zone_point_email')}}" name="internal_zone_point_email"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Phone # : </label>
                                        <div class=""><input type="text" class="form-control" value="{{old('internal_zone_point_phone')}}" name="internal_zone_point_phone"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow">
                                <div class="card-header">External Organization Zone API</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">Organization Name : </label>
                                        <div class=""><input type="text" class="form-control" value="{{old('external_organization_name')}}" name="external_organization_name"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">URL : </label>
                                        <div class="d-flex align-items-end"><input type="text" class="form-control" name="external_organization_url" value="{{old('external_organization_url')}}"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">External Organization Zone API Point of Contact : </label>
                                        <div class=""><input type="text" class="form-control" value="{{old('external_organization_point_contact')}}" name="external_organization_point_contact"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Title : </label>
                                        <div class=""><input type="text" class="form-control" value="{{old('external_organization_point_title')}}" name="external_organization_point_title"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Email : </label>
                                        <div class=""><input type="email" class="form-control" value="{{old('external_organization_point_email')}}" name="external_organization_point_email"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Phone # : </label>
                                        <div class=""><input type="text" class="form-control" value="{{old('external_organization_point_phone')}}" name="external_organization_point_phone"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tabbing3" role="tabpanel" aria-labelledby="tabbing3-tab">
                <div class="">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="card shadow">
                                <div class="card-header">Lottery Number</div>
                                <div class="card-body">
                                    <div class="form-group d-flex justify-content-between pt-5">
                                        <label for="" class="control-label">Display Lottery Number : </label>
                                        <div class="">
                                            <input id="chk_55" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="lottery_number_display" checked />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow">
                                <div class="card-header">Compliance</div>
                                <div class="card-body">
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="" class="control-label">Desegregation Compliance Active : </label>
                                        <div class="">
                                            <input id="chk_02" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="desegregation_compliance" checked />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow">
                                <div class="card-header">Submission Eligibility Requirements</div>
                                <div class="card-body">
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="" class="control-label">Zone Requirements Active : </label>
                                        <div class=""><input id="chk_0" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="zone_requirements" checked /></div>
                                    </div>
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="" class="control-label">Birthday Cutoff Requirements Active : </label>
                                        <div class=""><input id="chk_01" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="birthday_cutoff_requirement" checked /></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="card shadow">
                                <div class="card-header">Zone API</div>
                                <div class="card-body">
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="" class="control-label">Zone API (Non-Current Students) : </label>
                                        <div class=""><input id="chk_03" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="zone_api" checked /></div>
                                    </div>
                                    <div  id="mcpss_zone">
                                        <div class="form-group justify-content-between d-flex">
                                            <label for="" class="control-label">MCPSS Employee Zone API : </label>
                                            <div class=""><input type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="mcpss_zone_api" checked /></div>
                                        </div>
                                    </div>
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="" class="control-label">Zone API (Current Students) : </label>
                                        <div class=""><input id="chk_0333" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="zone_api_existing" /></div>
                                    </div>
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="" class="control-label">Zone API (Admin Submissions) : </label>
                                        <div class=""><input id="chk_0334" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="admin_mcpss_zone_api" /></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow">
                                <div class="card-header">Priority Master</div>
                                <div class="card-body">
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="" class="control-label">Feeder : </label>
                                        <div class=""><input type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="feeder"  /></div>
                                    </div>
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="" class="control-label">Sibling : </label>
                                        <div class=""><input type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="sibling"  /></div>
                                    </div>
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="" class="control-label">Magnet Employee : </label>
                                        <div class=""><input type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="magnet_employee"  /></div>
                                    </div>
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="" class="control-label">Current Over New : </label>
                                        <div class=""><input type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="current_over_new"  /></div>
                                    </div>
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="" class="control-label">Magnet Student : </label>
                                        <div class=""><input type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="magnet_student"  /></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box content-header-floating" id="listFoot">
        <div class="row">
            <div class="col-lg-12 text-right hidden-xs float-right">
                <button type="Submit" class="btn btn-warning btn-xs submit"><i class="fa fa-save"></i> Save </button>
                <button type="Submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                <a class="btn btn-danger btn-xs" href="{{url('/admin/District')}}"><i class="fa fa-times"></i> Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection
@section('scripts')
   {{-- <script src="{{asset('resources/assets/common/js/jquery.validate.min.js')}}"></script>--}}
   <script>
        if($("#chk_03").is(":checked")) {
           $("#zone").show();
           $("#mcpss_zone").removeClass('d-none');
        }
        else {
           $("#zone").hide();
            $("#mcpss_zone").addClass('d-none');

        }
        $("#chk_03").on("change",function(){
            if($("#chk_03").is(":checked")) {
                $("#zone").show();
                $("#mcpss_zone").removeClass('d-none');
            }
            else {
                $("#zone").hide();
                $("#mcpss_zone").addClass('d-none');
            }
        });

        // Datepicker
        $('.mydatepicker, .mydatepicker01').datepicker('destroy');
        $('.mydatepicker, .mydatepicker01').datepicker({
            format: 'mm/dd/yy',
            autoclose: true
        });
        $('.mydatepicker, .mydatepicker01').datepicker('clearDates');


   </script>
    {{-- <script src="{{asset('resources/assets/admin/js/additional-methods.min.js')}}"></script> --}}
    <script>
        $(function () {
            //Student Information System (SIS) API
            showappKey($("#sis_connection"));
            $("#sis_connection").change(function () {
                showappKey(this);
            });
            function showappKey(select)
            {
                if ($(select).val()=='Power Schools')
                {
                    $(".sis_api_application_key").addClass('d-none');
                    $("input[name='sis_application_key']").val('');
                }
                else {
                    $(".sis_api_application_key").removeClass('d-none');
                }
            }


            $("input[name='district_logo']").change(function () {
                console.log(this.files)
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#district_logo_img')
                            .attr('src', e.target.result)
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
            $("input[name='magnet_program_logo']").change(function () {
                console.log(this.files)
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#magnet_prog_img')
                            .attr('src', e.target.result)
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
            //enter only number in zip code
            $("#zipcode").keypress(function (e) {
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    return false;
                }
            });

            //accessurl
            $("input[name='name']").on('input',function () {
                $("input[name='district_slug']").val($(this).val().toLowerCase().trim().replace(/[^a-z0-9\s]/gi, '').replace(/\s{1,}/g,'-'));
            });
            $("input[name='district_slug']").on('input',function () {
                $(this).val($(this).val().toLowerCase().trimStart().replace(/\s{1,}/g,'-').replace(/-{2,}/g,'-').replace(/[^a-z0-9-]/gi, ''));
            });
            $.validator.addMethod( "email", function( value, element ) {
                return this.optional(element) || /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/.test(value);
            }, "The email address is not valid" );
            $.validator.addMethod( "mobile", function( value, element ) {
                return this.optional(element) || /^(\+\d{1,3}[- ]?)?\d{10}$/.test(value);
            }, "The Mobile number is not valid" );
            $("form[name='add_district']").validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 255,
                    },
                    short_name: {
                        maxlength: 255,
                    },
                    address: {
                        required: true,
                        maxlength: 255,
                    },
                    city: {
                        required: true,
                        maxlength: 255,
                    },
                    state: {
                        required: true,
                        maxlength: 255,
                    },
                    zipcode: {
                        required: true,
                        maxlength: 6,
                        minlength: 5,
                        integer:true
                    },
                    district_logo :{
                        required: true,
                        accept: "jpg,png,jpeg,gif",
                    },
                    magnet_program_logo:{
                        accept: "jpg,png,jpeg,gif",
                    },
                    district_slug:{
                        required: true,
                        maxlength:255,
                        remote:{
                            url: "{{url("admin/District/uniqueurl")}}",
                            type: "GET",
                            data: {
                                    district_slug: function () {
                                    return $( "#district_slug" ).val();
                                }
                            },
                        }
                    },
                    theme_color:{
                        required:true,
                        maxlength:7,
                    },
                    magnet_point_contact:{
                        // required: true,
                        maxlength: 255,
                    },
                    magnet_point_contact_title:{
                        // required: true,
                        maxlength: 255,
                    },
                    magnet_point_contact_email:{
                        // required: true,
                        email:true,
                    },
                    magnet_point_contact_phone:{
                        // required: true,
                        mobile:true,
                    },
                    sis_connection:{
                        // required:true,
                    },
                    sis_username:{
                        maxlength: 255,
                    },
                    sis_password:{
                        maxlength: 255,
                    },
                    sis_application_key:{
                        maxlength: 255,
                    },
                    school_sis_contact:{
                        // required:true,
                        maxlength:255,
                    },
                    school_sis_contact_title:{
                        // required:true,
                        maxlength:255,
                    },
                    school_sis_contact_email:{
                        // required:true,
                        maxlength:255,
                        email:true
                    },
                    school_sis_contact_phone:{
                        // required:true,
                        mobile:true
                    },
                    billing_start_date:{
                        // required:true,
                    },
                    billing_end_date:{
                        // required:true,
                    },
                    notify_renewal_date:{
                        // required:true,
                    }
                },
                messages: {
                    name:{
                        required: 'The name field is required.',
                        maxlength:'The name may not be greater than 255 characters.'
                    },
                    short_name:{
                        maxlength:'The short name may not be greater than 255 characters.'
                    },
                    address:{
                        required: 'The Address field is required.',
                        maxlength:'The Address may not be greater than 255 characters.'
                    },
                    city:{
                        required: 'The city field is required.',
                        maxlength:'The city name may not be greater than 255 characters.'
                    },
                    state:{
                        required: 'The state field is required.',
                        maxlength:'The State name may not be greater than 255 characters.'
                    },
                    zipcode:{
                        required: 'The Zip code field is required.',
                        maxlength:'The zip code must be 6 digit.',
                        minlength:'The zip code contain at least 5 digit.',
                        integer:'The zip code must be Integer',
                    },
                    district_logo :{
                        required: 'The District logo is required.',
                        accept:'The file must be jpg,png,jpeg,gif.'
                    },
                    magnet_program_logo:{
                        // required: 'The Magnet Program Logo is required.',
                        accept:'The file must be jpg,png,jpeg,gif.'
                    },
                    district_slug:{
                        required: 'The url field is required.',
                        maxlength:'The url may not be greater than 255 characters.',
                        remote:'The URL has already been taken.',
                    },
                    theme_color:{
                        required: 'The Theme color field is required.',
                        maxlength:'The Theme color may not be greater than 7 characters.'
                    },
                    magnet_point_contact:{
                        // required: 'The Contact field is required.',
                        maxlength:'The Contact may not be greater than 255 characters.'
                    },
                    magnet_point_contact_title:{
                        // required: 'The title field is required.',
                        maxlength:'The title may not be greater than 255 characters.'
                    },
                    magnet_point_contact_phone:{
                        // required: 'The phone field is required.',
                    },
                    magnet_point_contact_email:{
                        // required:'The email field is required.',
                    },
                    sis_connection:{
                        // required:'The Connection field is required.',
                    },
                    sis_username:{
                        maxlength:'The Username may not be greater than 255 characters.',
                    },
                    sis_password:{
                        maxlength:'The Password may not be greater than 255 characters.',
                    },
                    sis_application_key:{
                        maxlength:'The Application Key may not be greater than 255 characters.',
                    },
                    school_sis_contact:{
                        // required:'The Contact field is required.',
                        maxlength:'The Contact may not be greater than 255 characters.',
                    },
                    school_sis_contact_title:{
                        // required:'The Title field is required.',
                        maxlength:'The Title may not be greater than 255 characters.',
                    },
                    school_sis_contact_email:{
                        // required:'The email field is required.',
                        maxlength:'The email may not be greater than 255 characters.',
                    },
                    school_sis_contact_phone:{
                        // required:'The Phone field is required.',
                    },
                    billing_start_date:{
                        // required:'The billing start date is required.',
                    },
                    billing_end_date:{
                        // required:'The billing end date is required.',
                    },
                    notify_renewal_date:{
                        // required:'The notify renewal date is required.',
                    }
                },errorPlacement: function(error, element)
                {
                    error.appendTo( element.parents('.form-group'));
                    error.css('color','red');
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });
        });
    </script>
@endsection