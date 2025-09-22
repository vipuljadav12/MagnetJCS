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
            <form method="post" action="{{url('/return-student-step1')}}" id="return_student_step1">
                        {{csrf_field()}}
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
                                <label class="control-label col-12 col-md-4 col-xl-3  text-right">Student ID (10 Digit) : </label>
                    
                                <div class="col-12 col-md-6 col-xl-6 text-left">
                                    <strong>{{$data['student_id']}}</strong>   
                                    <input type="hidden" name="student_id" value="{{$data['student_id']}}" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3 text-right">Birthday : </label>
                    
                                <div class="col-12 col-md-6 col-xl-6 text-left">
                                    <strong>{{$data['birthday']}}</strong>   
                                    <input type="hidden" class="form-control" name="birthday" value="{{$data['birthday']}}" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3 text-right">Parent First Name : </label>
                                <div class="col-12 col-md-6 col-xl-6 text-left">
                                    {{$data['parent_first_name']}}   
                                    <input type="hidden" class="form-control" name="parent_first_name" value="{{$data['parent_first_name']}}" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3 text-right">Parent Last Name : </label>
                                <div class="col-12 col-md-6 col-xl-6 text-left">
                                    {{$data['parent_last_name']}}   
                                    <input type="hidden" class="form-control" name="parent_last_name" value="{{$data['parent_last_name']}}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3 text-right">Best Contact Phone Number : </label>
                                <div class="col-12 col-md-6 col-xl-6 text-left">
                                    {{$data['phone']}}   
                                    <input type="hidden" class="form-control" name="phone" value="{{$data['phone']}}" />
                                </div>
                            </div>
                        </div>
                    </div>
  
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3 text-right">Alternate Phone Number : </label>
                                <div class="col-12 col-md-6 col-xl-6 text-left">
                                    {{$data['alternate_phone']}}   
                                    <input type="hidden" class="form-control" name="alternate_phone" value="{{$data['alternate_phone']}}" />
                                </div>
                            </div>
                        </div>
                    </div> 

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3 text-right">Parent Email : </label>
                                <div class="col-12 col-md-6 col-xl-6 text-left">
                                    {{$data['parent_email']}}   
                                    <input type="hidden" class="form-control" name="parent_email" value="{{$data['parent_email']}}" />
                                </div>
                            </div>
                        </div>
                    </div>  


                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3  text-right">Student Name : </label>
                                <div class="col-12 col-md-6 col-xl-6 text-left">
                                    {{$data['student_name']}}   
                                </div>
                            </div>
                        </div>
                    </div>                                        

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3 text-right">Next Grade : </label>
                                <div class="col-12 col-md-6 col-xl-6 text-left">
                                    {{$data['next_grade']}}   
                                </div>
                            </div>
                        </div>
                    </div>                                        

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3  text-right">Current School : </label>
                                <div class="col-12 col-md-6 col-xl-6 text-left">
                                    {{$data['current_school']}}   
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3 text-right">Current Signature Academy : </label>
                                <div class="col-12 col-md-6 col-xl-6 text-left">
                                    {{$data['current_signature_academy']}}   
                                </div>
                            </div>
                        </div>
                    </div> 

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3 text-right">Is the above information correct for your student ?</label>
                                <div class="col-12 col-md-6 col-xl-6">
                                    <select name="correct_information" class="form-control" onchange="showNext(this.value)">
                                        <option value="">Select</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div> 

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
                    
                </div>
                </form>
            </div>
        </div>
          
   
@endsection
