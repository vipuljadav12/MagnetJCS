@extends('layouts.admin.app')
@section('title') District Configuration | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('styles')
<style type="text/css">
    .error {
        color: red;
    }
</style>
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">District Configuration</div>
    </div>
</div>

@include("layouts.admin.common.alerts")

    <div class="card shadow">
        <div class="card-body">

            <ul class="nav nav-tabs" id="myTab2" role="tablist">
                <li class="nav-item"><a class="nav-link active" id="district-config-tab" data-toggle="tab" href="#district-config" role="tab" aria-controls="district-config" aria-selected="true">District Configuration</a></li>
                <li class="nav-item"><a class="nav-link" id="student-api-tab" data-toggle="tab" href="#student-api-screen" role="tab" aria-controls="student-api-screen" aria-selected="true">Student API</a></li>
            </ul>
            <div class="tab-content bordered" id="myTab2Content">
                <div class="tab-pane fade show active" id="district-config" role="tabpanel" aria-labelledby="district-config-tab">
                    <div class="">
                        <form id="frm_index" action="{{url('admin/DistrictConfiguration/store')}}" method="post" enctype= "multipart/form-data">
                        {{csrf_field()}}
                            <div class="row">
                                <div class="form-group col-12">
                                    <label class="control-label">Letter Signature : </label>
                                    <div class="row">
                                        <div class="col-md-12">
                                             <textarea class="form-control" id="editor00" name="letter_signature">
                                                {!! ($old_letter_signature_value != '' ? $old_letter_signature_value : '') !!}
                                            </textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label class="control-label">Email Signature : </label>
                                    <div class="row">
                                        <div class="col-md-12">
                                             <textarea class="form-control" id="editor01" name="email_signature">
                                                {!! ($old_email_signature_value ? $old_email_signature_value : '') !!}
                                            </textarea>
                                        </div>
                                    </div>
                                </div>

                        <div class="box content-header-floating" id="listFoot">
                            <div class="row">
                                <div class="col-lg-12 text-right hidden-xs float-right">
                                    <button type="Submit" class="btn btn-warning btn-xs submit" title="Save"><i class="fa fa-save"></i> Save </button>
                                    <a class="btn btn-danger btn-xs" href="{{url('/admin/DistrictConfiguration')}}" title="Cancel"><i class="fa fa-times"></i> Cancel</a>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane fade" id="student-api-screen" role="tabpanel" aria-labelledby="student-api-screen-tab">
                    


                    <div class="">
                            <div class="row p-20">
                                <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small" placeholder="Enter Student State ID..." aria-label="Search" aria-describedby="basic-addon2" id="state_id">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button" onclick="fetchStudentData()">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                               

                            </div>

                    </div>

                    <div class="form-group d-none" id="studentinfo">
                                        
                                    </div>



                </div>
            </div>

            

        </div>
    </div>
        

</form>
@endsection
@section('scripts')
    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> API Successfully Started<br>It will take approx 1 minute to bring student record. </div></div>    

<script type="text/javascript" src="{{url('/')}}/resources/assets/admin/plugins/laravel-ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="{{url('/resources/assets/admin/plugins/laravel-ckeditor/adapters/jquery.js')}}"></script>

<script type="text/javascript"> 


   /* jQuery.validator.addMethod("imageDimension", function(value, element,options) {
        var myImg = document.querySelector("#email_signature_thumb");
        var realWidth = myImg.naturalWidth;
        var realHeight = myImg.naturalHeight;

        if(realWidth > 500 || realHeight > 500){
            return false;
        }else{
            return true;
        }
     }, "");


    $('input[name="signature"]').change(function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#signature_thumb')
                    .attr('src', e.target.result)
            };
            reader.readAsDataURL(this.files[0]);
        }
    });*/


   /* $('#frm_index').validate({
        rules: {
            letter_signature: {
                imageDimension: true,
                // required: true,
                extension: 'png,jpg,gif'
            },
            email_signature: {
                imageDimension: true,
                // required: true,
                extension: 'png,jpg,gif'
            }
        },
        messages: {
            letter_signature: {
                imageDimension: 'Maximum image dimensions are 500x500.',
                required: 'Signature Image File is required.',
                extension: 'Signature Image File is the file of type .png/.jpg/.gif'
            },
            email_signature: {
                imageDimension: 'Maximum image dimensions are 500x500.',
                required: 'Signature Image File is required.',
                extension: 'Signature Image File is the file of type .png/.jpg/.gif'
            }

        } 
    });*/
        CKEDITOR.replace('editor00',{
             filebrowserImageBrowseUrl: '{{url("/")}}/resources/assets/admin/plugins/laravel-ckeditor/imageBrowser.php?path={{url("/")}}',
            filebrowserBrowseUrl: '{{url("/")}}/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?type=Files',
            filebrowserUploadUrl: '{{url("/")}}/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?command=QuickUpload&type=Files',
            filebrowserWindowWidth: (screen.width/1.5),
            filebrowserWindowHeight: (screen.height/1.5),
        });
        CKEDITOR.replace('editor01', {
             filebrowserImageBrowseUrl: '{{url("/")}}/resources/assets/admin/plugins/laravel-ckeditor/imageBrowser.php?path={{url("/")}}',
            filebrowserBrowseUrl: '{{url("/")}}/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?type=Files',
            filebrowserUploadUrl: '{{url("/")}}/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?command=QuickUpload&type=Files',
            filebrowserWindowWidth: (screen.width/1.5),
            filebrowserWindowHeight: (screen.height/1.5),
        });
        CKEDITOR.replace('editor02', {
             filebrowserImageBrowseUrl: '{{url("/")}}/resources/assets/admin/plugins/laravel-ckeditor/imageBrowser.php?path={{url("/")}}',
            filebrowserBrowseUrl: '{{url("/")}}/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?type=Files',
            filebrowserUploadUrl: '{{url("/")}}/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?command=QuickUpload&type=Files',
            filebrowserWindowWidth: (screen.width/1.5),
            filebrowserWindowHeight: (screen.height/1.5),
            on: {
                pluginsLoaded: function() {
                    var editor = this,
                        config = editor.config;
                    
                    editor.ui.addRichCombo( 'my-combo', {
                        label: 'Insert Short Code',
                        title: 'Insert Short Code',
                        toolbar: 'basicstyles',
                
                        panel: {               
                            css: [ CKEDITOR.skin.getPath( 'editor' ) ].concat( config.contentsCss ),
                            multiSelect: false,
                            attributes: { 'aria-label': 'Insert Short Code' }
                        },
            
                        init: function() {   
                            var chk = []; 
                            $.ajax({
                                url:'{{url('/admin/shortCode/list')}}',
                                type:"get",
                                async: false,
                                success:function(response){
                                    chk = response;
                                }
                            }) 
                            for(var i=0;i<chk.length;i++){
                                this.add( chk[i], chk[i] );
                            }
                        },
            
                        onClick: function( value ) {
                            editor.focus();
                            editor.fire( 'saveSnapshot' );
                           
                            editor.insertHtml( value );
                        
                            editor.fire( 'saveSnapshot' );
                        }
                    } );        
                }        
            }
        });

        function fetchStudentData()
        {
            if($.trim($("#state_id").val()) == "")
            {
                alert("Please Enter State ID");
            }
            else
            {
                $("#wrapperloading").show();

                var url = "{{url('/INOW/API/fetch_student.php?id=')}}"+$("#state_id").val();
                $.ajax({
                url:url,
                method:'get',
                success:function(response){
                    if(response=="Student Application Already Subimtted")
                    {
                        alert(response);
                         $("#wrapperloading").hide();
                        return false;
                    }
                    else
                    {
                        $("#wrapperloading").hide();

                        var data = JSON.parse(response);
                        var html = '<table id="datatable" class="table table-striped mb-0">';
                        html += '<thead>';
                        html += '<tr>';
                        html += '<th class="align-middle w-120 text-center">Field</th>';
                        html += '<th class="align-middle w-120 text-center">Value</th>';
                        html += '</tr>';
                        html += '</thead>';

                        html += '<tr>';
                        html += '<td class="align-middle w-120 text-center">Student Name : </td><td>'+data['first_name']+' '+data['last_name']+'</td>';
                        html += '</tr>';
                        
                        html += '<tr>';
                        html += '<td class="align-middle w-120 text-center">Parent Name : </td><td>'+data['parent_first_name']+' '+data['parent_last_name']+'</td>';
                        html += '</tr>';
                        
                        html += '<tr>';
                        html += '<td class="align-middle w-120 text-center">Current Grade : </td><td>'+data['current_grade']+'</td>';
                        html += '</tr>';
                        
                        html += '<tr>';
                        html += '<td class="align-middle w-120 text-center">Current School : </td><td>'+data['current_school']+'</td>';
                        html += '</tr>';
                        
                        html += '<tr>';
                        html += '<td class="align-middle w-120 text-center">Address : </td><td>'+(data['address']+ ", "+data['city']+"-"+data['zip'])+'</td>';
                        html += '</tr>';
                        html += '</table>';

                        $("#studentinfo").removeClass("d-none");
                        $("#studentinfo").html(html);
                    }
                }
                });
            }
        }
</script> 
@endsection