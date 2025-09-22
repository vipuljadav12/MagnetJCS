<div class="card shadow">
    <div class="card-header">{{$eligibility->name}}</div>
    <div class="card-body">
        <div class="form-group custom-none">
{{--            <label class="control-label">{{$eligibility->name}}</label>--}}
            <div class="">
                <select class="form-control custom-select template-type" name="extra[eligibility_type][type]">
                    <option value="">Select Option</option>
                    @if(json_decode($eligibility->content)->eligibility_type->type=='YN')
                        @forelse(json_decode($eligibility->content)->eligibility_type->YN as $yn)
                            <option>{{$yn}}</option>
                        @empty
                        @endforelse
                    @else
                        @forelse(json_decode($eligibility->content)->eligibility_type->NR as $nr)
                            <option>{{$nr}}</option>
                        @empty
                        @endforelse
                    @endif
                </select>
            </div>
        </div>
    </div>
</div>