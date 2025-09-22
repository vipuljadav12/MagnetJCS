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
.p-20 {padding: 20px !important;}
@media (min-width: 767px) {
    .pt-lg-50  {padding-top : 50px !important;}
    .pb-lg-150  {padding-bottom : 150px !important;}
    .pl-lg-100  {padding-left : 100px !important;}
    .pr-lg-100 {padding-right : 100px !important;}
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
        <div class="p-10 bg-secondary text-center d-flex justify-content-between align-items-center" style="background: #ffffff !important"><a href="javascript:void(0);" class="d-inline-block w-m-150 w-200" title=""><img src="{{url('/resources/assets/admin/images/login.png')}}" title="" alt="" class="img-fluid"></a><a href="javascript:void(0);" class="d-inline-block w-m-100" title=""><img src="{{url('/resources/filebrowser/staging-magnet-jeffersonco/logo/the-jefferson-school_logo.png')}}" title="" alt="" style="max-height: 50px;"></a>
        </div>
    </div>
</header>
<main>
    <div class="container">
        <div class="mt-20">
            <div class="card aler alert-success p-20 pt-lg-50 pb-lg-150">
                @if($msg != '')
                    {!! $msg !!}
                @else
                    <div class="text-center font-20 b-600 mb-10">Grades Upload</div>
                    <div class="mb-10 text-center">Your application to the Magnet Program for {{$submission_data->first_name.' '.$submission_data->last_name}}, confirmation number {{$submission_data->submission_data}}, Grades Submitted Successfully.</div>
                @endif
                
            </div>
        </div>
    </div>
</main>

</body>
</html>