<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="{{url('/resources/assets/admin/images/favicon2.png')}}" type="image/x-icon" />
<title>Jefferson Co. County Schools</title>
<link rel="stylesheet" href="{{url('/resources/assets/admin/css/switchery.min.css')}}">
<link rel="stylesheet" href="{{url('/resources/assets/admin/plugins/sweet-alert2/sweetalert2.min.css')}}">
<link rel="stylesheet" href="{{url('/resources/assets/front/css/main.css?'.rand())}}">
<link rel="stylesheet" href="{{url('/resources/assets/front/css/custom.css?'.rand())}}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">


<style type="text/css">
.bg-secondary {
background-color:#ffffff !!important;
}
.top-links a {
    margin: 12px !important;
}
</style>
<style type="text/css">
label.error {
    color: red;
}
.d-none {
    display: none !important;
}
.close {
    display: none;
}
.d-custom-hide {
    display: none !important;
}
.b-600.w-110 {
    width: 50% !important
}
.w-200 {max-width: 200px;}
img {max-width: 100%;}    
@media (max-width: 767px) {
    .w-m-150 {width: 150px;}
    .w-m-100 {width: 100px;}
}
input[type="file"] {
    display: none;
}
.cls_grade_file {
    /*background-color: #00346b;
    color: #ffffff;*/
    border: 1px solid #ccc;
    display: inline-block;
    padding: 6px 12px;
    cursor: pointer;
}
.cls_cdi_file {
   /* background-color: #00346b;
    color: #ffffff;*/
    border: 1px solid #ccc;
    display: inline-block;
    padding: 6px 12px;
    cursor: pointer;
}

</style>
<style type="text/css">
.hidden {
    display: none;
}
</style>
</head>
<body>
<header>
    <div class="container">
        <div class="p-10 bg-secondary text-center d-flex justify-content-between align-items-center" style="background: #ffffff !important"><a href="javascript:void(0);" class="d-inline-block w-m-150 w-200" title=""><img src="{{url('/resources/assets/admin/images/login.png')}}" title="" alt="" class="img-fluid"></a><a href="javascript:void(0);" class="d-inline-block w-m-100" title=""><img src="{{url('/resources/filebrowser/magnet-jeffersonco/logo/the-jefferson-school_logo.png')}}" title="" alt="" style="max-height: 50px;"></a>
        </div>
    </div>
</header>
<main>
    <div class="container">
        <div class="mt-20">
            <div class="card bg-light p-20">
                @if($data->grade_cdi_welcome_text != '')
                    {!! $data->grade_cdi_welcome_text !!}
                @else
                    <div class="text-center font-20 b-600 mb-10">Grades Upload</div>
                    <div class="">
                        <div class="mb-10 text-center">
                            <div>Please upload Grades in PDF format. </div>
                            <div>PDF File up to 5 MB for Each File.</div>
                        </div>
                    </div>
                @endif
                <div class="">
                    
                    <form action="{{url('upload/grade/uploadFiles')}}" method="post" enctype='multipart/form-data' id='frm_gradecdi'>
                          {{csrf_field()}}
                          <input type="hidden" name="application_id" value="{{$data->application_id}}">
                    <div class="row justify-content-center">
                        <div class="col-12 col-lg-3">
                            <div class="form-group">
                                
                                <label for="">Last Four of Application Confirmation Number : </label>
                                <input type="text" class="form-control" name='submission_id' id='submission_id'>
                                <div><small><em style="font-weight: 600; font-size: 12px;">If your student application number e.g. MAGNET-2122-9999 then please enter last 4 digit e.g. 9999</em></small></div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3">
                            <div class="form-group">
                                <label for=""><br>Student Date of Birth : </label>
                                <input type="text" class="form-control" name='dobdatepicker' id='dobdatepicker' autocomplete="off" placeholder="MM/DD/YYYY">
                            </div>
                        </div>
                        <div class="col-12 col-lg-auto">
                            <div class="form-group"><label for="">&nbsp;</label>
                                <div><br><a href="javascript:void(0);" title="" class="btn btn-secondary btn-search ml-5 mr-5">Search</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive search-result" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered show_result">
                                <thead>
                                    <tr>
                                        <th class="">Last Four of Application Confirmation Number</th>
                                        <th class="">Student DOB</th>
                                        <th class="">Student Name</th>
                                        <th class="text-center w-200">Grades Report</th>
                                        {{-- <th class="text-center w-200">Conduct / Disciplinary Report</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- <tr>
                                        <td class="">1123</td>
                                        <td class="">24/02/2012</td>
                                        <td class="">Nancy Brown</td>
                                        <td class="">
                                            <div class="text-center">
                                                <div class="text-center"><a href="javascript:void(0);" title="" class="btn btn-secondary btn_upoad">Upload</a></div>
                                                <div class="uploaded-files" style="display: none;">
                                                    <div class=""><a target="_blank" href="download/test.pdf" title="" class="" style="color: #0000FF; text-decoration: underline;">Grades-1123-1</a></div>
                                                    <div class=""><a target="_blank" href="download/test.pdf" title="" class="" style="color: #0000FF; text-decoration: underline;">Grades-1123-2</a></div>
                                                    <div class=""><a target="_blank" href="download/test.pdf" title="" class="" style="color: #0000FF; text-decoration: underline;">Grades-1123-3</a></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="">
                                            <div class="text-center">
                                                <div class="text-center">
                                                    <a href="javascript:void(0);" title="" class=" btn_upoad"><i class="fa fa-plus" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="javascript:void(0);" title="" class="btn btn-secondary btn_upoad">-</a>
                                                </div>
                                                <div class="uploaded-files" style="display: none;">
                                                    <div class=""><a target="_blank" href="download/test.pdf" title="" class="" style="color: #0000FF; text-decoration: underline;">CDI-1123-1</a></div>
                                                    <div class=""><a target="_blank" href="download/test.pdf" title="" class="" style="color: #0000FF; text-decoration: underline;">CDI-1123-2</a></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr colspan="3">
                                        <td class=""></td>
                                        <td class=""></td>
                                        <td class=""></td>
                                        <td class="">
                                            <div class="text-center">
                                                <div class="text-center"><a href="javascript:void(0);" title="" class="btn btn-secondary btn_upoad">Upload</a></div>
                                                <div class="uploaded-files" style="display: none;">
                                                    <div class=""><a target="_blank" href="download/test.pdf" title="" class="" style="color: #0000FF; text-decoration: underline;">Grades-1123-1</a></div>
                                                    <div class=""><a target="_blank" href="download/test.pdf" title="" class="" style="color: #0000FF; text-decoration: underline;">Grades-1123-2</a></div>
                                                    <div class=""><a target="_blank" href="download/test.pdf" title="" class="" style="color: #0000FF; text-decoration: underline;">Grades-1123-3</a></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="">
                                            <div class="text-center">
                                                <div class="text-center">
                                                    <a href="javascript:void(0);" title="" class=" btn_upoad"><i class="fa fa-plus" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="javascript:void(0);" title="" class="btn btn-secondary btn_upoad">-</a>
                                                </div>
                                                <div class="uploaded-files" style="display: none;">
                                                    <div class=""><a target="_blank" href="download/test.pdf" title="" class="" style="color: #0000FF; text-decoration: underline;">CDI-1123-1</a></div>
                                                    <div class=""><a target="_blank" href="download/test.pdf" title="" class="" style="color: #0000FF; text-decoration: underline;">CDI-1123-2</a></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr> --}}
                                </tbody>
                            </table>
                        </div>
                        <div class="text-right">
                            <button type='submit' id='submit_btn' class="btn btn-success">Submit</button>
                        </div>
                        </form>
                       {{--  <div class="text-right"><a href="javascript:void(0);" title="" class="btn btn-success" data-toggle="modal" data-target="#submit_alert">Submit</a></div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<div class="modal fade" id="submit_alert" tabindex="-1" role="dialog" aria-labelledby="submit_alertLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="submit_alertLabel">Alert</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                <div class="">Are you sure you have uploaded all necessary documents ?</div>
            </div>
            <div class="modal-footer">
                <a href="accept-contract.html" title="" class="btn btn-success">Yes</a>
                <button type="button" class="btn btn-danger">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="option1" tabindex="-1" role="dialog" aria-labelledby="option1Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded">
            <div class="modal-body p-0">
                <div class="alert alert-danger m-0">This is a danger alert--Check it out!</div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="option2" tabindex="-1" role="dialog" aria-labelledby="option2Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded">
            <div class="modal-body p-0">
                <div class="alert alert-warning m-0">This is a warning alert--Check it out!</div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script> 
<script type="text/javascript" src="{{url('/resources/assets/admin/js/bootstrap/bootstrap.min.js')}}"></script> 
<script type="text/javascript" src="{{url('/resources/assets/admin/js/switchery.min.js')}}"></script> 
<script type="text/javascript" src="{{url('/resources/assets/admin/js/jquery/jquery-3.4.1.min.js')}}"></script>


<script
  src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
  integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
  crossorigin="anonymous"></script>


<script src="{{url('/resources/assets/admin/js/jquery.validate.min.js')}}"></script> 
<script src="{{url('/resources/assets/admin/js/additional-methods.min.js')}}"></script> 
<script src="{{url('/resources/assets/front/js/jquery/jquery_input_mask.js')}}"></script> 
<script src="{{url('/resources/assets/admin/plugins/sweet-alert2/sweetalert2.min.js')}}"></script> 
<script type="text/javascript">

$(document).ready(function() 
    {

        $('#submit_btn').attr('disabled',true);
        $('#dobdatepicker').datepicker({
            dateFormat:"yy-mm-dd",
            changeMonth:true,
            changeYear:true,
            dateFormat: 'mm/dd/yy',
            yearRange: '2000:{{date("Y")-4}}'

        });
    });
$(document).on("click",".btn-search",function(){
    //$(".search-result").show();
    //alert("fddffffsdfdsff");
    if($('#submission_id').val() == "")
    {
        swal({
                text: "Please Enter Last Four of Application Confirmation Number",
                type: "warning",
                confirmButtonText: "OK",
                confirmButtonColor: "#d62516"
            });
        return false;
    }
    if($('#dobdatepicker').val() == "")
    {
        swal({
                text: "Please Enter Student Date of Birth",
                type: "warning",
                confirmButtonText: "OK",
                confirmButtonColor: "#d62516"
            });
        return false;
    }

    var submission_id = $('#submission_id').val();
    var dob = $('#dobdatepicker').val();

    $.ajax({
        url: "{{url('upload/grade/checkSubmissionId')}}",
        type: "post",
        //dataType: "JSON",
        data:{"_token":"{{csrf_token()}}",submission_id:submission_id, dob:dob},
        success:function(response){
            if(jQuery.type(response) == "string")
            {
                if($.trim(response) == "SID"){
                    $(".search-result").hide();
                      swal({
                        text: "Student already has SID",
                        type: "warning",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#d62516"
                    });

                }
                else
                {
                    $(".search-result").hide();
                          swal({
                        text: "Student Submission ID and DOB Combination is not Matching",
                        type: "warning",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#d62516"
                    });
                }
            }
            else{
                var show_data='';
                show_data='<tr><td>'+response[0].id+'</td><td>'+$('#dobdatepicker').val()+'</td><td>'+response[0].first_name+' '+response[0].last_name+'</td><td><div class="text-center main-tr"><label for="grade_file_1" class="cls_grade_file btn btn-secondary">Upload</label><i class="fa fa-plus ml-20 grade_upload_plus" aria-hidden="true"></i><input type=file id=grade_file_1 name=grade_file[] class="grade_file"><div class="append_file_name"></div></div></td></tr>';
                $('.show_result tbody').html(show_data);
                $(".search-result").show();
            }
        }

    })
})  


$(document).ready(function(){
    $(document).on("change",".grade_file",function(){
        // $(this).parent().find('.append_file_name').text("");
        

        $(this).closest('tr').find('.append_file_name').text("");

        var names = [];
        var chk_flag = 0;
        for (var i = 0; i < $(this).get(0).files.length; ++i) {
               if ($(this).get(0).files[i].type != 'application/pdf') {
                swal({
                    text: "Only PDF format is allowed for Grades Upload",
                    type: "warning",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#d62516"
                });
                $('#submit_btn').attr('disabled',true);
                chk_flag=1;
            }else if($(this).get(0).files[i].size>5120){
                /* swal({
                    text: "Greater than 5 MB is not allowed.",
                    type: "warning",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#d62516"
                });
                 $('#submit_btn').attr('disabled',true);
                 chk_flag=1;*/
            }
            
        }

        if(chk_flag!=1){
                $('#submit_btn').attr('disabled',false);
                 for (var i = 0; i < $(this).get(0).files.length; ++i) {
                    names.push("<BR>"+$(this).get(0).files[i].name);
                }
                // $(this).parent().find('.append_file_name').html(names);
                $(this).closest('tr').find('.append_file_name').html(names);
        }
        //var getfilename = $('input[type=file]').val().split('\\').pop();
        //$('.append_file_name').append(getfilename);
    });


    $(document).on('click','.grade_upload_plus',function(){

        var count = $(document).find('.cls_grade_file').length + 1;

        console.log(count);

        var html = '<tr><td colspan="3"></td><td><div class="text-center"><label for="grade_file_'+count+'" class="cls_grade_file btn btn-secondary">Upload</label><i class="fa fa-plus ml-20 grade_upload_plus" aria-hidden="true"></i><i class="fa fa-minus ml-10 grade_upload_minus" aria-hidden="true"></i><input type="file" id="grade_file_'+count+'" name=grade_file[] class="grade_file"><div class="append_file_name"></div></div></td></tr>';
        var count = $(document).find('.grade_upload_plus').length;

        if(count < 5){
            $(this).closest('tr').after(html);
        }
    });

    $(document).on('click','.grade_upload_minus',function(){

        // if(!$(this).parent().hasClass('main-tr'))
        $(this).closest('tr').remove();
    });
});

$(document).on("change","#cdi_file",function(){
    $('.append_cdi_file_name').text("");
    var cdi_names = [];
    var cdi_chk_flag = 0;
    for (var i = 0; i < $(this).get(0).files.length; ++i) {
           if ($(this).get(0).files[i].type != 'application/pdf') {
            swal({
                text: "Only PDF format is allowed for Grades and CDI Upload",
                type: "warning",
                confirmButtonText: "OK",
                confirmButtonColor: "#d62516"
            });
            $('#submit_btn').attr('disabled',true);
            cdi_chk_flag=1;
        }else if($(this).get(0).files[i].size>5120){
            /* swal({
                text: "Greater than 5 MB is not allowed.",
                type: "warning",
                confirmButtonText: "OK",
                confirmButtonColor: "#d62516"
            });
             $('#submit_btn').attr('disabled',true);
             cdi_chk_flag=1;*/
        }
        
    }

    if(cdi_chk_flag!=1){
        $('#submit_btn').attr('disabled',false);
         for (var i = 0; i < $(this).get(0).files.length; ++i) {
            cdi_names.push("<BR>"+$(this).get(0).files[i].name);
        }
        $('.append_cdi_file_name').html(cdi_names);
    }
});

   

$(document).on("click",".btn_upoad",function(){
    $(this).parent("div").siblings(".uploaded-files").show();
});
</script>
</body>
</html>