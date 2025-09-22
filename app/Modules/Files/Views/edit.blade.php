@extends('layouts.admin.app')

@section('title') Edit File @stop

@section('styles')
    <style type="text/css">
        .error{
            color: red;
        }
    </style>
@stop

@section('content')

<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5"> Edit Front Page Link </div>
        <div class="">
            <a href="{{url('admin/Files')}}" class="btn btn-sm btn-secondary" title="Go Back">Go Back</a>
        </div>
    </div>
</div>

<div class="form-list">
    @include("layouts.admin.common.alerts")
</div>

<form action="{{url('/admin/Files/update/'.$id)}}" method="post" id="file-edit" name="file-edit" enctype= "multipart/form-data">
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 align-self-center">Label : </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" value="{{$files['link_title'] ?? ''}}" name="link_title">
                </div>
                <div class="col-md-7"></div>
                <div class="row col-md-32 offset-1">
                    @if($errors->has('link_title'))
                        <span class="error">
                            <strong>{{ $errors->first('link_title') ?? ''}}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3 align-self-center">Link Type : </div>
                <div class="col-md-4">
                    <select class="form-control custom-select" name="link_type" id="link_type" onchange="showFileText()">
                        <option value="file" @if($link_type=="file") selected @endif>File Attachment</option>
                        <option value="text" @if($link_type=="text") selected @endif>Editable Text Box</option>
                        <option value="link" @if($link_type=="link") selected @endif>Hyperlink to new page</option>
                    </select>
                </div>
            </div>
            <div class="row mt-2" id="file" 
                @if(old('link_type'))
                    @if(old('link_type')!="file") 
                        style="display: none"
                    @endif
                @endif>
                <div class="col-md-3 align-self-center">File : </div>
                <div class="col-md-4">
                    <input name="link_filename" type="file" class="form-control form-control-file">
                </div>
                <div class="col-md-7 align-self-center">
                    @if($files['link_filename'] != "")
                        <a href="{{url('/resources/filebrowser/'.$district->district_slug.'/documents/'.$files['link_filename'])}}" target="_blank" class="" title="">{{$files['link_filename']}}</a>
                    @endisset
                </div>
                <div class="row col-md-12 offset-1">
                    @if($errors->has('link_filename'))
                        <span class="error">
                            <strong>{{ $errors->first('link_filename') ?? ''}}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="row mt-2" id="text" @if(old('link_type')!="text") style="display: none" @endif>
                <div class="col-md-3">Text : </div>
                {{-- <div class="editor-height-90"> --}}
                <div class="col-md-9">
                    <textarea class="form-control editor" name="popup_text" id="popup_text">{{ $files['popup_text'] ?? ''}}</textarea>
                    @if($errors->has('popup_text'))
                        <span class="error">
                            <strong>{{ $errors->first('popup_text') ?? ''}}</strong>
                        </span>
                    @endif
                </div>
                <div class="col-md-1"></div>
            </div>
            <div class="row mt-2" id="link" @if(old('link_type')!="link") style="display: none" @endif>
                <div class="col-md-3 align-self-center">URL : </div>
                <div class="col-md-4">
                    <input class="form-control" name="link_url" id="link_url" value="{{ $files['link_url'] ?? ''}}">
                </div>
                <div class="col-md-7"></div>
                <div class="row col-md-12 offset-1">
                    @if($errors->has('link_url'))
                        <span class="error">
                            <strong>{{ $errors->first('link_url') ?? ''}}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>  
    <div class="box content-header-floating" id="listFoot">
        <div class="row">
            <div class="col-lg-12 text-right hidden-xs float-right">
                <button type="submit" class="btn btn-warning btn-xs"  name="save" value="save_edit" title="Save"><i class="fa fa-save"></i> Save </button>
                <button type="submit" class="btn btn-success btn-xs" name="save_exit" value="save_edit" title="Save & Exit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                <a class="btn btn-danger btn-xs text-white" href="{{url('/admin/Files')}}" title="Cancel"><i class="far fa-cross"></i> Cancel</a>
            </div>
        </div>
    </div>
</form>
@stop

@section('scripts')
<script type="text/javascript" src="{{url('/')}}/resources/assets/admin/plugins/laravel-ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="{{url('/resources/assets/admin/plugins/laravel-ckeditor/adapters/jquery.js')}}"></script>
<script type="text/javascript">

    CKEDITOR.replace('popup_text');

    // Form validation
    $('#file-edit').validate({
        rules: {
            link_title: {
                required: true,
                remote: {
                    url: "{{url('admin/Files/unique_title')}}",
                    data: {
                        'id': "{{$id}}" 
                    }
                }
            },
            popup_text: {
                required: true
            },
            link_filename: {
                // required: true,
                extension: 'pdf,jpg,gif,xls,xlsx,ppt,pptx,docx'
            },
            link_url: {
                required: true
            }
        },
        messages: {
            link_title: {
                required: "Link Title is required.",
                remote: "This Link Title already present."
            },
            popup_text: {
                required: "Text is required."
            },
            link_filename: {
                required: "File is required.",
                extension: "The File must be a file of type: pdf,jpg,gif,docx,xls,xlsx,ppt."
            },
            link_url: {
                required: "Link is required."
            }
        },
        errorPlacement: function(error, element) {
            error.addClass('row col-md-12 offset-1');
            error.appendTo(element.parent().parent());
        }
    });

    // CKEDITOR form validation
    $('#file-edit').submit(function() {
        return validate_ckeditor_text();
    });
    CKEDITOR.instances.popup_text.on('change', function(){
        return validate_ckeditor_text();
    });
    function validate_ckeditor_text() {
        if($("#link_type").val() == "text") {
            CKEDITOR.instances['popup_text'].updateElement();
            var editorcontent = $('#popup_text').val().replace(/<[^>]*>/gi, '');
            if (editorcontent.length < 1) {
                var ckerror = '<label id="cktextarea" class="error row col-md-12 offset-1">Text is required.</label>';
                $('#text').append(ckerror);
                return false;
            }
        }
        $('#cktextarea').remove();
    }

    // select default option in dropdown on page load
    var old_link_type = "{{(old('link_type') ?? $link_type)}}";
    var select_option = "";
    if (old_link_type.length > 0) {
        select_option = old_link_type;
    }else{
        select_option = $('#link_type option:first').val();
    }
    $('#link_type').val(select_option).trigger('change');

    // Show/Hide fields
    function showFileText()
    {
        $("#text,#file,#link").css("display", "none");
        if($("#link_type").val() == "file")
        {
            $("#file").css("display", "");
        }else if($("#link_type").val() == "text")
        {
            $("#text").css("display", "");
        }else{
            $("#link").css("display", "");
        }
    }

</script>
  @stop

