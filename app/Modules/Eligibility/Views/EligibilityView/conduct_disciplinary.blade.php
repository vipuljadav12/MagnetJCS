<div class="card shadow">
    <div class="card-header">{{$eligibility->name}}</div>
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">A info</label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="">
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">B info</label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="">
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">C info</label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="">
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">D info</label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="">
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">E info</label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="">
                    </div>
                </div>
            </div>
            @if(json_decode($eligibility->content)->scoring->type!='DD')
            <div class="col-12 col-lg-6">
                <div class="form-group custom-none row">
                    <label class="control-label col-12 col-md-12">Select : </label>
                    <div class="col-12 col-md-12">
                        <select class="form-control custom-select template-type" name="extra[eligibility_type][type]">
                            <option value="">Select Option</option>
                            @if(json_decode($eligibility->content)->scoring->method=='YN')
                                @forelse(json_decode($eligibility->content)->scoring->YN as $yn)
                                    <option>{{$yn}}</option>
                                @empty
                                @endforelse
                            @else
                                @forelse(json_decode($eligibility->content)->scoring->NR as $nr)
                                    <option>{{$nr}}</option>
                                @empty
                                @endforelse
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            @endif  
        </div>
    </div>
</div>


