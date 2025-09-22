@extends('layouts.front.app')
@section('title')
    <title>{{config('variables.page_title')}}</title>
@endsection

@section('content')
<style type="text/css">
    .form-group .error {float: left}
</style>
       <div class="mt-20">
        <div class="card bg-light p-20">
            <div class="text-center font-20 b-600 mb-10">{!! getconfig()['returning_customer_title'] ?? '' !!}</div>
            <div class="">
                {!! getconfig()['returning_customer_message'] ?? '' !!}
            </div>
        </div>
    </div>

    <div class="box-2" style="">
        <div class="box-2-1" style="">
            
            
        </div>
    </div>
    
        <div class="box-0 text-center">
            <div class="form-group text-center p-20 border mt-20 mb-20">
                <div class="back-box" style="">
                    <div class="form-group text-right pt-10">
                        <div class="">
                            <a href="{{url('/')}}" class="btn btn-secondary back-box-1" title="">Back</a>
                        </div>
                    </div>    
                </div>
 <form method="post" action="{{url('/return-student')}}" id="return_student">
                        {{csrf_field()}}
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-5 col-xl-5">Please provide the following information and click "Next". </label>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3">Student ID (10 Digit)<span class="text-danger">*</span>
                                </label>
                    
                                <div class="col-12 col-md-6 col-xl-6">
                                    <input type="text" class="form-control" thisname="Student ID (10 Digit)" name="student_id" required="" value="" placeholder="Student ID (10 Digit)" id="student_id"> <span class="hidden">
                                            Checking Student ID <img src="{{url('/resources/assets/front/images/loader.gif')}}"></span>      

                                </div>
                                <div class="col-12 col-md-2 col-xl-3 text-left">
                                    <span class="help" data-toggle="tooltip" data-html="true" title="" data-original-title="If you do not know your 10-digit state identification number, log into the PowerSchool Parent Portal to obtain this information. If you need assistance with PowerSchool access, contact your school office."><i class="fas fa-question"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3">Date of Birth<span class="text-danger">*</span>           </label>
                                <div class="col-12 col-md-6 col-xl-6">
                                    <input type="hidden" class="form-control" id="birthdayFiller" thisname="Date of Birth" name="birthday" placeholder="Date of Birth" required=""  value="{{date('Y-m-d')}}">
                                    <div class="row">
                                        <div class="col-4">
                                            <select class="form-control changeDate changeDater" id="month">
                                                
                                            </select>
                                        </div>

                                        <div class="col-4">
                                            <select class="form-control changeDate changeDater" id="day">
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select class="form-control changeDate changeDater" id="year">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3">Parent First Name <span class="text-danger">*</span>
                                </label>
                    
                                <div class="col-12 col-md-6 col-xl-6">
                                    <input type="text" class="form-control" thisname="Parent First Name" name="parent_first_name" required="" value="" placeholder="Parent First Name">     

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3">Parent Last Name <span class="text-danger">*</span>
                                </label>
                    
                                <div class="col-12 col-md-6 col-xl-6">
                                    <input type="text" class="form-control" thisname="Parent Last Name" name="parent_last_name" required="" value="" placeholder="Parent Last Name">     

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3">Best Contact Phone Number<span class="text-danger">*</span>
                                </label>
                    
                                <div class="col-12 col-md-6 col-xl-6">
                                    <input type="text" class="form-control" name="phone" required="" value="" placeholder="(___) ___-____">     

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3">Alternate Phone Number<span class="text-danger">*</span>
                                </label>
                    
                                <div class="col-12 col-md-6 col-xl-6">
                                    <input type="text" class="form-control" name="alternate_phone" required="" value="" placeholder="(___) ___-____">     

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3">Parent Email Address</label>
                    
                                <div class="col-12 col-md-6 col-xl-6">
                                    <input type="text" class="form-control" name="parent_email" required="" value="" placeholder="Parent Email Address">     

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="fetch_info_btn">
                        <div class="col-12 text-center">
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-secondary" >Next</button>
                            </div>
                        </div>
                    </div>
                    
                    
                    
                       

                        <!-- onclick="checkReturnStudentInfo()" <div class="row hidden" id="reason_div">
                            <div class="col-12">
                                <div class="form-group row">
                                    <label class="control-label col-12 col-md-4 col-xl-3">Please check the information you provided and try again. </label>
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <textarea class="form-control" name="reason"></textarea>
                                    </div>                           
                                </div>
                            </div>
                        </div>  -->

                        <div class="row hidden" id="submit_button">
                            <div class="col-12">
                                <div class="form-group row">
                                    <label class="control-label col-12 col-md-4 col-xl-3"></label>
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <button type="submit" class="btn btn-primary">Next</button>
                                    </div>                           
                                </div>
                            </div>
                        </div> 
                    
                </div>
                </form>
            </div>
        </div>
          
   
@endsection
