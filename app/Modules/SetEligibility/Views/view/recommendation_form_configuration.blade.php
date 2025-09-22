
<div class="card shadow">
    <form id="extraValueForm1" action="{{url('admin/SetEligibility/configurations/save')}}" method="post">
        {{csrf_field()}}
        <input type="hidden" name="program_id" value="{{$req['program_id']}}">
        <input type="hidden" name="eligibility_id" value="{{$req['eligibility_id']}}">
        <input type="hidden" name="eligibility_type" value="{{$req['eligibility_type']}}">
        <input type="hidden" name="application_id" value="{{$req['application_id']}}">

        @php
            $late_submission = 0;
            if (isset($req['late_submission'])) {
                $late_submission = $req['late_submission'];
            }
        @endphp
        <input type="hidden" name="late_submission" value="{{$late_submission}}">
        <div class="card-body">

            <div class="row">
                <div class="form-group col-12">
                        <label class="control-label pl-0">Form Title</label>
                        <div class="col-12" style="padding: 5px;">
                           <input class="form-control" name="form_title" value="{{getEligibilityConfigDynamic($req['program_id'], $req['eligibility_id'], 'form_title', $req['application_id']) }}">
                        </div>
                        
                </div>
            </div>

            <div class="row">
                <div class="form-group col-12">
                        <label class="control-label pl-0">Header Text</label>
                        <div class="col-12" style="padding: 5px;">
                           <textarea class="form-control" id="editor00" name="header_text">{!! getEligibilityConfigDynamic($req['program_id'], $req['eligibility_id'], 'header_text', $req['application_id']) !!}</textarea>
                        </div>
                        
                </div>
            </div>

            <div class="row">
                <div class="form-group col-12">
                        <label class="control-label pl-0">Form Footer Text</label>
                        <div class="col-12" style="padding: 5px;">
                           <textarea class="form-control" id="editor01" name="footer_text">{!! getEligibilityConfigDynamic($req['program_id'], $req['eligibility_id'], 'footer_text', $req['application_id']) !!}</textarea>
                        </div>
                        
                </div>
            </div>

            <div class="row">
                <div class="form-group col-12">
                        <label class="control-label pl-0">General Instructions</label>
                        <div class="col-12" style="padding: 5px;">
                           <textarea class="form-control" id="editor02" name="instructions">{!! getEligibilityConfigDynamic($req['program_id'], $req['eligibility_id'], 'instructions', $req['application_id']) !!}</textarea>
                        </div>
                        
                </div>
            </div>

           <div class="row">
                <div class="form-group col-12">
                        <label class="control-label pl-0">Subject Teacher Link Text</label>
                        <div class="col-12" style="padding: 5px;">
                           <textarea class="form-control" id="editor03" name="teacher_link_text">{!! getEligibilityConfigDynamic($req['program_id'], $req['eligibility_id'], 'teacher_link_text', $req['application_id']) !!}</textarea>
                        </div>
                        
                </div>
            </div>

             <div class="row">
                <div class="form-group col-12">
                        <label class="control-label pl-0">Manual Recommendation Form Email Subject</label>
                        <div class="col-12" style="padding: 5px;">
                           <input class="form-control" name="manual_email_subject" value="{{ getEligibilityConfigDynamic($req['program_id'], $req['eligibility_id'], 'manual_email_subject', $req['application_id']) }}">
                        </div>
                        
                </div>
            </div>

             <div class="row">
                <div class="form-group col-12">
                        <label class="control-label pl-0">Manual Recommendation Form Email Text</label>
                        <div class="col-12" style="padding: 5px;">
                           <textarea class="form-control" id="editor04" name="manual_email_text">{!! getEligibilityConfigDynamic($req['program_id'], $req['eligibility_id'], 'manual_email_text', $req['application_id']) !!}</textarea>
                        </div>
                        
                </div>
            </div>


            <div class="row">
                <div class="form-group col-12 text-right">
                    <button type="submit" id="extraValueFormBtn2" {{-- form="extraValueForm" --}} class="btn btn-success extraValueFormBtn">Save</button>
                </div>
            </div>

        </div>

    </form>
</div>

    <script type="text/javascript" src="{{url('/')}}/resources/assets/admin/plugins/laravel-ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="{{url('/resources/assets/admin/plugins/laravel-ckeditor/adapters/jquery.js')}}"></script>


<script type="text/javascript">
    $(document).ready(function(){
        $(document).find("#exampleModalLabel1").html("Writing Prompt");
        CKEDITOR.timestamp = new Date(); 
        /*for(name in CKEDITOR.instances)
        {
            CKEDITOR.instances[name].destroy(true);
            $("#"+name).css("visibility", "visible");
        }*/

        CKEDITOR.replace('editor00',{
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

        CKEDITOR.replace('editor01',{
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


        CKEDITOR.replace('editor02',{
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


        CKEDITOR.replace('editor03',{
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

        CKEDITOR.replace('editor04',{
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
    });

    
</script>