@extends('layouts.admin.app')
@section('title')
    Offer Status Report
@endsection
@section('styles')
<style type="text/css">
    .alert1 {
        position: relative;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
            border-top-color: transparent;
            border-right-color: transparent;
            border-bottom-color: transparent;
            border-left-color: transparent;
        border-radius: 0.25rem;
    }
    .custom-select2{
    margin: 5px !important;
}
.dt-buttons{position: absolute;}
</style>
@endsection
@section('content')

<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Offer Status Report</div></div>
</div>

<div class="card shadow">
    <div class="card-body">
        <form class="">
            <div class="form-group">
                <label for="">Enrollment Year : </label>
                <div class="">
                    <select class="form-control custom-select" id="enrollment">
                        <option value="">Select Enrollment Year</option>
                        @foreach($enrollment as $key=>$value)
                            <option value="{{$value->id}}" @if($enrollment_id == $value->id) selected @endif>{{$value->school_year}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="">Report : </label>
                <div class="">
                    <select class="form-control custom-select" id="reporttype">
                        <option value="">Select Report</option>
                        <option value="offerstatus" selected>Offer Status Report</option>
                        <option value="duplicatestudent">Student Duplicate Report</option>
                            {{-- <option value="gradecdiupload">Parent Submitted Records</option> --}}
                        <!--<option value="seatstatus">Seat Status Report</option>
                        <option value="populationchange">Population Changes</option>
                            <option value="results">Submission Results</option>-->
                        
                    </select>
                </div>
            </div>
            <div class=""><a href="javascript:void(0);" onclick="showReport()" title="Generate Report" class="btn btn-success generate_report">Generate Report</a></div>
        </form>
    </div>
</div>

<div class="">
    <div class="tab-content bordered" id="myTabContent">
        <div class="tab-pane fade show active" id="needs1" role="tabpanel" aria-labelledby="needs1-tab">
            
            <div class="tab-content" id="myTabContent1">
                <div class="tab-pane fade show active" id="grade1" role="tabpanel" aria-labelledby="grade1-tab">
                    <div class="">
                        <div class="card shadow">
                            <div class="card-body">
                                {{-- <div class="" align="right">
                                    <a class="btn btn-secondary btn-lg" href="javascript:void(0)"
                                     data-toggle="modal" data-target="#exportfieldsModal">Export to Excel</a>
                                </div> --}}
                                @if(count($versions_lists) > 0)
                                    <div class="row col-md-12 pull-left pb-10">
                                        <select class="form-control custom-select" onchange="loadVersionData(this.value)"> 
                                            
                                            @foreach($versions_lists as $key=>$value)
                                                @php
                                                    $link = url('/')."/admin/Reports/missing/".$enrollment_id."/offerstatus/".$value->type."/".$value->version;
                                                @endphp
                                                    <option value="{{$link}}" @if($value->version == $version && $type == $value->type) selected="" @endif>Process {{ucfirst($value->type)}}  - {{getDateTimeFormat($value->updated_at)}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                @endif
                                <div class="row col-md-12 pull-left" id="submission_filters"></div>

                                @if(!empty($data_ary))
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0 w-100" id="datatable">
                                        <thead>
                                            <tr>
                                                <th class="align-middle">Submission ID</th>
                                                <th class="align-middle">State ID</th>
                                                <th class="align-middle">Last Name</th>
                                                <th class="align-middle">First Name</th>
                                                <th class="align-middle">Next Grade</th>
                                                <th class="align-middle">Current School</th>
                                                <th class="align-middle">Program Choice 1</th>
                                                <th class="align-middle">Program Choice 2</th>
                                                <th class="align-middle">Program Choice 3</th>
                                                <th class="align-middle">Offered Program</th>
                                                <th class="align-middle">Submission Status</th>
                                                <th class="align-middle d-none">Contract Status</th>
                                                <th class="align-middle d-none">Contract Status</th>
                                                <th class="align-middle">Timestamp</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($data_ary as $value)
                                                @php
                                                    $first_choice_program = getProgramName($value->first_choice_program_id); 
                                                    $second_choice_program = getProgramName($value->second_choice_program_id); 
                                                    $offered_program = 'second';
                                                    if ($value->first_choice_final_status === "Offered") {
                                                        $offered_program = 'first';
                                                    }
                                                    $third_choice_program = getProgramName($value->third_choice_program_id); 
                                                    if ($value->third_choice_final_status === "Offered") {
                                                        $offered_program = 'third';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="text-center"><a href="{{url('/admin/Submissions/edit/'.$value->submission_id)}}" target="_blank">{{$value->submission_id}}</a></td>
                                                    <td class="">{{$value->student_id}}</td>
                                                    <td class="">{{$value->last_name}}</td>
                                                    <td class="">{{$value->first_name}}</td>
                                                    <td class="">{{$value->next_grade}}</td>
                                                    <td class="">{{$value->current_school}}</td>
                                                    <td class="">{{$first_choice_program}}</td>
                                                    <td class="">{{$second_choice_program}}</td>
                                                    <td class="">{{$third_choice_program}}</td>
                                                    <td class="">
                                                        {!! ${$offered_program.'_choice_program'} !!} - Grade {{$value->next_grade}}
                                                    </td>

                                                    @php
                                                        $offered_status_class = 'alert-warning';
                                                        $offered_status_txt = 'Offered';
                                                        $contract_status_class = 'alert-warning';
                                                        $contract_status_txt = 'Pending';
                                                        $contract_signed_on = '';

                                                        if ($value->{$offered_program.'_offer_status'} === "Accepted") {
                                                            $offered_status_class = 'alert-success';
                                                            $offered_status_txt = 'Offered and Accepted';
                                                            $contract_signed_on = getDateTimeFormat($value->{$offered_program.'_offer_update_at'});
                                                            if ($value->contract_status === "Signed") {
                                                                $contract_status_class = 'alert-success';
                                                                $contract_status_txt = 'Offered and Accepted';
                                                                
                                                            }

                                                        }elseif ($value->{$offered_program.'_offer_status'} === "Declined" || $value->{$offered_program.'_offer_status'} === "Auto Decline") {
                                                            $offered_status_class = 'alert-danger';
                                                            $offered_status_txt = 'Offered and Declined';

                                                            $contract_status_class = 'alert-danger';
                                                            $contract_status_txt = 'Not Applicable';
                                                        }elseif ($value->{$offered_program.'_offer_status'} === "Declined & Waitlisted") {
                                                            $offered_status_class = 'alert-warning';
                                                            $offered_status_txt = 'Declined / Waitlist for other';

                                                            $contract_status_class = 'alert-danger';
                                                            $contract_status_txt = 'Not Applicable';
                                                        }

                                                    @endphp

                                                    <td class="">
                                                        <div class="alert1 text-center {{$offered_status_class}} p-10">{{$offered_status_txt}}</div>
                                                    </td>

                                                    <td class="d-none">
                                                        <div class="alert1 text-center {{$contract_status_class}} p-10">{{$contract_status_txt}}</div>
                                                        @if($value->contract_status == "Signed")
                                                            @if(file_exists(resource_path('/assets/admin/online_contract/Contract-'.$value->confirmation_no.'.pdf')))
                                                                <div class="text-center">
                                                                    <a href="{{url('/resources/assets/admin/online_contract/Contract-'.$value->confirmation_no).'.pdf'}}" target="_blank"  class="font-18"><i class="far fa-file-pdf"></i></a>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="">{{$contract_status_txt}}</td>

                                                    <td class="">
                                                        {{$contract_signed_on}}
                                                        {!! getContratIPInfo($value->submission_id) !!}
                                                    </td>
                                                    
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                    <div class="table-responsive text-center"><p>No Records found.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script> 
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script> 
<script type="text/javascript">
    var dtbl_submission_list = $("#datatable").DataTable({
        dom: 'Bfrtip',
        order: [],
         buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Offer Status',
                        text:'Export to Excel'
                    }
                ]
    });
    $("#datatable thead th").each( function ( i ) {
                    // Disable dropdown filter for disalble_dropdown_ary (index=0)
                    var disalble_dropdown_ary = [6,7,8,9];//13
                    if ($.inArray(i, disalble_dropdown_ary) >= 0) {
                        var column_title = $(this).text();
                        
                        var select = $('<select class="form-control custom-select2 submission_filters col-md-3" id="filter_option"><option value="">Select '+column_title+'</option></select>')
                            .appendTo( $('#submission_filters') )
                            .on( 'change', function () {
                                if($(this).val() != '')
                                {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                         $(this).val()
                                     );
                                    dtbl_submission_list.column( i )
                                        .search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                }
                                else
                                {
                                    dtbl_submission_list.column( i )
                                        .search('')
                                        .draw();
                                }
                            } );
                 
                         dtbl_submission_list.column( i ).data().unique().sort().each( function ( d, j ) {
                                str = d.replace('<div class="alert1 text-center alert-success p-10">', "");
                                str = str.replace('<div class="alert1 text-center alert-warning p-10">', "");
                                str = str.replace('<div class="alert1 text-center alert-danger p-10">', "");
                                str = str.replace('</div>', "");
                                select.append( '<option value="'+str+'">'+str+'</option>' )
                            } );
                    }
                } );
    dtbl_submission_list.columns([11]).visible(false);

    function showReport()
    {
        if($("#enrollment").val() == "")
        {
            alert("Please select enrollment year");
        }
        else if($("#reporttype").val() == "")
        {
            alert("Please select report type");
        }
        else
        {
            var link = "{{url('/')}}/admin/Reports/missing/"+$("#enrollment").val()+"/"+$("#reporttype").val();
            document.location.href = link;
        }
    }

    function loadVersionData(value)
    {
            var link = value;//"{{url('/')}}/admin/Reports/missing/"+$("#enrollment2").val()+"/offerstatus/"+value;
            document.location.href = link;

    }
</script>

@endsection