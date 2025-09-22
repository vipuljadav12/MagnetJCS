<style>
        .collapsible-link::before {
            content: '';
            width: 14px;
            height: 2px;
            background: #333;
            position: absolute;
            top: calc(50% - 1px);
            right: 1rem;
            display: block;
            transition: all 0.3s;
        }
    
        /* Vertical line */
        .collapsible-link::after {
            content: '';
            width: 2px;
            height: 14px;
            background: #333;
            position: absolute;
            top: calc(50% - 7px);
            right: calc(1rem + 6px);
            display: block;
            transition: all 0.3s;
        }
    
        .collapsible-link[aria-expanded='true']::after {
            transform: rotate(90deg) translateX(-1px);
        }
    
        .collapsible-link[aria-expanded='true']::before {
            transform: rotate(180deg);
        }
        
    </style>
        <div class="card shadow">
            <div class="card-body">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 mx-auto">
                            <div id="accordionExample" class="">
                                <!-- Accordion item 2 -->
                                @forelse($data['grades'] as $key=>$grade)
                                    <div class="card" style="width: 100%">
                                        <div id="headingTwo" class="card-header bg-white shadow-sm border-0">
                                            <h6 class="mb-0 font-weight-bold"><a href="#" data-toggle="collapse" data-target="#{{$grade->name}}" aria-expanded="false" aria-controls="{{$grade->name}}" class="d-block position-relative collapsed text-dark text-uppercase collapsible-link py-2">{{$grade->name}}</a></h6>
                                        </div>
                                        {{-- <input type="hidden" name="application_id" value="{{$id}}"> --}}
                                        <div id="{{$grade->name}}" aria-labelledby="heading{{$grade->name}}" data-parent="#accordionExample" class="collapse">
                                            <div class="card-body p-5 mt-20">
                                                <div class="row pl-10">
                                                    @forelse($data['subjects'] as $key=>$subject)
                                                        <div class="col-md-3 mb-20">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    {{$subject}}
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    @php $subject=strtolower(str_replace(' ','_',$subject)) @endphp
                                                            {{-- !empty($data['subjectManagement']->where('grade_id',$grade->id)->where($subject,'Y')->first())--}}
                                                                    <input type="hidden" name="gradeSubject[{{$grade->name}}][{{$subject}}]" value="N">
                                                                    <input type="checkbox" value="Y" name="gradeSubject[{{$grade->name}}][{{$subject}}]" class="js-switch js-switch-1 js-switch-xs status" data-size="Small" @if(!isset($data['subjectManagement'][$grade->name][$subject])||$data['subjectManagement'][$grade->name][$subject]=='Y') checked @endif />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @empty
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>