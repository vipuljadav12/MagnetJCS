<style type="text/css">
    .disable-row{background: #f5f5f5 !important}
    .enable-row{}
    .hide-table{visibility: hidden !important}
    .show-table{}
</style>
<div class="tab-pane fade show active" id="preview03" role="tabpanel" aria-labelledby="preview03-tab">
    <form action="{{url('/admin/Waitlist/Process/Selection/'.$application_id.'/store')}}" method="post" id="process_selection">
             {{csrf_field()}}
             <input type="hidden" name="type" id="type" value="update">
             <input type="hidden" name="process_event" id="process_event" value="">
    <div class="table-responsive" style="height: 500px; overflow-y: auto;">
        
       <table class="table m-0" id="tbl_population_changes">
                <thead>
                    <tr>
                        @if($display_outcome == 0)
                            <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important"><input type="checkbox" id="checkall" onchange="checkUncheckAll($(this))"><br>Check/Uncheck All</th>
                        @endif
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important"></th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Original Seats Available</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Current <br>Enrolled<br>Student Withdrawn</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Black</th>
                        <th class="text-center d-none" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">White</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Non-Black</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Waitlisted</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Available Slots</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Additional Seats</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Slots to Award</th>
                    </tr>
                </thead>
                <tbody>
                    @if($display_outcome == 1)
                        @foreach($waitlist_process_logs as $key=>$value)
                               
                                    <tr>
                                        <td>{{$value->program_name}}</td>
                                        <td class="text-center">{{$value->total_seats}}</td>
                                        <td class="text-center"><select class="form-control" style="width:100px;" disabled>
                                                    <option value="No" @if($value->withdrawn_student == "No") selected @endif>No</option>
                                                    <option value="Yes" @if($value->withdrawn_student == "Yes") selected @endif>Yes</option>
                                                </select>
                                            </span>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-center" style="width: 100px;" value="{{$value->black_withdrawn}}" disabled>
                                        </td>
                                        <td class="d-none">
                                            <input type="text" class="form-control text-center" style="width: 100px;" value="{{$value->white_withdrawn}}" disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-center" style="width: 100px;" value="{{$value->other_withdrawn}}" disabled>
                                        </td>
                                        <td class="text-center">{{$value->waitlisted}}</td>
                                        <td class="text-center">{{$value->available_slots}}</td>
                                        <td class="text-center">{{$value->additional_seats}}</td>
                                        <td class="text-center">{{$value->slots_to_awards}}</td>
                                    </tr>
                        @endforeach
                    @else
                        @isset($disp_arr)
                            @php $count = 0 @endphp
                            @foreach($disp_arr as $key=>$value)
                                <tr class="bg-info text-white"><td colspan="11">{{$key}}</td></tr>
                                @foreach($value as $wkey=>$wvalue)
                                    @php ($class = ($wvalue['visible'] == "N" ? "disable-row" : "enable-row")) @endphp
                                    @php ($spanclass = ($wvalue['visible'] == "N" ? "hide-table" : "show-table")) @endphp

                                    <tr class="{{$class}}" id="row{{$wvalue['application_program_id']}}">
                                        <td><input type="checkbox" value="{{$wvalue['application_program_id']}}" name="application_program_id[]" class="check_selector" onchange="showHideRow({{$wvalue['application_program_id']}}, $(this))" @if($wvalue['visible'] == "Y") checked @endif></td>
                                        <td>{{$wvalue['name']}}
                                            <input type="hidden" value="{{$wvalue['name']}}" name="program_name{{$wvalue['application_program_id']}}">
                                            <input type="hidden" value="{{$wvalue['id']}}" name="program_id{{$wvalue['application_program_id']}}">
                                            <input type="hidden" value="{{$wvalue['grade']}}" name="grade{{$wvalue['application_program_id']}}">
                                        </td>
                                        <td class="text-center"><span class="{{$spanclass}}">{{$wvalue['total_seats']}}</span>
                                            <input type="hidden" value="{{ $wvalue['total_seats'] }}" name="total_seats{{$wvalue['application_program_id']}}">
                                        </td>
                                        <td class="text-center">
                                            <span class="{{$spanclass}}">
                                                <select class="form-control withdrawn_student" style="width:100px;" onchange="enableDisableWithdrawn({{$count}}, $(this).val())" data-application_program_id="{{$wvalue['application_program_id']}}" name="withdrawn_student{{$wvalue['application_program_id']}}">
                                                    <option value="No" @if($wvalue['withdrawn_student'] == "No") selected @endif>No</option>
                                                    @if($wvalue['withdrawn_allowed'] == "Yes")
                                                        <option value="Yes" @if($wvalue['withdrawn_student'] == "Yes") selected @endif>Yes</option>
                                                    @endif
                                                </select>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="{{$spanclass}}">
                                                <input type="text" class="form-control text-center" style="width: 100px;" id="black{{$count}}" value="{{$wvalue['black_withdrawn']}}" @if($spanclass=="hide-table" || $wvalue['withdrawn_student'] == "No") disabled @endif onchange="updateAwardSlot({{$count}})" onkeypress="return onlyNumberKey(event)" name="black{{$wvalue['application_program_id']}}">
                                            </span>
                                        </td>
                                        <td class="d-none">
                                            <span class="{{$spanclass}}">
                                                <input type="text" class="form-control text-center" style="width: 100px;" id="white{{$count}}" value="{{$wvalue['white_withdrawn']}}"  @if($spanclass=="hide-table" || $wvalue['withdrawn_student'] == "No") disabled @endif onchange="updateAwardSlot({{$count}})" onkeypress="return onlyNumberKey(event)" name="white{{$wvalue['application_program_id']}}">
                                            </span>
                                        </td>
                                        <td>
                                            <span class="{{$spanclass}}">
                                                <input type="text" class="form-control text-center" style="width: 100px;" id="other{{$count}}" value="{{$wvalue['other_withdrawn']}}"  @if($spanclass=="hide-table" || $wvalue['withdrawn_student'] == "No") disabled @endif onchange="updateAwardSlot({{$count}})" onkeypress="return onlyNumberKey(event)" name="other{{$wvalue['application_program_id']}}">
                                            </span>
                                        </td>
                                        <td class="text-center"><span class="{{$spanclass}}">{{ $wvalue['waitlist_count'] }}</span>
                                            <input type="hidden" value="{{ $wvalue['waitlist_count'] }}" name="waitlist_count{{$wvalue['application_program_id']}}">
                                        </td>
                                        <td class="text-center"><span class="{{$spanclass}}" id="available_slot{{$count}}">{{ $wvalue['available_count'] }}</span><input type="hidden" value="{{$wvalue['available_count']}}" name="available_slot{{$wvalue['application_program_id']}}"></td>

                                        <td class="text-center"><span class="{{$spanclass}}"><input type="text" value="{{$wvalue['additional_seats']}}" name="additional_seats{{$wvalue['application_program_id']}}" onchange="updateAwardSlot({{$count}})" onkeypress="return onlyNumberKey(event)" class="form-control" style="width: 100px;"  id="additional_seats{{$count}}"></span></td>
                                        
                                        <td><span class="{{$spanclass}}" id="awardslot_span{{$count}}">{{$wvalue['available_slot']}}</span>
                                            <input type="hidden" value="{{$wvalue['available_slot']}}" id="awardslot{{$count}}" name="awardslot{{$wvalue['application_program_id']}}">
                                            </span>
                                        </td>
                                    </tr>
                                    
                                    @if(!empty($wvalue['zoned_schools']))
                                       
                                        <tr class="hzs_container_{{$wvalue['application_program_id']}}">
                                            <td colspan="11">
                                                <div class="col-lg-12 row"> 
                                        @foreach($wvalue['zoned_schools'] as $hzs_key=>$hzs_chunk)
                                                
                                              <div class="col-lg-3 pt-5 inline-block">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <p class="text-center"><strong>{{$hzs_key}}</strong></p>
                                                        <p style="display: inline-block; width: 50%" class="pr-5">
                                                        Black <input type="text" name="zoned_schools[{{$wvalue['application_program_id']}}][{{$hzs_key}}][black]" class="form-control text-center hzs_val" onkeypress="return onlyNumberKey(event)" style="width: 50px;" value="{{($wvalue['zoned_school'][$hzs_key]['black'] ?? 0)}}"></p>
                                                        <p  style="display: inline-block">  Non-Black <input type="text" name="zoned_schools[{{$wvalue['application_program_id']}}][{{$hzs_key}}][non_black]" class="form-control text-center hzs_val" onkeypress="return onlyNumberKey(event)" style="width: 50px;" value="{{($wvalue['zoned_school'][$hzs_key]['non_black'] ?? 0)}}"> </p>
                                                    </div>
                                                    </div>
                                                    
                                            </div>
                                        @endforeach
                                        </div>
                                        </td>
                                        </tr>
                                    @endif
                                    @php $count++ @endphp
                                @endforeach
                                
                            @endforeach
                        @endisset
                    @endif
                </tbody>
            </table>

            
            
        
        
    </div>

<div class="text-right"><button type="button" name="value_save" value="value_save" class="btn btn-success mt-10" onclick="saveData()">Save</button></div>
    <div class="form-group mt-20">
        <label for="">Last day and time to accept ONLINE</label>
        <div class=""><input class="form-control datetimepicker" name="last_date_online_acceptance" id="last_date_online_acceptance" value="{{$last_date_online_acceptance}}" data-date-format="mm/dd/yyyy hh:ii"></div>
    </div>
    <div class="form-group">
        <label for="">Last day and time to accept OFFLINE</label>
        <div class=""><input class="form-control datetimepicker" name="last_date_offline_acceptance" id="last_date_offline_acceptance" value="{{$last_date_offline_acceptance}}" data-date-format="mm/dd/yyyy hh:ii"></div>
    </div>
    <div class="text-right">@if($display_outcome == 0)<input type="submit" class="btn btn-success" value="Process Submissions Now"> @else <input type="button" class="btn btn-danger disabled" value="Process Submissions Now"> @endif</div>
    </form>
</div>

