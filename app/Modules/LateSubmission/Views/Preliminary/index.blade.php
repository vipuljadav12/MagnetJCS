@extends('layouts.admin.app')
@section('title')Late Submission Preliminary Selection | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
<style type="text/css">
    .font-18{font-size: 18px !important;}
</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Composite Score Preliminary Processing</div>
        </div>
    </div>
    @if($prelim_count > 0)
        <div class="card shadow">
            <div class="card-body">
                <div class="col-12 text-center">
                    Preliminary Processing has been completed.
                </div>
            </div>
        </div>
    @else
     <div class="card shadow">
        <div class="card-body">
            <div class="col-12">
                <div class="form-group row">
                    <label for="" class="page-title control-label col-4 font-18">Total Submissions:  </label>
                    <label for="" class="page-title control-label col-4 font-18">{{$submissions}}</label>
                </div>

                <div class="form-group row">
                    <label for="" class="page-title control-label col-4 font-18">Submissions that have a Preliminary Score:  </label>
                    <label for="" class="page-title control-label col-4 font-18">{{$exist_count}}</label>
                </div>

                <div class="form-group row">
                    <label for="" class="page-title control-label col-4 font-18">Preliminary Score Cutoff Threshold:  </label>
                    <div class="col-4"><input type="text" name="thresold_val" id="thresold_val" class="form-control"></div>
                </div>

                <div class="form-group row">
                    <label for="" class="page-title control-label col-4 font-18">Submissions Meeting Threshold:  </label>
                    <div class="col-4"><input type="text" class="form-control" disabled id="passed_val"></div>
                </div>

                <div class="form-group row">
                    <label for="" class="page-title control-label col-4 font-18"> Submissions Below Threshold to be Denied:  </label>
                    <div class="col-4"><input type="text" class="form-control" disabled id="declined_val"></div>
                </div>

                <div class="form-group row">
                    <label for="" class="page-title control-label col-4 font-18"> Submissions Pending Scores:  </label>
                    <div class="col-4"><input type="text" class="form-control" disabled id="pending_val"></div>
                </div>

                <div class="form-group row" id="first_step_btn">
                    <div class="text-right col-12"><input type="submit" class="btn btn-danger" id="process_preliminary" value="View Submissions Below Cutoff"></div>
                </div>

            </div>

            <div class="col-12" id="response">
            </div>
            <div class="col-12 d-none pt-10" id="second_step_btn">
                <div class="form-group row">
                    <div class="text-right col-12"><input type="submit" class="btn btn-danger" id="commit_preliminary" value="Commit Preliminary Scores"></div>
                    <div class="text-right col-12 d-none"><input type="submit" class="btn btn-danger" id="rollback_preliminary" value="Rollback Preliminary Scores"></div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('scripts')
    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Process is started.<br>It will take approx 15 minutes to finish. </div></div>
    <script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
    <script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>

    <script type="text/javascript">
         $('#process_preliminary').click(function(event) {
            if($("#thresold_val").val() == '' || !$.isNumeric($("#thresold_val").val()))
            {
                alert("Please enter valid thresold value");
                $("#thresold_val").focus();
                return false;
            }
            $("#wrapperloading").show();
            $.ajax({
                url:'{{ url('/admin/LateSubmission/Preliminary/Processing/calculate')}}',
                type:"POST",
                data: {"_token": "{{csrf_token()}}", "thresold_val": $("#thresold_val").val()},
                success:function(response){
                    $("#wrapperloading").hide();
                    var data = JSON.parse(response);


                    $("#declined_val").val(data.fail);
                    $("#passed_val").val(data.pass);
                    $("#pending_val").val(data.pending);

                    $("#response").html(data.html);

                    var dtbl_submission_list = $("#datatable").DataTable({"aaSorting": [],
                             dom: 'Bfrtip',
                              fixedHeader: {
                            relativeScroll: true
                        },
                             bPaginate: true,
                             bSort: false,
                             buttons: [
                                    {
                                        extend: 'excelHtml5',
                                        title: 'Preliminary-Scores',
                                        text:'Export to Excel'
                                    }
                                ]
                            });

                    if((parseInt(data.fail) > 0 || parseInt(data.pass) > 0) && parseInt(data.pending) <= 0)
                    {
                        $("#second_step_btn").removeClass("d-none");
                        
                    }
                    else
                    {
                       $("#second_step_btn").addClass("d-none"); 
                    }

                    
                }
            })
         });

         $('#commit_preliminary').click(function(event) {
            $("#wrapperloading").show();
            $.ajax({
                url:'{{ url('/admin/LateSubmission/Preliminary/Processing/commit')}}',
                type:"POST",
                data: {"_token": "{{csrf_token()}}", "thresold_val": $("#thresold_val").val()},
                success:function(response){
                    $("#wrapperloading").hide();
                    alert("Score commited successfully.");
                    $("#commit_preliminary").addClass("d-none");
                    $("#thresold_val").attr("disabled", "disabled");

                    //$("#rollback_preliminary").removeClass("d-none");

                }
            })
         });

         $('#rollback_preliminary').click(function(event) {
            $("#wrapperloading").show();
            $.ajax({
                url:'{{ url('/admin/LateSubmission/Preliminary/Processing/rollback')}}',
                type:"POST",
                data: {"_token": "{{csrf_token()}}", "thresold_val": $("#thresold_val").val()},
                success:function(response){
                    $("#wrapperloading").hide();
                    alert("Score commited successfully.");
                    $("#commit_preliminary").addClass("d-none");
                    $("#thresold_val").removeAttr("disabled");
                    $("#response").html("");
                    $("#rollback_preliminary").addClass("d-none");
                    $("#process_preliminary").removeClass("d-none");

                }
            })
         });

    </script>
@endsection