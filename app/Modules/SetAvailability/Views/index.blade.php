@extends("layouts.admin.app")
@section("title")
	Set Availability | {{config('app.name',env("APP_NAME"))}}
@endsection
@section("content")
<div class="content-wrapper-in">
	<!-- InstanceBeginEditable name="Content-Part" -->
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Set Availability</div>
            <div class="">
                <a href="{{url('')}}/admin/Availability/import" class="text-white btn btn-sm btn-success" title="">Import Availability</a>
                <a href="{{url('')}}/admin/Availability/import_rising_composition" class="text-white btn btn-sm btn-success" title="">Import Rising Composition</a>
            </div>
            <!--<div class=""><a href="add-district.html" class="btn btn-sm btn-secondary" title="">Add District</a></div>-->
        </div>
    </div>
    <form class="" action="{{url("admin/Availability/store")}}" method="post">
        {!! csrf_field() !!}
        <div class="tab-content bordered" id="myTabContent">
            <div class="tab-pane fade show active" id="preview01" role="tabpanel" aria-labelledby="preview01-tab">
                <div class="">
                    @include("layouts.admin.common.alerts")
                    <label class="control-label"><strong>Select Program Group</strong></label>
                    <div class="form-group">
                        <select class="form-control custom-select" id="slt_af_program">
                            <option value="">Select Application Filter</option>
                            @forelse($af_programs as $program)
                                <option value="{{$program}}">{{$program ?? ""}}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <label class="control-label"><strong>Select Program for Seats</strong></label>
                    <div class="form-group">
                        <select class="form-control custom-select selectProgram" id="programs" name="program_id">
                            <option value="">Select Program</option>
                            {{-- @forelse($programs as $p=>$program)
                            	<option value="{{$program->id }}">{{$program->name ?? ""}}</option>
                            @empty
                            @endforelse --}}
                        </select>
                    </div>
                    <div class="AjaxContent">
	                    
                    </div>
                    <div class="card shadow avg_seats d-none">
                        <div class="card-header">Current Racial Composition for {{$enrollment->school_year ?? (date("Y")-1)."-".date("Y")}}</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>    
	<!-- InstanceEndEditable --> 
</div>

@endsection
@section("scripts")
<script type="text/javascript">
    $(function()
    {
        generateContent();
        var lastSelected = $(document).find(".selectProgram option:selected");
    });
    $(document).on("click",".selectProgram",function(event)
    {
        lastSelected = $(document).find(".selectProgram option:selected");
    });
	$(document).on("change",".selectProgram",function(event)
	{
        event.preventDefault();
        let checkChanged = $(document).find(".changed").length;
        if(checkChanged == 0)
        {
            generateContent();
        }
        else
        {
            event.preventDefault();
            lastSelected.prop("selected",true);
            swal("Please save current changes");
        }
	});
    function generateContent()
    {
        $(document).find(".AjaxContent").html('');
        let selected = $(document).find(".selectProgram").val();

        if (selected != '') {
            $.ajax(
            {
                url:"{{url('admin/Availability/getOptionsByProgram')}}"+"/"+selected,
                success:function(result)
                {
                    $(document).find(".AjaxContent").html(result);
                }
            });
            // console.log(selected);
            matchWithTotal();
        }
    };
    function matchWithTotal()
    {
        $(document).find(".whiteSeat").each(function()
        {
            var grade = $(this).attr("data-id");
            var whiteSeat = $(this).val();
            var blackSeat = $(document).find("input[name='grades["+grade+"][black_seats]']").val();
            var otherSeat = $(document).find("input[name='grades["+grade+"][other_seats]']").val();
            var notSpecifiedSeat = $(document).find("input[name='grades["+grade+"][not_specified_seats]']").val();

            var value = parseInt(whiteSeat || 0) + parseInt(blackSeat || 0) + parseInt(otherSeat || 0) + parseInt(notSpecifiedSeat || 0);
            var total = $(document).find(".totalSeat[data-id="+grade+"]").val();


            if(parseInt(total || 0) == 0){ 
                console.log(parseInt(total || 0));
                $(document).find(".blackSeat[data-id='"+grade+"']").attr('disabled','disabled');
                $(document).find(".whiteSeat[data-id='"+grade+"']").attr('disabled','disabled');
                $(document).find(".otherSeat[data-id='"+grade+"']").attr('disabled','disabled');
                $(document).find(".notSpecifiedSeat[data-id='"+grade+"']").attr('disabled','disabled');
            }
            /*else{
                $(document).find(".blackSeat[data-id='"+grade+"']").removeAttr('disabled');
                $(document).find(".whiteSeat[data-id='"+grade+"']").removeAttr('disabled');
                $(document).find(".otherSeat[data-id='"+grade+"']").removeAttr('disabled');
                $(document).find(".notSpecifiedSeat[data-id='"+grade+"']").removeAttr('disabled');
            }

            if(parseInt(value) > parseInt(total))
            {
                $(this).parent().parent().find("label").removeClass("d-none");
                $(this).addClass("notAllowed");
            }
            else
            {
                $(this).parent().parent().find("label").addClass("d-none");
                $(this).removeClass("notAllowed");
            }*/
        });
        // $(document).find(".notAllowed:first").focus();
    }
    $(document).on("change input",".whiteSeat, .blackSeat, .otherSeat, .notSpecifiedSeat, .totalSeat",function()
    {
        matchWithTotal();
        $(this).addClass("changed");
    });
    $(document).on("click","#optionSubmit",function(event)
    {
        let checkNotAllowed = $(document).find(".notAllowed").length;
        // alert(checkNotAllowed);
        if(checkNotAllowed > 0)
        {
            swal("Please review all errors");
            $(document).find(".notAllowed:first").focus();

            event.preventDefault();
            return false;
        }
            // event.preventDefault();
        $(document).find(".notAllowed:first").focus();
    });

    $(document).ready(function() {
        $('#slt_af_program').trigger('change');
    });
    // Get program on change application filter
    $('#slt_af_program').on('change', function() {

        var application_filter = $(this).val();
        var selected_program = $('#programs').val();
        $('#programs option:not(:first)').remove();

        // if (application_filter == '') {
        //     $('.avg_seats').addClass('d-none'); 
        // } else {
        //     $('.avg_seats').removeClass('d-none');
        // }

        $.ajax({
            url: "{{url('')}}/admin/Availability/get_programs",
            data: {
                'application_filter': application_filter
            },
            success: function(response) {
                var p_flag = true;
                var response = JSON.parse(response);
                $.each(response.data, function(key, program) {
                    if (selected_program == program.id) {
                        p_flag = false;
                        $('#programs').append(new Option(program.name, program.id, true, true));
                    } else {
                        $('#programs').append(new Option(program.name, program.id)); 
                    }
                });
                if (p_flag) {
                    // after application filter dropdown change
                    // check program dropdown for selected program
                    // or remove  availability content
                    $(document).find(".AjaxContent").html('');
                }

                if (application_filter == '') {
                    $('.avg_seats').addClass('d-none');
                } else {
                    var row_1 = '';
                    var row_2 = '';
                    
                    // $.each(response.avg_data.data, function(key, value) {
                    //     var value = parseInt(value);
                    //     var avg = 0;
                    //     if ($.isNumeric(value) && value>0) {
                    //         avg = ((value*100)/response.avg_data.total).toFixed(2);    
                    //     }
                        
                    //     row_1 = row_1 + '<th>'+key+'</th>';
                    //     row_2 = row_2 + '<td>'+avg+'% (Total '+value+')</td>';
                    // });
                    // $('.avg_seats').find('thead').html('<tr>'+row_1+'</tr>');
                    // $('.avg_seats').find('tbody').html('<tr>'+row_2+'</tr>');
                    // // show average data
                    // $('.avg_seats').removeClass('d-none');
                }
            }
        });
    });

    // float digits validation
    $(document).on('keypress', '.digit_float', function() {
        digitValidation(event, this, 'float');
    });
    // digits validation
    $(document).on('keypress', '.digit', function() {
        digitValidation(event, this);
    });
    function digitValidation(evt, elm, type='') {
        if (type=='float') {
            var float_valid = ($(elm).val().indexOf('.') != -1);
        } else {
            var float_valid = true;
        }
        if ((evt.which != 46 || float_valid) &&
           ((evt.which < 48 || evt.which > 57) &&
           (evt.which != 0 && evt.which != 8))) {
               evt.preventDefault();
        }
    }
</script>
@endsection