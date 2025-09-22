
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
                        <label class="control-label pl-0">Audition Due Date</label>
                        <div class="col-12" style="padding: 5px;">
                           <input class="form-control datetimepicker" id="audition_due_date" name="audition_due_date" value="{{getEligibilityConfigDynamic($req['program_id'], $req['eligibility_id'], 'audition_due_date', $req['application_id']) }}" data-date-format="mm/dd/yyyy hh:ii">
                        </div>
                        
                </div>
            </div>


            <div class="row">
                <div class="form-group col-12">
                        <label class="control-label pl-0">Email Subject</label>
                        <div class="col-12" style="padding: 5px;">
                           <input class="form-control" name="email_subject" value="{{getEligibilityConfigDynamic($req['program_id'], $req['eligibility_id'], 'email_subject', $req['application_id']) }}">
                        </div>
                        
                </div>
            </div>

            <div class="row">
                <div class="form-group col-12">
                        <label class="control-label pl-0">Email</label>
                        <div class="col-12" style="padding: 5px;">
                           <textarea class="form-control" id="editor00" name="email">{!! getEligibilityConfigDynamic($req['program_id'], $req['eligibility_id'], 'email', $req['application_id']) !!}</textarea>
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
        $(document).find("#exampleModalLabel1").html("Audition");
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

        $("#audition_due_date").datetimepicker({
            numberOfMonths: 1,
            autoclose: true,
            dateFormat: 'mm/dd/yy hh:ii'
        });
    });

    
</script>