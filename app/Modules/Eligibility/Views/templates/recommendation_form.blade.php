@php 
    if(isset($eligibilityContent))
    {
        // $allow_spreadsheet = json_decode($eligibilityContent->content)->allow_spreadsheet ?? null;
        $mainContent = json_decode($eligibilityContent->content);
        // dd($mainContent,$eligibilityContent);
    }
@endphp
    <div class="form-group">
        <label class="control-label">Eligibility Name : </label>
        <div class="">
            <input type="text" class="form-control" name="name" value="{{$eligibility->name ?? old('name')}}">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label">Select Teachers to receive Recommendation Form (Select all that apply) : </label>
        <div class="">
            <div class="d-flex flex-wrap">
                @php 
                    // $subjects = array("eng"=>"English","math"=>"Math","sci"=>"Science","ss"=>"Social Studies","o"=>"other");


                    // $subjects = array("eng"=>"English Teacher","math"=>"Math Teacher","sci"=>"Science Teacher","ss"=>"Social Studies Teacher","school_con"=>"School Counselor", "homeroom"=>"Homeroom Teacher", "principal"=>"Principal", "gift"=>"Gifted Teacher");
                    $subjects = config('variables.recommendation_subject');
                @endphp
                @foreach($subjects as $s=>$subject)
                    <div class="mr-20">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="checkbox{{$s}}" @if(isset($mainContent->subjects) && in_array($s, $mainContent->subjects)) checked @endif name="extra[subjects][]" value="{{$s}}">  
                            <label for="checkbox{{$s}}" class="custom-control-label">{{$subject}}</label></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label">Select Calculation of Scores : </label>
        <div class="">
            <select class="form-control custom-select" name="extra[calc_score]">
                <option value="">Select Option</option>
                <option value="1" @if(isset($mainContent->calc_score) && $mainContent->calc_score == 1) selected  @endif>Sum Scores</option>
                <option value="2" @if(isset($mainContent->calc_score) && $mainContent->calc_score == 2) selected  @endif>Average Scores</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label">Store for : </label>
        <div class="">
            <select class="form-control custom-select" name="store_for">
                <option >Select Option</option>
                <option value="DO" {{isset($eligibility->store_for) && $eligibility->store_for=='DO'?'selected':''}}>District Only</option>
                <option value="MS" {{isset($eligibility->store_for) && $eligibility->store_for=='MS'?'selected':''}}>MyPick System</option>
            </select>
        </div>
    </div>

    <div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-header" title="">Add Header</a></div>
    <div class="form-list">
        @if(isset($mainContent->header))
        {{-- {{dd($mainContent->header)}} --}}
            @foreach($mainContent->header as $h=>$header)
                <div class="card shadow">
                    <div class="card-header">
                        <div class="form-group">
                        <label class="control-label">
                            <a href="javascript:void(0);" class="mr-10 handle" title=""><i class="fas fa-arrows-alt"></i></a>
                            Header Name {{$h}}: 
                         </label>
                        <div class="">
                            <input type="text" class="form-control headerInput" name="extra[header][{{$h}}][name]" value="{{$header->name}}" id="header_{{$h}}">
                        </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group text-right">
                            <a href="javascript:void(0);" class="btn btn-secondary btn-sm add-option" title="" data-header="{{$h}}">Add Option</a>
                        </div>
                        <div class="option-list mt-10">
                            @if(isset($header->options))
                                @forelse($header->options as $o=>$option)
                                    <div class="form-group border p-10">
                                        <div class="row">
                                            <div class="col-12 col-md-7 d-flex flex-wrap align-items-center">
                                                <a href="javascript:void(0);" class="mr-10 handle2" title=""><i class="fas fa-arrows-alt"></i></a>
                                                <label for="" class="mr-10">Option {{$o}}: </label>
                                                <div class="flex-grow-1"><input type="text" class="form-control" name="extra[header][{{$h}}][options][{{$o}}]" value="{{$option ?? ""}}"></div>
                                            </div>
                                            <div class="col-10 col-md-5 d-flex flex-wrap align-items-center">
                                                <label for="" class="mr-10">Point : </label>
                                                <div class="flex-grow-1"><input type="text" class="form-control" name="extra[header][{{$h}}][points][{{$o}}]" value="{{$header->points->$o ?? ""}}"></div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            @endif
                        </div>
                        <div class="form-group text-right">
                            <a href="javascript:void(0);" class="btn btn-secondary btn-sm add-question" title="" data-header="{{$h}}">Add Question</a>
                        </div>
                        <div class="question-list mt-10">
                            @if(isset($header->questions))
                                @forelse($header->questions as $q=>$question)
                                    <div class="form-group border p-15">
                                        <label class="control-label d-flex flex-wrap justify-content-between">
                                            <span><a href="javascript:void(0);" class="mr-10 handle1" title=""><i class="fas fa-arrows-alt"></i></a>Question {{$q}} : </span>
                                        </label>
                                        <div class="">
                                            <input type="text" class="form-control" value="{{$question ?? ''}}" name="extra[header][{{$h}}][questions][{{$q}}]" >
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-description" title="">Add Description</a></div>
    <div class="card shadow">
        <div class="card-header">
            {{-- <div class="form-group"> --}}
                <label class="control-label">
                    <a href="javascript:void(0);" class="mr-10 handle" title=""><i class="fas fa-arrows-alt"></i></a>
                    Recommendation Description : 
                </label>
            {{-- </div> --}}
        </div>
        <div class="card-body">
            <div class="description-list mt-10">
                {{-- {{dd($mainContent->description)}} --}}
                @if(isset($mainContent->description))
                    @foreach($mainContent->description as $d=>$desc)
                        <div class="form-group border p-15">
                            <div class="">
                                <textarea class="form-control" name="extra[description][]">{{$desc ?? ''}}</textarea>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="form-group border p-15">
                        <div class="">
                            <textarea class="form-control" name="extra[description][]"></textarea>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
