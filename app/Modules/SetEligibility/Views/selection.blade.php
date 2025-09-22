 <div class="">
    <div class="card shadow">
        <div class="card-header">Selection Process Set Up</div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="form-group">
                        <label class="control-label"><strong>Ranking System :</strong> </label>
                        <div class="row flex-wrap">
                            <div class="col-12 col-sm-4 form-group">
                                <label for="table26" class="mr-10">First Applicants Over Late Applicants : </label>
                                <div class="">
                                    <select class="form-control custom-select ranking_system" name="current_over_new" id="current_over_new">
                                        <option value="">NA</option>
                                        <option value="1" {{$program->current_over_new=='1'?'selected':''}}>1</option>
                                        <option value="2" {{$program->current_over_new=='2'?'selected':''}}>2</option>
                                        <option value="3" {{$program->current_over_new=='3'?'selected':''}}>3</option>
                                        <option value="4" {{$program->current_over_new=='4'?'selected':''}}>4</option>
                                        <option value="5" {{$program->current_over_new=='5'?'selected':''}}>5</option>
                                        <option value="6" {{$program->current_over_new=='6'?'selected':''}}>6</option>
                                        <option value="7" {{$program->current_over_new=='7'?'selected':''}}>7</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 form-group">
                                <label for="table26" class="mr-10">Priority : </label>
                                <div class="">
                                    <select class="form-control custom-select ranking_system" name="rating_priority" id="rating_priority">
                                        <option value="">NA</option>
                                        <option value="1" {{$program->rating_priority=='1'?'selected':''}}>1</option>
                                        <option value="2" {{$program->rating_priority=='2'?'selected':''}}>2</option>
                                        <option value="3" {{$program->rating_priority=='3'?'selected':''}}>3</option>
                                        <option value="4" {{$program->rating_priority=='4'?'selected':''}}>4</option>
                                        <option value="5" {{$program->rating_priority=='5'?'selected':''}}>5</option>
                                        <option value="6" {{$program->rating_priority=='6'?'selected':''}}>6</option>
                                        <option value="7" {{$program->rating_priority=='7'?'selected':''}}>7</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 form-group">
                                <label for="table26" class="mr-10">Lottery Number : </label>
                                <div class="">
                                    <select class="form-control custom-select ranking_system" name="lottery_number" id="lottery_number">
                                        <option value="">NA</option>
                                        <option value="1" {{$program->lottery_number=='1'?'selected':''}}>1</option>
                                        <option value="2" {{$program->lottery_number=='2'?'selected':''}}>2</option>
                                        <option value="3" {{$program->lottery_number=='3'?'selected':''}}>3</option>
                                        <option value="4" {{$program->lottery_number=='4'?'selected':''}}>4</option>
                                        <option value="5" {{$program->lottery_number=='5'?'selected':''}}>5</option>
                                        <option value="6" {{$program->lottery_number=='6'?'selected':''}}>6</option>
                                        <option value="7" {{$program->lottery_number=='7'?'selected':''}}>7</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 form-group">
                                <label for="table26" class="mr-10">Committee Score : </label>
                                <div class="">
                                    <select class="form-control custom-select ranking_system" name="committee_score" id="committee_score">
                                        <option value="">NA</option>
                                        <option value="1" {{$program->committee_score=='1'?'selected':''}}>1</option>
                                        <option value="2" {{$program->committee_score=='2'?'selected':''}}>2</option>
                                        <option value="3" {{$program->committee_score=='3'?'selected':''}}>3</option>
                                        <option value="4" {{$program->committee_score=='4'?'selected':''}}>4</option>
                                        <option value="5" {{$program->committee_score=='5'?'selected':''}}>5</option>
                                        <option value="6" {{$program->committee_score=='6'?'selected':''}}>6</option>
                                        <option value="7" {{$program->committee_score=='7'?'selected':''}}>7</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 form-group">
                                <label for="table26" class="mr-10">Combined Score : </label>
                                <div class="">
                                    <select class="form-control custom-select ranking_system" name="combine_score" id="combine_score">
                                        <option value="">NA</option>
                                        <option value="1" {{$program->combine_score=='1'?'selected':''}}>1</option>
                                        <option value="2" {{$program->combine_score=='2'?'selected':''}}>2</option>
                                        <option value="3" {{$program->combine_score=='3'?'selected':''}}>3</option>
                                        <option value="4" {{$program->combine_score=='4'?'selected':''}}>4</option>
                                        <option value="5" {{$program->combine_score=='5'?'selected':''}}>5</option>
                                        <option value="6" {{$program->combine_score=='6'?'selected':''}}>6</option>
                                        <option value="7" {{$program->combine_score=='7'?'selected':''}}>7</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 form-group">
                                <label for="table26" class="mr-10">Audition Score : </label>
                                <div class="">
                                    <select class="form-control custom-select ranking_system" name="audition_score" id="audition_score">
                                        <option value="">NA</option>
                                        <option value="1" {{$program->audition_score=='1'?'selected':''}}>1</option>
                                        <option value="2" {{$program->audition_score=='2'?'selected':''}}>2</option>
                                        <option value="3" {{$program->audition_score=='3'?'selected':''}}>3</option>
                                        <option value="4" {{$program->audition_score=='4'?'selected':''}}>4</option>
                                        <option value="5" {{$program->audition_score=='5'?'selected':''}}>5</option>
                                        <option value="6" {{$program->audition_score=='6'?'selected':''}}>6</option>
                                        <option value="7" {{$program->audition_score=='7'?'selected':''}}>7</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 form-group">
                                <label for="table26" class="mr-10">Final Score : </label>
                                <div class="">
                                    <select class="form-control custom-select ranking_system" name="final_score" id="final_score">audition_scorefinal_score
                                        <option value="">NA</option>
                                        <option value="1" {{$program->final_score=='1'?'selected':''}}>1</option>
                                        <option value="2" {{$program->final_score=='2'?'selected':''}}>2</option>
                                        <option value="3" {{$program->final_score=='3'?'selected':''}}>3</option>
                                        <option value="4" {{$program->final_score=='4'?'selected':''}}>4</option>
                                        <option value="5" {{$program->final_score=='5'?'selected':''}}>5</option>
                                        <option value="6" {{$program->final_score=='6'?'selected':''}}>6</option>
                                        <option value="7" {{$program->final_score=='7'?'selected':''}}>7</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-12">
                    <div class="form-group">
                        <label class="control-label"><strong>Selection Method :</strong> </label>
                        <div class="row flex-wrap">
                            @if(isset($district->desegregation_compliance)&&$district->desegregation_compliance=='Yes')
                                <div class="col-12 col-sm-4">
                                <div class="custom-control custom-radio"><input type="radio" disabled="" name="selection_method"value="Racial Composition" class="custom-control-input selection_method" id="table27" {{$program->selection_method=='Racial Composition'?'checked':''}}>
                                    <label for="table27" class="custom-control-label">Racial Composition</label></div>
                            </div>
                            @endif
                            <div class="col-12 col-sm-4">
                                <div class="custom-control custom-radio"><input type="radio" disabled="" name="selection_method" value="Home Zone Placement" class="custom-control-input selection_method" id="table23" {{$program->selection_method=='Home Zone Placement'?'checked':''}}>
                                    <label for="table23" class="custom-control-label">Home Zone Placement</label></div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="custom-control custom-radio"><input type="radio" disabled="" name="selection_method" value="Lottery Number Only" class="custom-control-input selection_method" id="table24" {{$program->selection_method=='Lottery Number Only'?'checked':''}}>
                                    <label for="table24" class="custom-control-label">Lottery Number Only</label></div>
                            </div>
                        </div>
                        @if($errors->first('selection_method'))
                            <div class="mb-1 text-danger">
                                {{$errors->first('selection_method')}}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-lg-12">
                    <div class="row flex-wrap">
                        <div class="col-12 form-group">
                            <label for="table26" class="mr-10"><strong>Selection By : </strong></label>
                            <div class="">
                                <select class="form-control custom-select" name="selection_by" disabled="" id="selection_by">
                                    <option value="">Select</option>
                                    <option value="Program Name" {{$program->selection_by=='Program Name'?'selected':''}}>Program Name</option>
                                    <option value="Application Filter 1" {{$program->selection_by=='Application Filter 1'?'selected':''}}>Application Filter 1</option>
                                    <option value="Application Filter 2" {{$program->selection_by=='Application Filter 2'?'selected':''}}>Application Filter 2</option>
                                    <option value="Application Filter 3" {{$program->selection_by=='Application Filter 3'?'selected':''}}>Application Filter 3</option>
                                </select>
                            </div>
                            @if($errors->first('selection_by'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('selection_by')}}
                                </div>
                            @endif
                        </div>

                       {{-- <div class="col-12 col-sm-4 form-group">
                            <label for="table26" class="mr-10"><strong>Selection By : </strong></label>
                            <div class="">
                                <select class="form-control custom-select" id="home_zone_sel">
                                    <option value="">Select</option>
                                    <option value="">Program Name</option>
                                    <option value="">Application Filter 1</option>
                                    <option value="">Application Filter 2</option>
                                    <option value="">Application Filter 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 form-group">
                            <label for="table26" class="mr-10"><strong>Selection By : </strong></label>
                            <div class="">
                                <select class="form-control custom-select" id="Lottery_num_sel">
                                    <option value="">Select</option>
                                    <option value="">Program Name</option>
                                    <option value="">Application Filter 1</option>
                                    <option value="">Application Filter 2</option>
                                    <option value="">Application Filter 3</option>
                                </select>
                            </div>
                        </div>--}}
                        <div class="col-12 form-group">
                            <label for="table26" class="mr-10"><strong>Seat Availability Entered by : </strong></label>
                            <div class="">
                                <select class="form-control custom-select" name="seat_availability_enter_by" disabled="" id="seat_availability_enter_by">
                                    <option value="">Select</option>
                                    <option  value="Manual Entry" {{$program->seat_availability_enter_by=='Manual Entry'?'selected':''}}>Manual Entry</option>
                                    <option value="Calculation" {{$program->seat_availability_enter_by=='Calculation'?'selected':''}}>Calculation</option>
                                </select>
                            </div>
                        </div>
                        @if($errors->first('seat_availability_enter_by'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('seat_availability_enter_by')}}
                            </div>
                        @endif
                       {{-- <div class="col-12 col-sm-4 form-group">
                            <label for="table26" class="mr-10"><strong>Seat Availability Entered by : </strong></label>
                            <div class="">
                                <select class="form-control custom-select" id="home_zone_seat">
                                    <option value="">Select</option>
                                    <option value="">Manual Entry</option>
                                    <option value="">Calculation</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 form-group">
                            <label for="table26" class="mr-10"><strong>Seat Availability Entered by : </strong></label>
                            <div class="">
                                <select class="form-control custom-select" id="Lottery_num_seat">
                                    <option value="">Select</option>
                                    <option value="">Manual Entry</option>
                                    <option value="">Calculation</option>
                                </select>
                            </div>
                        </div>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>