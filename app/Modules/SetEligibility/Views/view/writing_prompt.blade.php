<div class="card shadow">
    <form id="extraValueForm" action="{{url('admin/SetEligibility/extra_values/save')}}" method="post">
        {{csrf_field()}}
        <input type="hidden" name="program_id" value="{{$req['program_id']}}">
        <input type="hidden" name="eligibil`ity_id" value="{{$req['eligibility_id']}}">
        <input type="hidden" name="eligibility_type" value="{{$req['eligibility_type']}}">
        <input type="hidden" name="application_id" value="{{$req['application_id']}}">

        @php
            $late_submission = 0;
            if (isset($req['late_submission'])) {
                $late_submission = $req['late_submission'];
            }

        @endphp
        <input type="hidden" name="late_submission" value="{{$late_submission}}">

        <div class="card-body">
            <div class="row">
                <div class="form-group col-12">
                        <label class="control-label pl-0">Program Intro Text</label>
                        <div class="col-12" style="padding: 5px;">
                            <textarea name="value[intro_text]" class="form-control">{{$extraValue['intro_text'] ?? ''}}</textarea>
                        </div>
                        
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                        <p>Set writing prompt questions.</p>
                </div>
                <div class="col-12">
                    <div class="form-group row wp_container">
                        {{-- <label class="control-label col-12 col-md-12"># Susp</label> --}}
                        {{-- <div class="col-12 row"> --}}
                        @php
                            if(empty($extraValue['wp_question'])){
                                $extraValue['wp_question'] = [''];
                            }
                            $extra_txt = '';
                            if (count($extraValue['wp_question']) == 1) {
                                $extra_txt = 'd-none';
                            }
                            $i=1;
                        @endphp
                        
                        @foreach($extraValue['wp_question'] as $wp_question)
                        <div class="col-12 row wp_row">
                            <div class="col-10 ml-2"> 
                                <textarea class="form-control mb-2" name="value[wp_question][{{$i}}]">{{$wp_question}}</textarea>
                            </div>
                            <div class="col-2 row ml-1" style="padding: 5px;">
                                <a href="javascript:void(0)" class="wp_add" style="color: green;"><i class="fa fa-plus mr-1" aria-hidden="true"></i></a>
                                <a href="javascript:void(0)" class="wp_remove ml-1 {{$extra_txt}}" style="color: red;"><i class="fa fa-minus" aria-hidden="true"></i></a>
                            </div>
                        </div>
                        @php ++$i; @endphp
                        @endforeach
                            
                    </div>
                </div>
                
            </div>
        </div>
    </form>
</div>



<script type="text/javascript">
    $(document).ready(function(){
        $(document).find("#exampleModalLabel1").html("Writing Prompt");
    });

    // form validation
    $('#extraValueForm').validate();
    dynamicFieldsValidation();
</script>