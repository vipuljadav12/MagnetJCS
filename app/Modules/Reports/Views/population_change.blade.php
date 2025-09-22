@extends('layouts.admin.app')
@section('title')
    Population Changes
@endsection
@section('styles')
@endsection
@section('content')
<style>
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
.dt-buttons {position: absolute !important; padding-top: 5px !important;}

</style>


<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Population Changes</div>
        <div class=""><a class=" btn btn-secondary btn-sm" href="{{url('/admin/Reports/process/logs')}}" title="Go Back">Go Back</a></div>
    </div>
</div>

<div class="card shadow">
                            <div class="card-body">
                                {{-- <div class="" align="right">
                                    <a class="btn btn-secondary btn-lg" href="javascript:void(0)"
                                     data-toggle="modal" data-target="#exportfieldsModal">Export to Excel</a>
                                </div> --}}
                                <div class="row col-md-12 pull-left" id="submission_filters"></div>
<div class="">
                                @if(!empty($data_ary))
                                <div  style="height: 657px; overflow-y: auto;">
                                <div class="table-responsive" id="div_pop_changes" style="overflow-y: scroll; height: 100%;">
                                    <table class="table" id="tbl_population_changes">
                                        <thead>
                                            <tr>
                                                <th style="position: sticky; top: 0; background-color: #fff !important">Program Name</th>
                                                <th style="position: sticky; top: 0; background-color: #fff !important">Max Capacity</th>
                                                <th style="position: sticky; top: 0; background-color: #fff !important">Starting Available Slot</th>
                                                @isset($race_ary)
                                                    @foreach($race_ary as $race=>$tmp)
                                                        <th style="position: sticky; top: 0; background-color: #fff !important">{{$race}}</th>
                                                    @endforeach
                                                @endisset
                                                <th style="position: sticky; top: 0; background-color: #fff !important">Total Offered</th>
                                                <th style="position: sticky; top: 0; background-color: #fff !important">Ending Available Slots</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @isset($data_ary)
                                                @foreach($data_ary as $key=>$value)
                                                <tr>
                                                    @php
                                                        $available_seats = $value['available_seats'] ?? 0;
                                                    @endphp
                                                    {{-- <td class="">{{$value['program_id']}} - Grade {{$value['grade']}}</td> --}}
                                                    <td class="">{{getProgramName($value['program_id'])}} - Grade {{$value['grade']}}</td>
                                                    <td class="text-center">{{$value['total_seats'] ?? 0}}</td>
                                                    <td class="text-center">{{$available_seats}}</td>
                                                    @isset($race_ary)
                                                        @php
                                                            $total_offered = 0;
                                                        @endphp
                                                        @foreach($race_ary as $race=>$tmp)
                                                            @php
                                                                $offered_value = $value['race_count'][$race] ?? 0;
                                                                $total_offered += $offered_value;
                                                            @endphp
                                                            <td class="text-center">{{$offered_value}}</td>
                                                        @endforeach
                                                    @endisset
                                                    <td class="text-center">{{$total_offered}}</td>
                                                    <td class="text-center">{{$available_seats - $total_offered}}</td>
                                                </tr>
                                                @endforeach
                                            @endisset
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                                @else
                                    <div class="table-responsive text-center"><p>No Records found.</div>
                                @endif

                            </div>
                            </div>
                        </div>

                        

@endsection
@section('scripts')
    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Committing submission status.<br>It will take approx 2 minutes to update all records. </div></div>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>
    <script type="text/javascript">
        var dtbl_submission_list = $("#tbl_population_changes").DataTable({"aaSorting": [],
             dom: 'Bfrtip',
             
             bPaginate: false,
             bSort: false,
             buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'PopulationChanges',
                        text:'Export to Excel'
                    }
                ]
            });

            $("#ExportReporttoExcel").on("click", function() {
                dtbl_submission_list.button( '.buttons-excel' ).trigger();
            });

            
            
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


    </script>
@endsection