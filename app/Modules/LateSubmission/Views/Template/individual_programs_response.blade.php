    <form action="{{url('/admin/LateSubmission/Individual/store')}}" method="post" id="process_selection">
             {{csrf_field()}}
             <input type="hidden" name="program_id" value="{{$program_id}}">
    <div class="table-responsive">
        
       <table class="table m-0" id="tbl_population_changes">
                <thead>
                    <tr>
                        <th class="">Program Name</th>
                        <th class="text-center">Original Entered Available Seats</th>
                        <th class="text-center">Current Offered and Accepted</th>
                        <th class="text-center">Waitlisted</th>
                        <th class="text-center">Late Applications</th>
                        <th class="text-center">Remaining Available Seats</th>
                        <th class="text-center">Withdrawn Students Count to Add</th>
                        <th class="text-center">Seats to Process</th>
                        <th class="text-center">Updated Available Seats</th>
                    </tr>
                </thead>
                <tbody>
                    @isset($data_ary)
                        @foreach($data_ary as $key=>$value)
                        <input type="hidden" id="program_id" value="{{$value['program_id']}}">
                        <input type="hidden" id="grade" value="{{$value['grade']}}">
                        <tr>
                            @php
                                $available_seats = $value['available_seats'] ?? 0;
                            @endphp
                            <td class="">{{getProgramName($value['program_id'])}} - Grade {{$value['grade']}}</td>
                            <td class="text-center"><span id="available_seats-{{$value['program_id'].'-'.$value['grade']}}">{{$value['available_seats'] ?? 0}}</span></td>
                            <td class="text-center"><span id="offer_count-{{$value['program_id'].'-'.$value['grade']}}">{{$value['offer_count'] ?? 0}}</span></td>
                            <td class="text-center"><span id="waitlist_count-{{$value['program_id'].'-'.$value['grade']}}">{{$value['waitlist_count'] ?? 0}}</span></td>
                            <td class="text-center"><span>{{$value['late_submission_count'] ?? 0}}</span></td>
                            <td class="text-center">{{$value['available_seats'] - $value['offer_count']}}</td>
                            <td class="text-center"><input type="text" class="form-control numberinput" value="{{$value['withdrawn_seats']}}" name="WS-{{$value['program_id'].'-'.$value['grade']}}" id="WS-{{$value['program_id'].'-'.$value['grade']}}" onblur="updateProcessSeats('{{$value['program_id'].'-'.$value['grade']}}')" onkeypress="return onlyNumberKey(event)" @if($display_outcome > 0) disabled @endif></td>
                            <td class="text-center"><span class="process_seats-{{$value['program_id'].'-'.$value['grade']}}">{{$value['available_seats'] - $value['offer_count'] + $value['withdrawn_seats']}}</span></td>
                            <td class="text-center"><input type="text" disabled class="form-control updated_seats-{{$value['program_id'].'-'.$value['grade']}}" value="{{$value['available_seats'] + $value['withdrawn_seats']}}"></td>
                        </tr>
                        @endforeach
                    @endisset
                </tbody>
            </table>

            @if($disabled == 0)
                <div class="text-right"><button type="button" name="value_save" value="value_save" class="btn btn-success mt-10" onclick="saveData()">Save</button></div>
            @endif

            <div id="waitingprograms" class="d-none">
        </div>
        
        
    </div>
    <div class="form-group mt-20">
        <label for="">Last day and time to accept ONLINE</label>
        <div class=""><input class="form-control datetimepicker" name="last_date_late_submission_online_acceptance" id="last_date_late_submission_online_acceptance" value="{{$last_date_online}}" data-date-format="mm/dd/yyyy hh:ii"></div>
    </div>
    <div class="form-group">
        <label for="">Last day and time to accept OFFLINE</label>
        <div class=""><input class="form-control datetimepicker" name="last_date_late_submission_offline_acceptance" id="last_date_late_submission_offline_acceptance" value="{{$last_date_offline}}" data-date-format="mm/dd/yyyy hh:ii"></div>
    </div>
    <div class="text-right">@if($display_outcome == 0)<input type="submit" class="btn btn-success" value="Process Submissions Now"> @else <input type="button" class="btn btn-danger disabled" value="Process Submissions Now"> @endif</div>
    </form>