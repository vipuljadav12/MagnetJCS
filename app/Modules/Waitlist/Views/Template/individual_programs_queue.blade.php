        
    <div class="table-responsive" style="height: 395px; overflow-y: auto;">
        <strong>Following Programs will be processed:</strong>
       <table class="table m-0" id="tbl_population_changes">
                <thead>
                    <tr>
                        <th class="">Program Name</th>
                        <th class="text-center">Original Entered Available Seats</th>
                        <th class="text-center">Current Offered and Accepted</th>
                        <th class="text-center">Waitlisted</th>
                        <th class="text-center">Remaining Available Seats</th>
                        <th class="text-center">Withdrawn Students Count to Add</th>
                        <th class="text-center">Seats to Process</th>
                        <th class="text-center">Updated Available Seats</th>
                    </tr>
                </thead>
                <tbody>
                    @isset($data_ary)
                        @foreach($data_ary as $key=>$value)
                        <tr>
                            @php
                                $available_seats = $value->available_seats ?? 0;
                            @endphp
                            <td class="">{{getProgramName($value['program_id'])}} - Grade {{$value['grade']}}</td>
                            <td class="text-center"><span id="available_seats-{{$value['program_id'].'-'.$value['grade']}}">{{$value['available_seats'] ?? 0}}</span></td>
                            <td class="text-center"><span id="offer_count-{{$value['program_id'].'-'.$value['grade']}}">{{$value['offer_count'] ?? 0}}</span></td>
                            <td class="text-center"><span id="waitlist_count-{{$value['program_id'].'-'.$value['grade']}}">{{$value['waitlist_count'] ?? 0}}</span></td>
                            <td class="text-center">{{$value['available_seats'] - $value['offer_count']}}</td>
                            <td class="text-center"><input type="text" class="form-control numberinput" value="{{$value['withdrawn_seats']}}" name="WS-{{$value['program_id'].'-'.$value['grade']}}" id="WS-{{$value['program_id'].'-'.$value['grade']}}" onblur="updateProcessSeats('{{$value['program_id'].'-'.$value['grade']}}')" onkeypress="return onlyNumberKey(event)" disabled></td>
                            <td class="text-center"><span class="process_seats-{{$value['program_id'].'-'.$value['grade']}}">{{$value['available_seats'] - $value['offer_count'] + $value['withdrawn_seats']}}</span></td>
                            <td class="text-center"><input type="text" disabled class="form-control updated_seats-{{$value['program_id'].'-'.$value['grade']}}" value="{{$value['available_seats'] + $value['withdrawn_seats']}}"></td>
                        </tr>
                        @endforeach
                    @endisset
                </tbody>
            </table>

        
    </div>
