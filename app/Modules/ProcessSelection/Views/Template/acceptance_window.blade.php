<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <div class="">
       
        
        <div class="card shadow">
            <div class="card-header">Acceptance Window</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <label class="">Last day and time to accept ONLINE</label>

                        <div class="input-append date form_datetime">
                        <input class="form-control datetimepicker" name="last_date_online_acceptance" id="last_date_online_acceptance" value="{{$last_date_online_acceptance}}" data-date-format="mm/dd/yyyy hh:ii">
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="">Last day and time to accept OFFLINE</label>
                        <div class="input-append date form_datetime"> <input class="form-control datetimepicker" name="last_date_offline_acceptance" id="last_date_offline_acceptance"  value="{{$last_date_offline_acceptance}}" data-date-format="mm/dd/yyyy hh:ii"></div>
                    </div>
                   

                </div>
                <div class="row pt-10">
                     <div class="col-12 pb-10"><strong>Programs to Process</strong></div>
                     <div class="form-group col-12">
                                @foreach($programs as $key=>$program)
                                   <div class="">
                                            <div class="">
                                                <input type="checkbox" name="program_id[]" class="" value="{{$program->id}}" @if(in_array($program->id, $processed_programs)) checked @endif @if(empty($processed_programs)) checked @endif>
                                                <label class="">{{$program->name}}</label>
                                            </div>
                                        </div>
                                    
                                @endforeach
                            </div>
                </div>
                <div class="text-right pt-20"><input type="submit" class="btn btn-success" title="Process Submissions Now" value="Process Submissions Now"></div>    
            </div>
        </div>
        
    </div>
