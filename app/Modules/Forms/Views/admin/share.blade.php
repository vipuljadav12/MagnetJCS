<div class="card-body pb-0 pt-0">
    <div class="row">
        <div class="col-12 col-sm-3 col-xl-3 bg-light rounded">
            <div class="mt-10 add-field-setting active">
                <div class="font-20">Share</div>
                <hr>
                <div>
                    <div class="field-type-list-detail style-property">
                        <div class="row">
                            @if($page->page_status == 'Publish')
                            <div class="col-12 col-sm-12">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <span><strong>Embed in your site</strong></span><br>
                                        <p class="pt-5">Simply copy & paste the code below into your html!</p>
                                        <hr>
                                    </div>  
                                </div>
                                <div class="col-md-12 pt-10">
                                    <div class="form-group">
                                        <textarea class="form-control" style="height: 110px !important;" disabled><iframe src="{{ url('/').'/iwsForms/'.$page->access_url }}" width="500px" height="720px"></iframe>
                                        </textarea>
                                        
                                    </div>
                                </div>
                              
                                <div class="col-md-12 pt-10">
                                    <div class="form-group">
                                        
                                        <span><strong>Share a link</strong></span><br>
                                        <p class="pt-5">Use this URL to create a hyperlink to your form.</p>
                                       
                                        <hr>
                                    </div>
                                </div>
                                <div class="col-md-12 pt-10">
                                    <div class="form-group">
                                        <textarea class="form-control" style="height: 60px !important;" disabled>{{ url('/').'/'.$page->access_url }}
                                        </textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-12 pt-10">
                                    <div class="form-group">
                                        <span><strong>Share on social platform</strong></span><br>
                                        <hr>
                                    </div>  
                                </div>
                                <div class="col-sm-12 pt-10">
                                    <div class="row">
                                        <a class="pl-15" target="_blank" href="https://twitter.com/intent/tweet?text={{ $page->page_title }}&url={{ url('/').'/'.$page->access_url }}"><i class="fab fa-twitter"></i></a>
                                        <a class="pl-15" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={{ url('/').'/'.$page->access_url }}&p[title]={{ $page->page_title }}"><i class="fab fa-facebook-square"></i></a>
                                        <a class="pl-15" target="_blank" href="http://pinterest.com/pin/create/button/?url={{ url('/').'/'.$page->access_url }}&description={{ $page->page_title }}"><i class="fab fa-pinterest"></i></a>
                                        <a class="pl-15" target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&url={{ url('/').'/'.$page->access_url }}&title={{ $page->page_title }}&source={{ url('/') }}"><i class="fab fa-linkedin-in"></i></a>
                                        <a class="pl-15" href="#"><i class="fab fa-twitter"></i></a>
                                        <a class="pl-15" href="#"><i class="fab fa-facebook-square"></i></a>
                                        <a class="pl-15" href="#"><i class="fab fa-pinterest"></i></a>
                                        <a class="pl-15" href="#"><i class="fab fa-linkedin-in"></i></a>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="col-12 col-sm-12">
                                <label><strong>Publish the form first.</strong></label>
                            </div>
                            @endif
                        </div>
                     </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-9 col-xl-9 pb-10 pt-30 scroll-height">
            <form method="get" action="#" id="">
                {{csrf_field()}}
                @if(count($page_form) > 0)
                    @php $no_item = count($page_form); @endphp
                    @foreach($page_form as $key=>$value)
                        @if($key == 0)
                            <input type="hidden" name="form_title" id="form_title" value="{{ $value->form_title }}">
                            <div class="row align-items-start"> 
                        @endif
                        @php
                            $layout = $value->layout;
                            $class = getClassbylayout($value->layout);
                        @endphp
                            <div class="custom-fields pl-15 pr-15 {{ $class }}" id="{{ $value->page_content_id.'_'.$layout.'_'.$key }}">
                                <div class="row pl-15 pr-15">
                                    <input type="hidden" name="form_id" value="{{ $value->form_id }}" >
                                    @if(count($value['child']) > 0)
                                        @foreach($value['child'] as $keys=>$values)
                                            <div class="col-12 cust-box">
                                                <div class="cust-box-out">
                                                    {!! getFormtypeView($values->type_id,$values) !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-12 cust-box">
                                            <div class="cust-box-out">
                                                <div class="cust-box-in">
                                                    <div class="box_control text-center p-5 fa-2x">+                                                
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @php
                            $last_key = key(end($page_form));
                        @endphp
                        @if($key+1 == $no_item)
                            </div>
                        @endif
                    @endforeach
                @else
                    {{--<div class="ff-title-box active font-20">Untitled</div>--}}
                    <input type="hidden" name="form_title" id="form_title">
                    @php
                        $layout = explode("-",$data->page_layout);
                    @endphp
                    <div class="row align-items-start"> 
                        @foreach($layout as $key=>$value)
                            @php
                                $class = getClassbylayout($value);
                            @endphp
                        <div class="custom-fields pl-15 pr-15 {{ $class }}" id="{{ $data->page_content_id.'_'.$value.'_'.$key }}">
                            <div class="col-12 cust-box">
                                <div class="cust-box-out">
                                    <div class="cust-box-in">
                                        <div class="box_control text-center p-5 fa-2x">+                                                
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        @endforeach
                    </div>
                @endif
                <div class="text-center button_setting">
                    @if($previous != "")
                        <input type="button" class="btn btn-primary" name="submit" value="Previous" onclick="formSubmit('previous',{{ $data->page_content_id }})">
                    @endif
                    @if($next != "")
                        <input type="button" class="btn btn-primary" name="submit" value="Next" onclick="formSubmit('next',{{ $data->page_content_id }})">
                    @endif
                    @if($next == "")
                        <input type="button" class="btn btn-primary" name="submit" value="Save" onclick="formSubmit('save',{{ $data->page_content_id }})">
                    @endif
                </div>
            </form>
        </div>
    </div>                    
</div>