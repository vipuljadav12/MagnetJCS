@extends('layouts.admin.app')
@section('title')
	Missing Committee Score Report
@endsection
@section('content')
<style type="text/css">
    .alert1 {
    position: relative;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
        border-top-color: transparent;
        border-right-color: transparent;
        border-bottom-color: transparent;
        border-left-color: transparent;
    border-radius: 0.25rem;
}
#tbl_committee_score_filter {display: none}

</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Missing Committee Score Report</div></div>
    </div>
    <div class="card shadow">
        @include("Reports::display_report_options", ["selection"=>$selection, "enrollment"=>$enrollment, "enrollment_id"=>$enrollment_id])

    </div>

    <div class="">
        <div class="card shadow" id="response">
                            </div>
    </div>


@endsection
@section('scripts')
    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Process is started.<br>It will take approx 2 minutes to finish. </div></div>

    <script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
    <script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>

	<script type="text/javascript">

        loadSubmissionData();

        function loadSubmissionData()
        {
            $("#wrapperloading").show();
            var  url = "{{url('admin/Reports/missing/'.$enrollment_id.'/')}}/committee_score/response";

            
            $.ajax({
                type: 'get',
                dataType: 'JSON',
                url: url,
                success: function(response) {
                    $("#response").html(response.html);
                    $("#wrapperloading").hide();
                    var dtbl_submission_list = $("#tbl_committee_score").DataTable(
                        {
                            "aaSorting": [],
                            dom: 'Bfrtip',
                            buttons: [
                                { 
                                    extend: 'excel', 
                                    text: 'Export to Excel',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6, 8, 9, 10, 11]
                                    } 
                                }
                            ]
                        }


                    );
                    // Hide Columns
                    dtbl_submission_list.columns([2]).visible(false);

                    var select = $('<select class="form-control custom-select2 submission_filters col-md-8" id="filter_option"><option value="">Select Programs</option></select>')
                        .appendTo( $('#submission_filters') )
                        .on( 'change', function () {
                            if($(this).val() != '')
                            {
                                if($(this).val() == "Academy for Gifted and Talented (AGT)")
                                {
                                    dtbl_submission_list.search("Academy for Gifted and Talented",true,false)
                                            .draw();
                                }
                                else if($(this).val() == "New Century Technology High School (NCTHS)")
                                {
                                    dtbl_submission_list.search("New Century Technology High School",true,false)
                                            .draw();
                                }

                                else
                                {
                                    dtbl_submission_list.search($(this).val(),true,false)
                                            .draw();
                                }
                            }
                            else
                            {
                                dtbl_submission_list.search($(this).val(),true,false)
                                    .draw();
                            }
                        } );

                    var filterArr = new Array();
                     dtbl_submission_list.column( 8 ).data().unique().sort().each( function ( d, j ) {

                            if(d != '' && d != "NA" && jQuery.inArray(d, filterArr) == -1)
                            {
                                filterArr[filterArr.length] = d;
                            }
                        } );
                     dtbl_submission_list.column( 10 ).data().unique().sort().each( function ( d, j ) {
                            if(d != '' && d != "NA" && jQuery.inArray(d, filterArr) == -1)
                            {
                                filterArr[filterArr.length] = d;
                            }
                        } );

                     //alert(filterArr);

                        for(i=0; i < filterArr.length; i++)
                        {
                            select.append( '<option value="'+filterArr[i]+'">'+filterArr[i]+'</option>' )
                        }
                }
            });

        }


        function editRow(id)
        {
            $("#edit"+id).addClass("d-none");
            $("#save"+id).removeClass("d-none");
            $("#cancel"+id).removeClass("d-none");

            $("#row"+id).find("span.scorelabel").addClass('d-none');
            $("#row"+id).find("input.scoreinput").removeClass('d-none');

        }

        function hideEditRow(id)
        {
            $("#edit"+id).removeClass("d-none");
            $("#save"+id).addClass("d-none");
            $("#cancel"+id).addClass("d-none");

            $("#row"+id).find("span.scorelabel").removeClass('d-none');
            $("#row"+id).find("input.scoreinput").addClass('d-none');
        }

        function saveScore(id)
        {
            // alert('saved');return false;
            var data = {};
            var keyArr = new Array();
            var valid = true;
            var zeroInclude = false;
            $("#row"+id).find("input.scoreinput").each(function(e)
            {
                var value = $.trim($(this).val());
                if(value != "")
                {
                    if (value != "") {
                        if(parseInt(value) > 100 || parseInt(value) < 0)
                        {
                            alert("Value allowed between 0-100");
                            valid = false;
                        }
                        data[$(this).attr("id")] = $(this).val();
                        $(this).parent().find(".scorelabel").html($(this).val());
                        keyArr[keyArr.length] = $(this).attr("id");
                    } else {
                        zeroInclude = true;
                    }
                }

            })

            if (!$.isEmptyObject(data) && valid == true) { 
                // data['_token'] = "{{csrf_token()}}";
                $.ajax({
                    url : "{{url('/admin/Reports/missing/committee_score/save/')}}/"+id,
                    type: "POST",
                    data : {
                        '_token': "{{csrf_token()}}",
                        data: data
                    },
                    success: function(data)
                    {
                        $("#edit"+id).removeClass("d-none");
                        $("#save"+id).addClass("d-none");
                        $("#cancel"+id).addClass("d-none");

                        $("#row"+id).find("span.scorelabel").removeClass('d-none');
                        $("#row"+id).find("input.scoreinput").addClass('d-none');

                        alert("Committee Score updated successfully");
                        if(!zeroInclude)
                        {
                            $("#row"+id).remove();
                        }
                    }
                });
            }    
        }

	</script>

@endsection