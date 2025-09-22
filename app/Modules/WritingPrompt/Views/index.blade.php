@extends('layouts.admin.app')
@section('title')WritingPrompt | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('styles')
<style type="text/css">
    .error { color: red; }
</style>
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Writing Prompt</div>
    </div>
</div>
@include("layouts.admin.common.alerts")  
<div class="tab-pane active" id="new">
    <form class="" action="{{url('/admin/WritingPrompt/store')}}" method="post" id="frm_wp">
    {{ csrf_field() }}
        @php
            $fields_ary = ['duration', 'intro_txt', 'mail_subject', 'mail_body'];
            foreach ($fields_ary as $field) {
                ${$field} = '';
                if (old($field)) {
                    ${$field} = old($field);
                }
                else if (isset($data)) {
                    ${$field} = $data->{$field} ?? '';
                }
            }
        @endphp
        <div class="card shadow">
            <div class="card-body">
                <div class="form-group">
                    <label for="">Duration Time for Writing Prompt (in Minutes) : </label>
                    <div class="">
                        <input type="text" class="form-control" name="duration" maxlength="3" value="{{$duration}}">
                        @if($errors->first('duration'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('duration') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label for="">Introductory Text : </label>
                    <div class="">
                        <textarea class="form-control" name="intro_txt">{{$intro_txt}}</textarea>
                        @if($errors->first('intro_txt'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('intro_txt') }}
                            </div>
                        @endif
                    </div>
                </div>    
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <div class="tab-pane" id="active-email">
                    <div class="form-group">
                        <label for="">Mail Subject</label>
                        <div class="">
                            <input type="text" class="form-control" name="mail_subject" maxlength="255" value="{{$mail_subject}}">
                            @if($errors->first('mail_subject'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('mail_subject') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Mail Body : </label>
                        <div class="editor-height-210">
                            <textarea id="mail_body" class="form-control" name="mail_body">{{$mail_body}}</textarea>
                            @if($errors->first('mail_body'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('mail_body') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group d-none" id="emaillist">
                        
                    </div>
                    <div class="box content-header-floating" id="listFoot">
                        <div class="row">
                            <div class="col-lg-12 text-right hidden-xs float-right">
                                <button type="submit" class="btn btn-warning btn-xs" name="submit" value="Save"><i class="fa fa-save"></i> Save </button>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="form-group"> 
                        <input type="submit" class="btn btn-secondary" value="Save Changes"> 
                    </div> --}}
                </div>
            </div>
        </div>   
    </form>
</div>



     
@endsection
@section('scripts')
<script type="text/javascript" src="{{url('/')}}/resources/assets/admin/plugins/laravel-ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="{{url('/resources/assets/admin/plugins/laravel-ckeditor/adapters/jquery.js')}}"></script>
<script type="text/javascript" src="{{url('/')}}/resources/assets/admin/js/additional-methods.min.js"></script>
<script type="text/javascript">

    CKEDITOR.replace('mail_body',{
        toolbar : 'Basic',
        toolbarGroups: [
            { name: 'document',    groups: [ 'mode', 'document' ] },            // Displays document group with its two subgroups.
            { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },           // Group's name will be used to create voice label.
            { name: 'basicstyles', groups: [ 'cleanup', 'basicstyles'] },
            
            '/',                                                                // Line break - next group will be placed in new line.
            { name: 'links' }
        ],
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

    function validateTextarea(textarea) {
        CKEDITOR.instances[textarea.id].updateElement();
        var editorcontent = textarea.value.replace(/<[^>]*>/gi, '');
        return editorcontent.length === 0;
    }

    $('#frm_wp').validate({
        ignore: [],
        rules: {
            duration: {
                required: true,
                digits: true,
                maxlength: 3
            },
            intro_txt: {
                required: true
            },
            mail_subject: {
                required: true,
                maxlength: 255
            },
            mail_body: {
                required: function(textarea) {
                    return validateTextarea(textarea);
                }
            }
        },
        messages: {
            duration: {
                required: 'Duration is required.',
                maxlength: 'No more than 3 digits.'
            },
            intro_txt: {
                required: 'Introductory Text is required.'
            },
            mail_subject: {
                required: 'Mail Subject is required.'
            },
            mail_body: {
                required: 'Mail Body is required.'
            }
        },
        errorPlacement: function(error, element)
        {
            error.appendTo( element.parents('.form-group') );
        }
    });
       
</script>
@endsection