@extends('layouts.admin.app')
@section('title')Eligibility Checking | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Eligibility Checking - {{getApplicationName($application_id)}}</div>
            <div class=""><a class=" btn btn-secondary btn-sm" href="{{url('/admin/EligibilityChecker')}}" title="Go Back">Go Back</a></div>
            </div>
        </div>
    </div>
    
      <div class="tab-pane fade show" id="preview03" role="tabpanel" aria-labelledby="preview03-tab">  
        <div class="card shadow">
            <div class="card-body">
                    <div class="tab-pane fade show active" id="preview02" role="tabpanel" aria-labelledby="preview02-tab">
                        <div class="">
                            
                            <div class="table-responsive">
                                <table id="datatable" class="table table-striped mb-0">
                                    <thead>
                                    <tr>
                                        <th class="align-middle">Program Name</th>
                                        @foreach($eligibility_templates as $key=>$value)
                                            <th class="align-middle">{{$value->name}}</th>
                                        @endforeach
                                    </tr>
                                    </thead>
                                    @foreach($programs as $pkey=>$pvalue)
                                        <tr>
                                            <td class="">{{getProgramName($pvalue->program_id)}}</td>
                                            @foreach($eligibility_templates as $pekey=>$pevalue)
                                                <td class="">{{$program_eligibilities[$pvalue->program_id][$pevalue->id]}}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
@endsection
@section('scripts')

<script type="text/javascript">

    $("#selectform_settings").click(function()
    {
        if($("#form_field").val() == "")
        {
            alert("Please select application")
            $("#form_field").focus();
            return false;
        }
        document.location.href = "{{url('/admin/EligibilityChecker/application/')}}/"+$("#form_field").val();
    })
     
</script>
@endsection