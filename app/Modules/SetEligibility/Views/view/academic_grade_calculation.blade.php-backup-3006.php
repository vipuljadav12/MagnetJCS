@php		
	if(isset($eligibility))
    {
		$content = json_decode($eligibility->content) ?? null;
		$scoring = json_decode($eligibility->content)->scoring ?? null;
		/*print_r($scoring);
		echo "<br><hr>";
		print_r($content);*/
    }
@endphp

@if(isset($scoring->type) && $scoring->type == "GA")
<div class="card shadow">
    <div class="card-header">Grade Average</div>
    <div class="card-body">
        <div class="form-group row">
            <label class="control-label col-12 col-md-12">{{$eligibility->name ?? ""}} :</label>
            <div class="@if(isset($scoring->method) && $scoring->method == "DD") col-12 col-md-12 @else col-6 col-md-6 @endif">
                <input type="text" class="form-control" value="">
            </div>
            @if(isset($scoring->method) && $scoring->method != "DD")
            <div class="col-6">
                <select class="form-control custom-select">
                	@if($scoring->method == "YN")
                		@foreach($scoring->YN as $i=>$single)
	                		<option value="">{{$single ?? ""}}</option>
                    	@endforeach
                    @endif
                    @if($scoring->method == "NR")
                		@foreach($scoring->NR as $i=>$single)
	                		<option value="">{{$single ?? ""}}</option>
                    	@endforeach
                    @endif
                </select>
            </div>
            @endif
        </div>
    </div>
</div>
@endif
@if(isset($scoring->type) && $scoring->type == "GPA")
<div class="card shadow">
    <div class="card-header">GPA</div>
    <div class="card-body">
        <div class="form-group row">
            <label class="control-label col-12 col-md-12">{{$eligibility->name ?? ""}} :</label>
            <div class="card-body d-flex align-items-start">
                <div class="form-group row mr-10">
                    <label class="control-label col-12 col-md-12">A </label>
                    <div class="col-12 col-md-12 mb-10">
                        <input type="text" class="form-control" value="{{$content->GPA->A ?? ""}}">
                    </div>
                    @if(isset($scoring->method) && $scoring->method != "DD")
			            <div class="col-12">
			                <select class="form-control custom-select">
			                	@if($scoring->method == "YN")
			                		@foreach($scoring->YN as $i=>$single)
				                		<option value="">{{$single ?? ""}}</option>
			                    	@endforeach
			                    @endif
			                    @if($scoring->method == "NR")
			                		@foreach($scoring->NR as $i=>$single)
				                		<option value="">{{$single ?? ""}}</option>
			                    	@endforeach
			                    @endif
			                </select>
			            </div>
		            @endif
                </div>
                <div class="form-group row mr-10">
                    <label class="control-label col-12 col-md-12">B </label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="{{$content->GPA->B ?? ""}}">
                    </div>
                </div>
                <div class="form-group row mr-10">
                    <label class="control-label col-12 col-md-12">C </label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="{{$content->GPA->C ?? ""}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">D </label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="{{$content->GPA->D ?? ""}}">
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endif
@if(isset($scoring->type) && $scoring->type == "CLSG")
<div class="card shadow">
    <div class="card-header">Counts of Letters / Standard Grades</div>
    <div class="card-body">
        <div class="form-group row">
           	<label class="control-label col-12 col-md-12">{{$eligibility->name ?? ""}} :</label>
            <div class="@if(isset($scoring->method) && $scoring->method == "DD") col-12 col-md-12 @else col-6 col-md-6 @endif">
                <input type="text" class="form-control" value="">
            </div>
            @if(isset($scoring->method) && $scoring->method != "DD")
            <div class="col-6">
                <select class="form-control custom-select">
                	@if($scoring->method == "YN")
                		@foreach($scoring->YN as $i=>$single)
	                		<option value="">{{$single ?? ""}}</option>
                    	@endforeach
                    @endif
                    @if($scoring->method == "NR")
                		@foreach($scoring->NR as $i=>$single)
	                		<option value="">{{$single ?? ""}}</option>
                    	@endforeach
                    @endif
                </select>
            </div>
            @endif
        </div>
    </div>
</div>
@endif