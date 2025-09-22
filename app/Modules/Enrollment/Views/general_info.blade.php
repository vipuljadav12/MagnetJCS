{{-- <form id="edit-enrollment" method="post" action="{{url('admin/Enrollment/update/'.$enrollment->id)}}">
    {{csrf_field()}} --}}
    <div class="card shadow">
        <div class="card-header">Open Enrollment</div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="">School Year*</label>
                        <div class="">
                            <input name="school_year" value="{{$enrollment->school_year}}" type="text" maxlength="10" class="form-control">
                            @if($errors->has('school_year'))
                                <span class="error">
                                    {{$errors->first('school_year')}}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="">Confirmation Style*</label>
                        <div class="">
                            <input name="confirmation_style" value="{{$enrollment->confirmation_style}}" type="text" maxlength="30" class="form-control">
                            @if($errors->has('confirmation_style'))
                                <span class="error">
                                    {{$errors->first('confirmation_style')}}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12">
                    <div class="form-group">
                        <label for="">Import Grades By (cannot be changed after Beginning Date)</label>
                        <div class="">
                            <select name="import_grades_by" class="form-control custom-select">
                                <option value="">Select</option>
                                <option value="submission_date" 
                                    @if($enrollment->import_grades_by=='SD')
                                    selected
                                    @endif>Submission Date
                                </option>
                            </select>
                            @if($errors->has('import_grades_by'))
                                <span class="error">
                                    {{$errors->first('import_grades_by')}}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="">Beginning Date (1st day of open enrollment period)*</label>
                        <div class="">
                            <input name="begning_date" value="{{date('m/d/Y', strtotime($enrollment->begning_date))}}" class="form-control" id="begning_date">
                            @if($errors->has('begning_date'))
                                <span class="error">
                                    {{$errors->first('begning_date')}}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="">Ending Date (last day of open enrollment period ending at 11:59 PM)*</label>
                        <div class="">
                            <input name="ending_date" value="{{date('m/d/Y', strtotime($enrollment->ending_date))}}" class="form-control" id="ending_date">
                            @if($errors->has('ending_date'))
                                <span class="error">
                                    {{$errors->first('ending_date')}}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <!--<div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="">Late Application Beginning date (1st Day to accept late applications)*</label>
                        <div class="">
                            <input class="form-control" id="datepicker03">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="">Late Application Ending date (system will accept applications up until 11:59 PM on this date)*</label>
                        <div class="">
                            <input class="form-control" id="datepicker04">
                        </div>
                    </div>
                </div>-->
            </div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-header">Application Cut offs<br>
            <small>Applications must be born on or before:</small></div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="">PreK Birthday Cut Off*</label>
                        <div class="">
                            <input name="perk_birthday_cut_off" value="{{date('m/d/Y', strtotime($enrollment->perk_birthday_cut_off))}}" class="form-control" id="perk_birthday_cut_off">
                            <small>After this date, submissions applying for Pre Kindergarten will not be accepted.</small> <br>
                            @if($errors->has('perk_birthday_cut_off'))
                                <span class="error">
                                    {{$errors->first('perk_birthday_cut_off')}}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="">Kindergarten Birthday Cut Off*</label>
                        <div class="">
                            <input name="kindergarten_birthday_cut_off" value="{{date('m/d/Y', strtotime($enrollment->kindergarten_birthday_cut_off))}}" class="form-control" id="kindergarten_birthday_cut_off">
                            <small>After this date, submissions applying for Kindergarten will not be accepted.</small>  <br>
                            @if($errors->has('kindergarten_birthday_cut_off'))
                                <span class="error">
                                    {{$errors->first('kindergarten_birthday_cut_off')}}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="">First Grade Birthday Cut Off *</label>
                        <div class="">
                            <input name="first_grade_birthday_cut_off" value="{{date('m/d/Y', strtotime($enrollment->first_grade_birthday_cut_off))}}" class="form-control" id="first_grade_birthday_cut_off">
                             <small>After this date, submissions applying for First grade will not be accepted.</small>  <br>
                            @if($errors->has('first_grade_birthday_cut_off'))
                                <span class="error">
                                    {{$errors->first('first_grade_birthday_cut_off')}}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="box content-header-floating" id="listFoot">
        <div class="row">
            <div class="col-lg-12 text-right hidden-xs float-right">
                <button type="submit" class="btn btn-warning btn-xs" name="submit" value="Save" title="Save"><i class="fa fa-save"></i> Save </button>
                <button type="Submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit" title="Save & Exit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                <a class="btn btn-danger btn-xs" href="{{url('/admin/Enrollment')}}" title="Cancel"><i class="fa fa-times"></i> Cancel</a>
            </div>
        </div>
    </div> --}}
{{-- </form> --}}