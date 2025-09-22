@extends('layouts.admin.app')
@section('title')Edit Communication | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('styles')
    
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Edit Waitlist Screen Text</div>
    </div>
</div>
    @include("layouts.admin.common.alerts")
   
        <div class="tab-content bordered" id="myTab1Content">
            <div class="tab-pane fade show active" id="communication" role="tabpanel" aria-labelledby="communication-tab">
                <ul class="nav nav-tabs custom-nav-tabs" id="myTab2">
                                <li class="nav-item"><a class="nav-link active" id="accept-tab" data-toggle="tab" href="#accept-screen">Offer Screen</a></li>
                                <li class="nav-item"><a class="nav-link" id="confirmation-tab" data-toggle="tab" href="#confirmation-screen">Confirmation Screen</a></li>
                                <li class="nav-item"><a class="nav-link" id="waitlist-tab" data-toggle="tab" href="#waitlist-screen">Waitlist Screen</a></li>
                                <li class="nav-item"><a class="nav-link" id="declined-tab" data-toggle="tab" href="#declined-screen">Declined Screen</a></li>

                            </ul>
                            <div class="tab-content tab-validate  bordered" id="myTab2Content">
                                <div class="tab-pane fade show active" id="accept-screen">
                                     <form class="" action="{{url('admin/DistrictConfiguration/saveWaitlistEditText')}}" method="POST">
                                        {{csrf_field()}}
                                        <div class="form-group col-12">
                                            <label class="control-label">Offer Screen : </label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                     <textarea class="form-control" id="editor01" name="offer_accept_screen">
                                                        {!! ($offer_accept_screen ? $offer_accept_screen->value : '') !!}
                                                    </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 text-right"> <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button></div>
                                    </form>
                                </div>
                                


                                <div class="tab-pane fade" id="confirmation-screen">
                                     <form class="" action="{{url('admin/DistrictConfiguration/saveWaitlistEditText')}}" method="POST">
                                        {{csrf_field()}}
                                        <div class="form-group col-12">
                                            <label class="control-label">Offer Confirmation Screen : </label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                     <textarea class="form-control" id="editor03" name="offer_confirmation_screen">
                                                        {!! ($offer_confirmation_screen ? $offer_confirmation_screen->value : '') !!}
                                                    </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 text-right"> <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button></div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="waitlist-screen">
                                     <form class="" action="{{url('admin/DistrictConfiguration/saveWaitlistEditText')}}" method="POST">
                                        {{csrf_field()}}
                                        <div class="form-group col-12">
                                            <label class="control-label">Offer Waitlist Screen : </label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                     <textarea class="form-control" id="editor04" name="offer_waitlist_screen">
                                                        {!! ($offer_waitlist_screen ? $offer_waitlist_screen->value : '') !!}
                                                    </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 text-right"> <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button></div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="declined-screen">
                                     <form class="" action="{{url('admin/DistrictConfiguration/saveWaitlistEditText')}}" method="POST">
                                        {{csrf_field()}}
                                        <div class="form-group col-12">
                                            <label class="control-label">Offer Declined Screen : </label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                     <textarea class="form-control" id="editor05" name="offer_declined_screen">
                                                        {!! ($offer_declined_screen ? $offer_declined_screen->value : '') !!}
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


        /*CKEDITOR.replace('editor02', {
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
        });*/

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