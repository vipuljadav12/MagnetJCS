@extends('layouts.admin.app')

@section('title', "Edit Enrollment Period")

@section('styles')
<link rel="stylesheet" href="{{url('/resources/assets/admin/css/jquery-ui.css?rand()')}}">
    <style type="text/css">
        .error{
            color: red;
        }
    </style>
@stop

@section('content')

    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Edit Enrollment Period</div>
            <div class=""><a href="{{url('admin/Enrollment')}}" class="btn btn-sm btn-secondary" title="Go Back">Go Back</a></div> 
        </div>
    </div>
    @include('layouts.admin.common.alerts')

    <form id="edit-enrollment" method="post" action="{{url('admin/Enrollment/update/'.$enrollment->id)}}">
        {{csrf_field()}}

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General Information</a></li>
            <li class="nav-item"><a class="nav-link" id="adm-tab" data-toggle="tab" href="#adm" role="tab" aria-controls="adm" aria-selected="false">Racial Composition</a></li>
        </ul>
        <div class="tab-content bordered" id="myTabContent">
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                @include("Enrollment::general_info") 
            </div>
            <div class="tab-pane fade" id="adm" role="tabpanel" aria-labelledby="adm-tab">
                @include("Enrollment::adm_data")
            </div>
        </div>

        <div class="box content-header-floating" id="listFoot">
            <div class="row">
                <div class="col-lg-12 text-right hidden-xs float-right">
                    <button type="submit" class="btn btn-warning btn-xs" name="submit" value="Save" title="Save"><i class="fa fa-save"></i> Save </button>
                    <button type="Submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit" title="Save & Exit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                    <a class="btn btn-danger btn-xs" href="{{url('/admin/Enrollment')}}" title="Cancel"><i class="fa fa-times"></i> Cancel</a>
                </div>
            </div>
        </div>
    </form>
@stop

@section('scripts')
    <!-- InstanceBeginEditable name="Footer Scripts" -->
    <script type="text/javascript" src="{{url('resources/assets/admin/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    {{-- <script src="{{asset('resources/assets/common/js/jquery.validate.min.js')}}"></script> --}}
    {{-- <script src="{{asset('resources/assets/common/js/additional-methods.min.js')}}"></script> --}}

    <script>
        /** Script for general information start **/
        $( function() {
            /* $(".mydatepicker").datepicker({
                    dateFormat: 'yyyy-mm-dd'
                });*/
            // $( "#datepicker01" ).datepicker();
            // $( "#datepicker02" ).datepicker();
            // $( "#datepicker03" ).datepicker();
            // $( "#datepicker04" ).datepicker();
            // $( "#datepicker05" ).datepicker();
            // $( "#datepicker06" ).datepicker();
            // $( "#datepicker07" ).datepicker();

            // datepicker
            
            /*for(var i=1; i<=7; i++){
                $( "#datepicker0"+i ).datepicker({
                    dateFormat: 'Y-m-d'
                });
            }*/
            $("#begning_date").datepicker({
                numberOfMonths: 1,
                autoclose: true,
                dateFormat: 'mm/dd/yy',
            });
            $("#ending_date").datepicker({
                numberOfMonths: 1,
                autoclose: true,
                dateFormat: 'mm/dd/yy',
            });
            $("#first_grade_birthday_cut_off").datepicker({
                numberOfMonths: 1,
                autoclose: true,
                dateFormat: 'mm/dd/yy',
            });
            $("#kindergarten_birthday_cut_off").datepicker({
                numberOfMonths: 1,
                autoclose: true,
                dateFormat: 'mm/dd/yy',
            });
            $("#perk_birthday_cut_off").datepicker({
                numberOfMonths: 1,
                autoclose: true,
                dateFormat: 'mm/dd/yy',
            });

            // Ending Date validation  
            jQuery.validator.addMethod('afterDate', function(value, element, parameters){
                return new Date(value) >= new Date($(parameters).val());
            }, 'End Date should be greater than Begnning Date');

            // Form validation
            $('#edit-enrollment').validate({
                rules:{
                    school_year: {
                        required: true,
                        pattern: /(^[\d]+[\-][\d]+$)/
                    },
                    confirmation_style: {
                        required: true,
                        pattern: /[0-9a-z\s]+$/i
                    },
                    // import_grades_by: {
                    //     required: true
                    // },
                    begning_date: {
                        required: true
                    },
                    ending_date: {
                        required: true,
                        afterDate: $('input[name="begning_date"]')
                    },
                    perk_birthday_cut_off: {
                        required: true
                    },
                    kindergarten_birthday_cut_off: {
                        required: true
                    },
                    first_grade_birthday_cut_off: {
                        required: true
                    }
                },
                messages:{
                    school_year: {
                        required: 'School year is required',
                        pattern: 'Enter valid school year'
                    },
                    confirmation_style: {
                        required: 'Confirmation style is required.',
                        pattern: 'Do not enter special characters.'
                    },
                    // import_grades_by: {
                    //     required: 'Import grades by is required.'
                    // },
                    begning_date: {
                        required: 'Beginning date is required.'
                    },
                    ending_date: {
                        required: 'Ending date is required.'
                    },
                    perk_birthday_cut_off: {
                        required: 'Perk birthday cut off is required.'
                    },
                    kindergarten_birthday_cut_off: {
                        required: 'Kindergarten birthday cut off is required.'
                    },
                    first_grade_birthday_cut_off: {
                        required: 'First grade birthday cut off is required.'
                    }
                },
                errorPlacement: function(error, element){
                    error.insertAfter(element.parent());
                }
            });

        } );
        /** Script for general information end **/

        /** Script for ADM Data start **/
        $(document).on('keypress', '.adm_value', function(e){
            var dot_count = ($(this).val().match(/\./g) || []).length;
            
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                if (e.which != 16 && e.which != 46) {
                    return false;
                }else if (e.which == 46 && dot_count > 0) {
                    return false;
                }
            }    
        });

        $(document).on('keyup', '.adm_value', function() {
            adm_percentage($(this));
        });
        // on load udpate all percentage values
        $('.tbl_adm').find('tbody').find('tr').each(function() {
            var val = $(this).find('.adm_value:first');
            adm_percentage(val);
        });
        function adm_percentage(e) {
            var total = 0;
            var blank_count = 0;
            parent_tr = e.closest('tr');
            parent_tr.find('.adm_value').each(function() {
                var tmp_value = parseFloat($(this).val());
                if (!$.isNumeric(tmp_value)) {
                    tmp_value = 0;
                    blank_count++;
                }
                total = total+tmp_value;
            });
            if (total != 0 && total != 100) {
                total = total.toFixed(2);
            }
            parent_tr.find('.adm_percent').text(total);
            // console.log(blank_count);
            return blank_count;
        }

        $('#edit-enrollment').submit(function() {
            var valid_check = true;
            $('.adm_percent').each(function() {
                var tmp_percent = parseFloat($(this).text());
                var adm_err = $(this).closest('td').find('.adm_error');
                // Except blanks
                var inp_obj = $(this).closest('tr').find('.adm_value:first');
                var blank_count = adm_percentage(inp_obj);
                // blank_count < 3
                if (
                    (tmp_percent < 100 || tmp_percent > 100) &&
                    (blank_count < 3)
                    ) {
                    if (adm_err.hasClass('d-none')) {
                        adm_err.removeClass('d-none');
                    }
                    valid_check = false;
                }else {
                    if (!adm_err.hasClass('d-none')) {
                        adm_err.addClass('d-none');
                    }
                }
            });
            if (valid_check == false) {
                return false;
            }
        });
        /** Script for ADM Data end **/

        // float digits validation
        $('.digit_float').on('keypress', function() {
            var $this = $(this);
            if ((event.which != 46 || $this.val().indexOf('.') != -1) &&
               ((event.which < 48 || event.which > 57) &&
               (event.which != 0 && event.which != 8))) {
                   event.preventDefault();
            }
        });
    </script>         

@stop