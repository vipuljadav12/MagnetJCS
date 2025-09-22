@extends('layouts.front.app')
@section('title')
    <title>{{config('variables.page_title')}}</title>
@endsection

@section('content')
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

                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3">Student ID (10 Digit)<span class="text-danger">*</span>
                                </label>
                    
                                <div class="col-12 col-md-6 col-xl-6">
                                    <input type="text" class="form-control" thisname="Student ID (10 Digit)" name="formdata[62]" required="" value="" placeholder="Student ID (10 Digit)" id="student_id"> <span class="hidden">
                                            Checking Student ID <img src="{{url('/resources/assets/front/images/loader.gif')}}"></span>      

                                </div>
                                <div class="col-12 col-md-2 col-xl-3 hidden">
                                    <span class="help" data-toggle="tooltip" data-html="true" title="" data-original-title="If you do not know your 10-digit state identification number, log into the I-Now Parent Portal to obtain this information. If you need assistance with I-Now access, contact your school office."><i class="fas fa-question"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3">Date of Birth<span class="text-danger">*</span>           </label>
                                <div class="col-12 col-md-6 col-xl-6">
                                    <input type="hidden" class="form-control" id="birthdayFiller" thisname="Date of Birth" name="formdata[3]" placeholder="Date of Birth" required="" value="">
                                    <div class="row">
                                        <div class="col-4">
                                            <select class="form-control changeDate" id="month">
                                                
                                            </select>
                                        </div>

                                        <div class="col-4">
                                            <select class="form-control changeDate" id="day">
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select class="form-control changeDate" id="year">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="fetch_info_btn">
                        <div class="col-12 text-center">
                            <div class="form-group text-center">
                                <button class="btn btn-secondary" onclick="checkReturnStudentInfo()">Fetch Student Information</button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="returning_info">
                    </div> 
                    <form method="post" action="{{url('/return-customer')}}">
                        {{csrf_field()}}
                        <input type="hidden" id="form_student_id" name="student_id" />
                        <div class="row hidden" id="confirm_div">
                            <div class="col-12">
                                <div class="form-group row">
                                    <label class="control-label col-12 col-md-4 col-xl-3">Is the above information correct for your student ?</label>
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <select class="form-control" onchange="showReason(this.value)" name="returning_customer">
                                            <option value="">Select</option>
                                            <option  value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>                           
                                </div>
                            </div>
                        </div>

                        <div class="row hidden" id="reason_div">
                            <div class="col-12">
                                <div class="form-group row">
                                    <label class="control-label col-12 col-md-4 col-xl-3">Please check the information you provided and try again. </label>
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <textarea class="form-control" name="reason"></textarea>
                                    </div>                           
                                </div>
                            </div>
                        </div> 

                        <div class="row hidden" id="submit_button">
                            <div class="col-12">
                                <div class="form-group row">
                                    <label class="control-label col-12 col-md-4 col-xl-3"></label>
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>                           
                                </div>
                            </div>
                        </div> 
                    </form>
                </div>
            </div>
        </div>
          
   
@endsection
