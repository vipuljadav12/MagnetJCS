<div class="tab-pane fade show active" id="preview04" role="tabpanel" aria-labelledby="preview04-tab">
    <div class="@if($display_outcome > 0) d-none @endif" style="height: 657px; overflow-y: auto;">
        
            <div class="table-responsive" id="table-wrap" style="overflow-y: scroll; height: 100%;">
            <table class="table" id="tbl_population_changes">
                <thead>
                    <tr>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important">Program Name</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important">Max Capacity</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important">Starting Available Slot</th>
                        @isset($race_ary)
                            @foreach($race_ary as $race=>$tmp)
                                <th class="" style="position: sticky; top: 0; background-color: #fff !important">{{$race}}</th>
                            @endforeach
                        @endisset
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important">Total Offered</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important">Ending Available Slots</th>
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
                            <td class="">{{getProgramName($value['program_id'])}}</td>
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
    <div class="d-flex flex-wrap justify-content-between mt-20"><a href="javascript:void(0);" class="btn btn-secondary" title="" id="ExportReporttoExcel">Download Population Changes</a> @if($display_outcome == 0) <a href="javascript:void(0);" class="btn btn-success" title="" onclick="updateFinalStatus()">Accept Outcome and Commit Result</a> @else <a href="javascript:void(0);" class="btn btn-danger" title="" onclick="alert('Already Outcome Commited')">Accept Outcome and Commit Result</a>  @endif
        @if($from == "program")
                 <a href="{{url('/admin/Process/Selection/Results/'.$pid)}}" class="btn btn-success d-none" title="">Accept Outcome and Commit Result</a>
            @else
                <a href="{{url('/admin/Process/Selection/Results/Form/'.$pid)}}" class="btn btn-success d-none" title="">Accept Outcome and Commit Result</a>
            @endif
           </div>
</div>

