<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="{{url('/resources/assets/admin')}}/images/favicon2.png" type="image/x-icon" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>@yield("title")</title>
<!-- InstanceEndEditable -->
<link rel="stylesheet" href="{{url('/resources/assets/admin')}}/css/metismenu.min.css">
<link rel="stylesheet" href="{{url('/resources/assets/admin')}}/plugins/DataTables/datatables.min.css">
<link rel="stylesheet" href="{{url('/resources/assets/admin')}}/plugins/sweet-alert2/sweetalert2.min.css">
<link rel="stylesheet" href="{{url('/resources/assets/admin')}}/css/main.css?{{rand()}}">
<link rel="stylesheet" href="{{url('/resources/assets/admin')}}/css/switchery.min.css">
<link rel="stylesheet" href="{{url('/resources/assets/admin')}}/css/custom.css?{{rand()}}">

<link rel="stylesheet" href="{{url('/resources/assets/admin')}}/css/jquery.mCustomScrollbar.min.css?{{rand()}}">

<!-- InstanceBeginEditable name="head" -->
<link rel="stylesheet" href="{{url('/resources/assets/admin')}}/css/colorpicker.css">
<link rel="stylesheet" href="{{url('/resources/assets/admin')}}/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css">   
<link rel="stylesheet" href="{{url('/resources/assets/admin')}}/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css">   

<!-- InstanceEndEditable -->
{!! theme_css() !!}

<style type="text/css">
	#wrapperloading {
		width: 100%;
		height: 100%;
		position: fixed;
		top: 0%;
		left: 0%;
		z-index: 2000;
		display: none;
		background: url({{url('/resources/assets/admin/images/loaderbg.png')}}) repeat;
	}
	#wrapperloading #loading {
		position: fixed;
		top: 40%;
		left: 50%;
		font-weight: bold;
		text-align: center;
	}
	.d-none{display: none !important;}
</style>
