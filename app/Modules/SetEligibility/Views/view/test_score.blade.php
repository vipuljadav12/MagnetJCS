<div class="card shadow">
    <form id="extraValueForm" action="{{url('admin/SetEligibility/extra_values/save')}}" method="post">
        {{csrf_field()}}
        <input type="hidden" name="program_id" value="{{$req['program_id']}}">
        <input type="hidden" name="eligibil`ity_id" value="{{$req['eligibility_id']}}">
        <input type="hidden" name="eligibility_type" value="{{$req['eligibility_type']}}">
        <input type="hidden" name="application_id" value="{{$req['application_id']}}">

        <div class="card-header">{{$eligibility->name}}</div>
        <div class="card-body">
            <div class="form-group wp_container row">
                 @php $box_count = json_decode($eligibility->content)->eligibility_type->box_count @endphp
                    @php
                        if(empty($extraValue['ts_scores'])){
                            $extraValue['ts_scores'] = [''];
                        }
                    @endphp
                    @php $count = 1 @endphp
                    @foreach($extraValue['ts_scores'] as $key=>$value)
                        <div class="col-12 row wp_row">
                            <div class="col-12">Name of Test Title Score Field {{$count}}</div>
                            <div class="col-12"> 
                                <input type="text" class="form-control mb-2" name="value[ts_scores][{{$count}}]" value="{{$value}}">
                            </div>
                        </div>
                        @php $count++ @endphp
                    @endforeach
                    @for($i=$count; $i <= $box_count; $i++)
                        <div class="col-12 row wp_row">
                            <div class="col-12">Name of Test Title Score Field {{$i}}</div>
                            <div class="col-12"> 
                                <input type="text" class="form-control" name="value[ts_scores][{{$i}}]" value="">
                            </div>
                        </div>
                    @endfor
            </div>
        </div>
    </form>
</div>