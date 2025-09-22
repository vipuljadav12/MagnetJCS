@extends('layouts.admin.app')
@section('title')Process Selection | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
<style type="text/css">
    .buttons-excel{display: none !important;}
</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Waitlist Process Selection <span class="font-16">[{{getDateTimeFormat($version_data->created_at)}}]</span></div>
            <div class=""><a class=" btn btn-secondary btn-sm" href="{{url('/admin/Reports/process/logs')}}" title="Go Back">Go Back</a></div>

        </div>
    </div>
    
    <form class="">
        <div class="tab-content bordered" id="myTabContent">
            <div class="tab-pane fade show active" id="preview03" role="tabpanel" aria-labelledby="preview03-tab">
                <div class="" style="height: 657px; overflow-y: auto;">
                    <div class="table-responsive " id="table-wrap">
                        <table class="table" id="tbl_population_changes">
                            <thead>
                                <tr>
                                    <th class="">Program Name</th>
                                    <th class="">Max Capacity</th>
                                    <th class="">Starting Available Slot</th>
                                    @isset($race_ary)
                                        @foreach($race_ary as $race=>$tmp)
                                            <th class="">{{$race}}</th>
                                        @endforeach
                                    @endisset
                                    <th class="">Total Offered</th>
                                    <th class="">Ending Available Slots</th>
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
                <div class="d-flex flex-wrap justify-content-between mt-20"><a href="javascript:void(0);" class="btn btn-secondary" title="" id="ExportReporttoExcel">Download Population Changes</a>
                       </div>
            </div>


        </div>
    </form>    
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

            
            function updateFinalStatus()
            {
                $("#wrapperloading").show();
                $.ajax({
                    url:'{{url('/admin/Waitlist/Accept/list')}}',
                    type:"post",
                    data: {"_token": "{{csrf_token()}}"},
                    success:function(response){
                        alert("Status Allocation Done.");
                        $("#wrapperloading").hide();
                        document.location.reload();

                    }
                })
            }


    </script>
@endsection