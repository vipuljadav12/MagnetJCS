<header>
    <div class="p-10 bg-secondary text-center" style="background: {{Session::get("theme_color")}} !important">
    	<a href="javascript:void(0);" class="d-inline-block" title="" id="logo">
    		<img src="{{(isset($application_data) ? getDistrictLogo($application_data->display_logo) : getDistrictLogo())}}" title="" alt="" style="max-height: 50px;">
    	</a>
    	<div class="align-middle d-inline-block ml-20 text-left d-none">
    		<div>{!! $district->address ?? '' !!}</div>
    		<div>{!! $district->city ?? '' !!}, {!! $district->state  ?? '' !!} {!! $district->zipcode ?? '' !!}</div>
    		<div>{{$district->magnet_point_contact_phone ?? '' }}</div>
    	</div>
    </div>
</header>