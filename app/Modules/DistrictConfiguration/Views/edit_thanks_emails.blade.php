@extends('layouts.admin.app')
@section('title')Edit Communication | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('styles')
    
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Edit Final Confirmation Email</div>
    </div>
</div>
    @include("layouts.admin.common.alerts")
   
        <div class="tab-content bordered" id="myTab1Content">
            <div class="tab-pane fade show active" id="communication" role="tabpanel" aria-labelledby="communication-tab">
                <ul class="nav nav-tabs custom-nav-tabs" id="myTab2">
                                <li class="nav-item"><a class="nav-link active" id="accept-tab" data-toggle="tab" href="#accept-email">Offered and Accepted</a></li>
                                <li class="nav-item"><a class="nav-link" id="declined-tab" data-toggle="tab" href="#declined-email">Offered and Declined</a></li>
                                <li class="nav-item"><a class="nav-link" id="waitlisted-tab" data-toggle="tab" href="#waitlisted-email">Offered and Waitlist for Other</a></li>
                                <li class="nav-item d-none"><a class="nav-link" id="contract-signed-tab" data-toggle="tab" href="#contract-signed-email">Contract Signed</a></li>
                            </ul>
                            <div class="tab-content tab-validate  bordered" id="myTab2Content">
                                <div class="tab-pane fade show active" id="accept-email">
                                     <form class="" action="{{url('admin/DistrictConfiguration/saveEditEmail')}}" method="POST" onsubmit="return ValidateForm('offer_accepted')">
                                        {{csrf_field()}}
                                        <div class="form-group col-12">
	                                        <label style="width: 100%">Mail Subject : <a href="{{url('/admin/DistrictConfiguration/preview/thanks/email/offer_accepted')}}" target="_blank" class="btn btn-success" style="float: right !important;">Preview</a></label>
	                                        <div class="">
	                                            <input type="text" class="form-control" name="offer_accepted_mail_subject" id="offer_accepted_mail_subject" value="{{$offer_accepted_mail_subject->value ?? ''}}">
	                                        </div>
	                                    </div>
                                        <div class="form-group col-12">
                                            <label class="control-label">Mail Body : </label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                     <textarea class="form-control" id="editor01" name="offer_accepted_mail_body" id="offer_accepted_mail_body">
                                                        {!! $offer_accepted_mail_body->value ?? '' !!}
                                                    </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 text-right"> <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button></div>
                                    </form>
                                </div>
                                
                                <div class="tab-pane fade" id="declined-email">
                                     <form class="" action="{{url('admin/DistrictConfiguration/saveEditEmail')}}" method="POST" onsubmit="return ValidateForm('offer_declined')">
                                        {{csrf_field()}}
                                        <div class="form-group col-12">
	                                        <label style="width: 100%">Mail Subject : <a href="{{url('/admin/DistrictConfiguration/preview/thanks/email/offer_declined')}}" target="_blank" class="btn btn-success" style="float: right !important;">Preview</a></label>
	                                        <div class="">
	                                            <input type="text" class="form-control" name="offer_declined_mail_subject" id="offer_declined_mail_subject" value="{{$offer_declined_mail_subject->value ?? ''}}">
	                                        </div>
	                                    </div>
                                        <div class="form-group col-12">
                                            <label class="control-label">Mail Body : </label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                     <textarea class="form-control" id="editor02" name="offer_declined_mail_body" id="offer_declined_mail_body">
                                                        {!! $offer_declined_mail_body->value ?? '' !!}
                                                    </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 text-right"> <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button></div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="waitlisted-email">
                                      <form class="" action="{{url('admin/DistrictConfiguration/saveEditEmail')}}" method="POST" onsubmit="return ValidateForm('offer_waitlisted')">
                                        {{csrf_field()}}
                                        <div class="form-group col-12">
	                                        <label style="width: 100%">Mail Subject : <a href="{{url('/admin/DistrictConfiguration/preview/thanks/email/offer_waitlisted')}}" target="_blank" class="btn btn-success" style="float: right !important;">Preview</a></label>
	                                        <div class="">
	                                            <input type="text" class="form-control" name="offer_waitlisted_mail_subject" id="offer_waitlisted_mail_subject" value="{{$offer_waitlisted_mail_subject->value ?? ''}}">
	                                        </div>
	                                    </div>
                                        <div class="form-group col-12">
                                            <label class="control-label">Mail Body : </label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                     <textarea class="form-control" id="editor03" name="offer_waitlisted_mail_body" id="offer_waitlisted_mail_body">
                                                        {!! $offer_waitlisted_mail_body->value ?? '' !!}
                                                    </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 text-right"> <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button></div>
                                    </form>
                                </div>

                                <div class="tab-pane fade  d-none" id="contract-signed-email">
                                      <form class="" action="{{url('admin/DistrictConfiguration/saveEditEmail')}}" method="POST" onsubmit="return ValidateForm('contract_signed')">
                                        {{csrf_field()}}
                                        <div class="form-group col-12">
                                            <label style="width: 100%">Mail Subject : <a href="{{url('/admin/DistrictConfiguration/preview/thanks/email/contract_signed')}}" target="_blank" class="btn btn-success" style="float: right !important;">Preview</a></label>
                                            <div class="">
                                                <input type="text" class="form-control" name="contract_signed_mail_subject" id="contract_signed_mail_subject" value="{{$contract_signed_mail_subject->value ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="form-group col-12">
                                            <label class="control-label">Mail Body : </label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                     <textarea class="form-control" id="editor04" name="contract_signed_mail_body" id="contract_signed_mail_body">
                                                        {!! $contract_signed_mail_body->value ?? '' !!}
                                                    </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 text-right"> <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button></div>
                                    </form>
                                </div>

            </div>
        </div>
     </div>
     
@endsection
@section('scripts')
<script type="text/javascript" src="{{url('/')}}/resources/assets/admin/plugins/laravel-ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="{{url('/resources/assets/admin/plugins/laravel-ckeditor/adapters/jquery.js')}}"></script>
<script type="text/javascript" src="{{url('/')}}/resources/assets/admin/js/additional-methods.min.js"></script>
<script type="text/javascript">
	function ValidateForm(str)
	{
		if($("#"+str+"_mail_subject").val() == "")
		{
			alert("Please enter email subject");
			return false;
		}
		if($("#"+str+"_mail_body").val() == "")
		{
			alert("Please enter email body");
			return false;
		}

	}
    CKEDITOR.replace('editor01', {
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

        CKEDITOR.replace('editor03', {
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

        CKEDITOR.replace('editor04', {
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

        CKEDITOR.replace('editor05', {
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


</script>
@endsection