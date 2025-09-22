 <div class="">
    <div class="card shadow">
        {{-- <div class="card-header">Selection Process Set Up</div> --}}
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="form-group">
                        <div class="mb-3">
                            {{-- <label for="table26" class="mr-10"> </label> --}}
                            <select class="form-control custom-select ranking_system col-6" name="exception_choice" id="exception_choice">
                                <option value="">Select Exception Type</option>
                                @if($rec_form_data['status'])
                                    <option value="recommendation_form" @if($exception_choice == 'recommendation_form') selected @endif>Recommendation Form</option>
                                @endif
                                <option value="program_choice" @if($exception_choice == 'program_choice') selected @endif>Choice Program Change</option>
                            </select>
                        </div>
                        <div class="exception_content">
                            @if ($exception_choice != '')
                                @php
                                    $grade_lavel = explode(',', $program->grade_lavel);
                                @endphp
                                @if ($exception_choice == 'recommendation_form')
                                    <input type="hidden" name="assigned_eligibility_id" value="{{ $rec_form_data['eligibility_id'] }}">
                                    @php
                                        $i = 0;
                                        $j = 0;
                                        // $grade_lavel = explode(',', $program->grade_lavel);
                                        $rec_subj = config('variables.recommendation_subject');
                                    @endphp
                                    <div class="form-group ifDD ">
                                        <label class="control-label">Recommendation Subjects :</label>
                                        <div class="row">
                                            @foreach($grade_lavel as $gvalue)
                                                <div class="col-12 col-lg-3 mb-30 ay_main">
                                                    <div class="custom-outer-box">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input grade_lvl ay_parent" id="grade_lvl_{{$i}}" value="{{$gvalue}}" name="extra[grade][]" @if(isset($rec_form_data['data'][$gvalue])) checked @endif>
                                                            <label for="grade_lvl_{{$i}}" class="custom-control-label">{{$gvalue}}</label>
                                                        </div>
                                                        <div class="custom-sub-box grade_lvl_{{$i}}" @if(!isset($rec_form_data['data'][$gvalue])) style="display: none;" @endif>
                                                            @foreach($rec_subj as $rskey=>$rsvalue)
                                                                <div class="pl-20 custom-sub-child">
                                                                    <div class="custom-control custom-checkbox">
                                                                        @php
                                                                            $is_checked = '';
                                                                            if( isset($rec_form_data['data'][$gvalue]) && 
                                                                                !empty($rec_form_data['data'][$gvalue]) && 
                                                                                in_array($rskey, $rec_form_data['data'][$gvalue]) ) 
                                                                            {
                                                                                $is_checked = 'checked';
                                                                            } 
                                                                        @endphp
                                                                        <input type="checkbox" class="custom-control-input ay_child" id="rec_subj_{{$j}}" value="{{$rskey}}" name="extra[rec_subj][{{$gvalue}}][]" {{$is_checked}}>
                                                                    <label for="rec_subj_{{$j}}" class="custom-control-label">{{$rsvalue}}</label></div>
                                                                </div>
                                                                @php $j++ @endphp
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                @php $i++ @endphp
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif ($exception_choice == 'program_choice')
                                    <div class="form-group">
                                        <label class="control-label">Program Choice :</label>
                                        @foreach($grade_lavel as $gkey=>$gvalue)
                                            @if(is_numeric($gvalue) && $gvalue > 1)
                                                @php $cgrade = $gvalue-1 @endphp
                                            @elseif($gvalue == 1)
                                                @php $cgrade = "PreK" @endphp
                                            @elseif($gvalue == "PreK")
                                                @php $cgrade = "K" @endphp
                                            @else
                                                @php $cgrade = "K" @endphp
                                            @endif
                                            <div class="row col-12 mb-3 pc_parent">
                                                @php
                                                    $grade_check = $rec_form_data['data']->where('grade', $cgrade)->first();
                                                    $display_name = $grade_check->display_name ?? '';
                                                    // dd($grade_check);
                                                @endphp
                                                <div class="mt-5 custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input pc_chkbx" id="grade_lvl_{{$gkey}}" name="extra[grade][]" value="{{$cgrade}}" @if(isset($grade_check)) checked @endif>
                                                    <label for="grade_lvl_{{$gkey}}" class="custom-control-label">{{$cgrade}}</label>
                                                </div>
                                                <div class="col-5 ml-4 pc_input d-none">
                                                    <input class="form-control" type="text" name="extra[name][{{$cgrade}}]" value="{{$display_name}}" maxlength="200">
                                                    <sup style="color: red;">* </sup>For next academic year, use ##NEXT_YEAR##
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            
                                <div class="" align="right">  
                                    <button form="frm_exception" class="btn btn-success" id="rec_frm_save" type="submit">Save</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <style type="text/css">
    <style>
    .custom-sub-box {position: relative}
    .custom-sub-box:before {content: ""; position: absolute; left: 20px; top: 20px; background: #ccc; width: 2px; bottom: 11px;}
    .custom-sub-child {position: relative}
    .custom-sub-child:before {content: ""; position: absolute; left: 7px; top: 11px; background: #ccc; height: 2px; width: 20px;}
    .custom-checkbox .custom-control-input:checked~.custom-control-label::before {background-color: #00346b;}
</style>

</style> --}}
@section('exception_script')
<script type="text/javascript">
    $(document).on('change', '#exception_choice', function() {
        var op_val = $(this).val();
        if (op_val != '') {
            window.location.href = '{{url('/')}}/admin/Program/edit/'+'{{$program->id}}?exception_choice='+op_val;
        }
    });

    @if ($exception_choice == 'recommendation_form')
        // In checkbox Uncheck child when parent unchecked
        $(document).on('change', '.ay_parent', function() {
            if ($(this).prop('checked') == false) {
                $(this).closest('.ay_main').find('.ay_child').prop('checked', false).trigger('change');
            }
        });
        // Hide/show
        $(document).on('click', '.grade_lvl', function(){
            var id = $(this).attr('id');

            if($(this).is(':checked')){
                $(document).find('.'+id).show();
            }else{
                $(document).find('.'+id).hide();

            }
        });
    @elseif ($exception_choice == 'program_choice')
        $('.pc_parent').find('.pc_chkbx').each(function() {
            hideShowInput($(this));
        });
        $(document).on('change', '.pc_chkbx', function() {
            hideShowInput($(this));
        });
        function hideShowInput(e) {
            var input_obj = e.closest('.pc_parent').find('.pc_input');
            if (e.prop('checked')) {
                input_obj.removeClass('d-none');
            } else {
                input_obj.addClass('d-none');
            }
        }
    @endif

    @if ($exception_choice != '')
        // Recommendation form save
        $('form#frm_exception').submit(function() {
            var content = $('.exception_content').clone();
            $('.exception_tab_content').find('#frm_exception').append(content);
        });
    @endif

</script>
@endsection