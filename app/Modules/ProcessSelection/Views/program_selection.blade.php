@extends('layouts.admin.app')
@section('title')Process Selection | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Process Selection</div></div>
        </div>
    </div>
    
      <div class="tab-pane fade show" id="preview03" role="tabpanel" aria-labelledby="preview03-tab">  
        <div class="card shadow">
            <div class="card-body">
                    <div class="tab-pane fade show active" id="preview02" role="tabpanel" aria-labelledby="preview02-tab">
                        <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <div class="">
       
        <form action="{{ url('admin/Process/Selection/selection_report')}}" method="post" name="process_selection" id="process_selection">
            {{csrf_field()}}
                <input type="hidden" name="application_id" value="{{$application_id}}" id="application_id">
                <div class="card shadow">
                    <div class="card-header">Programs to Process</div>
                    <div class="card-body">
                        <div class="row pt-10">
                             <div class="form-group col-12">
                                        @foreach($programs as $key=>$program)
                                           <div class="">
                                                    <div class="">
                                                        <input type="checkbox" name="program_id[]" class="" value="{{$program->id}}" checked>
                                                        <label class="">{{$program->name}}</label>
                                                    </div>
                                                </div>
                                            
                                        @endforeach
                                    </div>
                        </div>
                        <div class="text-right pt-20"><input type="submit" class="btn btn-success" title="Process Submissions Now" value="Process Submissions Now"></div>    
                    </div>
                </div>
            </form>
        
    </div>

                    </div>
                </div>
            </div>
        </div>
        
@endsection