@extends('layouts.admin.app')
@section('putformstyle')
    {!! $content[0]->PageForm[0]->form_style ?? ''!!}     
@endsection
@section('content')
<div class="">
        <div class="heading-and-filters d-flex justify-content-between align-items-center">
            <div class="dashboard-top-tabs">
                <ul class="nav nav-tabs">
                    <li class="page-title"><a href="{{url('/admin/Page/edit/'.$page->page_id)}}">General</a></li>
                    <li class="page-title"><a href="{{url('/admin/form/'.$page_content_id)}}">Create</a></li>
                    <li class="page-title active"><a href="#">Preview</a></li>
                    <li class="page-title"><a href="{{url('/admin/Answers/'.$page->page_id)}}">Submission</a></li>
                </ul>
            </div>

            @if($page->page_status != 'Publish')
                <div class="text-right mb-10 mt-10 mr-10"><a href="{{url('/admin/Page')}}" title="" class="btn btn-orange text-white"><i class="fa fa-arrow-left"></i> Back</a><a class="btn btn-primary publishPage text-white ml-10" style=""> Publish Form <I class="far fa-paper-plane"></I></a></div>
            @else
                <div class="text-right mb-10 mt-10 mr-10"><a href="{{url('/admin/Page')}}" title="" class="btn btn-orange text-white"><i class="fa fa-arrow-left"></i> Back</a><a target="_blank" href="{{ url('/').'/'.$page->access_url }}" class="btn btn-blue text-white ml-5 mr-5 pb-10 pt-10" ><i class="fas fa-external-link-alt"></i></a></div>
            @endif
        </div>
        <div class="m-10">
            @if(Session::has('success'))
                <p class="alert  alert-success">{{ Session::get('success') }}</p>
            @endif
        </div>
        @if($page->show_logo == "Y")
            <header>
                <div class="text-center mb-40">
                    <a href="javascript:void(0);" title="" class=""><img src="{{url('/resources/assets/front/images/logo_black.png')}}" title="" alt=""></a>
                </div>
            </header>
        @endif
    <hr>
    <div class="text-center mb-30 m-10">
        {!! $page->description !!}
    </div>
    <div class="mt-20 p-40 pt-10">
        @if($length>1)
            {{-- Multiple Page View --}}
            {{csrf_field()}}
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                @foreach($content as $key=>$val)
                    {{-- <li class="active"><a data-toggle="tab" href="#home">Home</a></li> --}}
                    <li class="nav-item">
                        <a class="nav-link @if($key==0) active @endif" id="page-tab{{$key}}" data-toggle="tab" href="#page{{$key}}" role="tab" aria-controls="page{{$key}}" aria-selected="true">Page {{$key+1}}</a>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content" id="myTabContent">
                @foreach($content as $key1=>$val)
                    <div class="tab-pane slide @if($key1==0) show active @endif" id="page{{$key1}}" role="tabpanel" tabid="{{$key1}}" aria-labelledby="page-tab{{$key1}}">
                        @if(isset($val->PageForm) && count($val->PageForm)>0)
                            <div class="row">
                                @foreach($val->PageForm as $key2=>$val2)
                                    <div class="{{getClassbylayout($val2->layout)}}">
                                        {!!getFormbyId($val2->form_id)!!}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="">
                @if(isset($content[0]->PageForm) && count($content[0]->PageForm)>0)
                    <div class="row">
                        @foreach($content[0]->PageForm as $key2=>$val2)
                            <div class="{{getClassbylayout($val2->layout)}}">
                                {!!getFormbyId($val2->form_id)!!}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
@section('footscript')
    <script type="text/javascript">
        $(function(){
            /*-- Switchery JS Start  --*/   
            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch')); 
            elems.forEach(function(html) {
                var switchery = new Switchery(html, { size: 'small' });
            });

            /*-- Switchery JS End  --*/ 
        });
        $(document).on('click','.publishPage',function(){

            swal({ 
              title: "Are you sure you want to publish this form?",
                text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
              }).then(function() {
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    url: "{{url('/admin/Page/publish/'.$page->page_id)}}",
                    method: 'GET',
                    success: function (response) {
                        window.location.reload();
                    }
                });
            });
            
        });
    </script>
@endsection
</html>