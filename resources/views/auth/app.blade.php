<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="{{url('/')}}/resources/assets/admin/images/favicon2.png" type="image/x-icon" />
<title>@yield("title")</title>
<link rel="stylesheet" href="{{url('/')}}/resources/assets/admin/css/main.css" type="text/css" />
<link href="{{url('/')}}/resources/assets/admin/css/animate.css" rel="stylesheet" type="text/css" />
<style>
    .pre-icon {color: #1f80c1;}
    body[data-bg="theme01"] .btn-secondary:not(:disabled):not(.disabled):active, 
    body[data-bg="theme01"] .btn-secondary:not(:disabled):not(.disabled).active, 
    body[data-bg="theme01"] .show > .btn-secondary.dropdown-toggle, 
    body[data-bg="theme01"] .btn-secondary {background: #1f80c1;}
    .help-block{color:  red;}
</style>    
    
</head>

<body data-bg="theme01">
<div id="page-container" class="login-option min-vh-100 vw-100 d-flex align-items-center justify-content-center"  style="background: url({{url('/')}}/resources/assets/admin/images/login_bg.png) no-repeat center center; background-size: cover;   "> 
    <!-- START Page Content-->
    <main id="main-container" class="w-100 pt-0">
        <section class="login-sign-up-wrapper">
            <div class="container">
                @yield("content")               
            </div>
        </section>
    </main>
    <!-- END Page Content--> 
</div>
<script src="{{url('/')}}/resources/assets/admin/js/jquery/jquery-3.4.1.min.js"></script> 
<script src="{{url('/')}}/resources/assets/admin/js/bootstrap/bootstrap.min.js"></script> 
<script type="text/javascript">
</script>
</body>
</html>