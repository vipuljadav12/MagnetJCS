<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="{{url('/resources/assets/front/images/favicon2.png')}}" type="image/x-icon" />
<title>Magnet JeffersonCo - Magnet School</title>
<link rel="stylesheet" href="{{url('/')}}/resources/assets/front/css/main.css?{{rand()}}">
<link rel="stylesheet" href="{{url('/')}}/resources/assets/front/css/custom.css?{{rand()}}">
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
</style>
<style type="text/css">
.hidden {
    display: none;
}
</style>
<script type="text/javascript" src="{{url('/')}}/resources/assets/admin/js/jquery/jquery-3.4.1.min.js"></script>
</head>
<body>
@if(!Session::has("contract_from_admin"))
<header>
    <div class="container">
        @php $logo = (isset($application_data) ? getDistrictLogo($application_data->display_logo) : getDistrictLogo()) @endphp
        <div class="p-10 bg-secondary text-center d-flex justify-content-between align-items-center" style="background: #ffffff !important"><a href="javascript:void(0);" class="d-inline-block w-m-150 w-200" title=""><img src="{{url('/resources/assets/admin/images/login.png')}}" title="" alt="" class="img-fluid"></a><a href="javascript:void(0);" class="d-inline-block w-m-100" title=""><img src="{{$logo}}" title="" alt="" style="max-height: 100px;"></a>
        </div>
    </div>
</header>
@endif
@yield('formstart')

<main>
  @yield('content')
</main>
@yield('footer')
@yield('formend')
<div class="modal fade" id="option1" tabindex="-1" role="dialog" aria-labelledby="option1Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded">
            <div class="modal-body p-0">
                <div class="alert alert-danger m-0">This is a danger alert--Check it out!</div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="option2" tabindex="-1" role="dialog" aria-labelledby="option2Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded">
            <div class="modal-body p-0">
                <div class="alert alert-warning m-0">This is a warning alert--Check it out!</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>