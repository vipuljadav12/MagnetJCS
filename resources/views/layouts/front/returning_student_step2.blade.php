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
<form method="post" action="{{url('/return-student-step2')}}" id="return_student_step2">
                        {{csrf_field()}}
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3">Student ID (10 Digit) : </label>
                    
                                <div class="col-12 col-md-6 col-xl-6 text-left">
                                   <strong>{{$data['student_id']}}   </strong>
                                    <input type="hidden" name="student_id" value="{{$data['student_id']}}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3">Student Name : </label>
                    
                                <div class="col-12 col-md-6 col-xl-6 text-left">
                                   <strong>{{$data['student_name']}}   </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3">Current Signature Academily : </label>
                    
                                <div class="col-12 col-md-6 col-xl-6 text-left">
                                   <strong>{{$data['current_signature_academy']}}   </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                                <input type="hidden" class="form-control" name="birthday" value="{{$data['birthday']}}" />
                                <input type="hidden" class="form-control" name="parent_first_name" value="{{$data['parent_first_name']}}" />
                                <input type="hidden" class="form-control" name="parent_last_name" value="{{$data['parent_last_name']}}" />
                                <input type="hidden" class="form-control" name="phone" value="{{$data['phone']}}" />
                                <input type="hidden" class="form-control" name="alternate_phone" value="{{$data['alternate_phone']}}" />
                                <input type="hidden" class="form-control" name="parent_email" value="{{$data['parent_email']}}" />
                    
                    
                    
                        <div class="row" id="confirm_div">
                            <div class="col-12">
                                <div class="form-group row">
                                    <label class="control-label col-12 col-md-4 col-xl-3">Will your student continue attending this Signature Academy for 2023/2024 ?</label>
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <select class="form-control" onchange="showReason(this.value)" name="returning_customer" id="returning_customer">
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
                                    <label class="control-label col-12 col-md-4 col-xl-3">Please provide the reason your student will not be returning: </label>
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
                                        <button type="submit" class="btn btn-primary">Submit Form</button>
                                    </div>                           
                                </div>
                            </div>
                        </div> 
                    
                </div>
                </form>
            </div>
        </div>
          
   
@endsection
