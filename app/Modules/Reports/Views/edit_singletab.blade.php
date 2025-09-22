    @extends('layouts.admin.app')
@section('title')
	Edit Submission
@endsection
@section('content')
	<div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Edit Submission</div>
            <div class=""><a class=" btn btn-secondary btn-sm" href="{{url('admin/Submissions')}}">Back</a></div>
        </div>
    </div>
    @include("layouts.admin.common.alerts")
    {{-- <form method="post" action="{{ url('admin/Submissions/update',$submission->id) }}">     --}}
    {{csrf_field()}}                    
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="grades0-tab" data-toggle="tab" href="#grades0" role="tab" aria-controls="grades0" aria-selected="false">General</a></li>
           
            @if($submission->first_choice != '')
            @php
              $eligibilities = getEligibilities($submission->first_choice);
              
              // print_r("expression");
            @endphp
            @foreach($eligibilities as $key=>$value)
                <li class="nav-item"><a class="nav-link" id="{{$key+1}}-tab" data-toggle="tab" href="#{{$key+1}}-tabcontent" role="tab" aria-controls="grades{{$key+1}}" aria-selected="false">{{$value->eligibility_ype}}</a></li>
             @endforeach
             @endif
        </ul>
        <div class="tab-content bordered" id="myTabContent">
            <div class="tab-pane fade show active" id="grades0" role="tabpanel" aria-labelledby="grades0-tab">
                @include("Submissions::template.submission_general")
            </div>
            @if($submission->first_choice != '')
              @foreach($eligibilities as $key=>$value)
                  <div class="tab-pane fade" id="{{$key+1}}-tabcontent" role="tabpanel" aria-labelledby="{{$key+1}}-tab">
                  @include("Submissions::template.submission_".str_replace(" ","_",strtolower($value->eligibility_ype)))
              </div>
              @endforeach
            @endif
        </div>
        {{-- <div class="box content-header-floating" id="listFoot">
	        <div class="row">
	            <div class="col-lg-12 text-right hidden-xs float-right">
	            	<button type="submit" class="btn btn-warning btn-xs"><i class="fa fa-save"></i> Save </button>
	            	<button type="submit" class="btn btn-success btn-xs" name="save_edit" value="save_edit"><i class="fa fa-save"></i> Save &amp; Edit</button>
	            	
	            </div>
	        </div>
    	</div> --}}
    {{-- </form> --}}
@endsection
@section('scripts')

    <script>
        $(document).on("change",".changeDate",function()
        {
            let year = $(document).find("#year").val();
            let month = $(document).find("#month").val();
            let day = $(document).find("#day").val();
            $(document).find("#birthday").val(year+"-"+month+"-"+day);
        });
        $("#first_choice").change(function(){
          var val = $(this).val();
          
          $('#second_choice > option').each(function(){
              if($(this).val() == val && val != "")
                $(this).addClass("d-none");
              else
                $(this).removeClass("d-none");
          });
        })

        $("#second_choice").change(function(){
          var val = $(this).val();
          $('#first_choice > option').each(function(){
              if($(this).val() == val && val != "")
                $(this).addClass("d-none");
              else
                $(this).removeClass("d-none");
          });
        })
        ScreenOrientation.lock;
    </script>
@endsection