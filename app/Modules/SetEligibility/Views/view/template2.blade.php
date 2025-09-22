@php
    if(isset($eligibility))
    {
        // $allow_spreadsheet = json_decode($eligibility->content)->allow_spreadsheet ?? null;
        $mainContent = json_decode($eligibility->content);
        // print_r($mainContent);
    }
@endphp
<div class="card shadow">
<div class="card-header">{{$eligibility->name}}</div>
<div class="card-body">
    @if(isset($mainContent->header))
        @foreach($mainContent->header as $h=>$header)
            <div class="row">
                <div class="col-12">
                    <div class="card show">
                        <div class="card-header">
                            {{$header->name}}
                        </div>
                        <div class="card-body">
                            <div class="row" style="1border:1px solid red;">
                                <div class="col-6">
                                    <h6 class="text-center"><strong>Questions</strong></h6>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-center"><strong>Options</strong></h6>
                                </div>
                                @forelse($header->questions as $q=>$question)
                                    <div class="col-6" style="1border:1px green solid;">
                                        <p class="form-control">{{$question->name}}</p>
                                    </div>
                                    <div class="col-6">
                                        @php
                                            $options = $question->options ?? null;
                                        @endphp
                                        @if(isset($options))
                                            <select class="form-control">
                                                @forelse($options as $o=>$option)
                                                    <option>{{$option}}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        @endif
                                    </div>
                                @empty
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
</div>