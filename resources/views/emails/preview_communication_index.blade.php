<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<style type="text/css">
	table, td, th, tr {border: none !important}
</style>
	<script type="text/javascript" src="{{url('/resources/assets/admin/js/jquery/jquery-3.4.1.min.js')}}"></script> 
	<div class="container">    
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
            <div class="panel panel-info" >
                    <div class="panel-heading">
                        <div class="panel-title">Send Test Email</div>
                    </div>     

                    <div style="padding-top:30px" class="panel-body" >

                        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
                            
                        <form id="loginform" class="form-horizontal" role="form">
                                    
                            <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <input type="text" class="form-control" name="email" id="email" value="" placeholder="email address">                                        
                                    </div>
                                
                          
                                    

                                
                          


                                <div style="margin-top:10px" class="form-group">
                                    <!-- Button -->

                                    <div class="col-sm-12 controls">
                                      <a id="btn-login" href="javascript:void(0)" onclick="sendEmail();" class="btn btn-success">Send</a>
                                    </div>
                                </div>


                             
                            </form>     



                        </div>                     
                    </div>  
        </div>

@extends('emails.maillayout')
@section('content')
    {!! $data['email_text'] !!}
@endsection

<script type="text/javascript">
        
	
	function sendEmail()
	{
        @if($type == "regular")
            var url = "{{url('/admin/EditCommunication/Send/Test/Mail')}}";
        @elseif($type == "waitlist")
            var url = "{{url('/admin/Waitlist/EditCommunication/Send/Test/Mail')}}";
        @elseif($type == "late_submission")
            var url = "{{url('/admin/LateSubmission/EditCommunication/Send/Test/Mail')}}";
        @endif
		$.ajax({
            url: url,
            type:"post",
            data: {"_token": "{{csrf_token()}}", "email": $("#email").val(), "status": "{{$status}}", "type": "{{$type}}", "application_id": "{{$application_id}}"},
            success:function(response){
                alert("Test email sent successfully.");
            }
        })
	}
</script>