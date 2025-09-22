<div class="card-body pb-0 pt-0">
    <div class="row">
        <div class="col-12 col-sm-3 col-xl-3 bg-light rounded">
            <div class="mt-10 form-setting d-none">
                <div class="font-20">Form Setting</div>
                <hr>
                <div class="form-group">
                    @if(count($page_form) > 0)
                        @foreach($page_form as $key=>$value)
                            @if($key == 0)
                                <label class="">Title</label>
                                <input type="text" class="form-control" id="formtitle" value="{{ $value->form_title }}">
                            @endif
                        @endforeach
                    @else
                        <label class="">Title</label>
                        <input type="text" class="form-control" id="formtitle">    
                    @endif
                </div>
            </div>
            <div class="mt-10 add-field-setting active">
                <div class="font-20">Add Field</div>
                <hr>
                <div>
                    <div class="field-type-list-detail textbox-property d-none">
                        @include("Form::fieldProperty.textboxProperty")
                    </div>
                    <div class="field-type-list-detail email-property d-none">
                        @include("Form::fieldProperty.emailProperty")
                    </div>
                    <div class="field-type-list-detail textarea-property d-none">
                        @include("Form::fieldProperty.textareaProperty")
                    </div>
                    <div class="field-type-list-detail multitext-property d-none">
                        @include("Form::fieldProperty.multitextProperty")
                    </div>
                    <div class="field-type-list-detail checkbox-property d-none">
                        @include("Form::fieldProperty.checkboxProperty")
                    </div>
                    <div class="field-type-list-detail radio-property d-none">
                         @include("Form::fieldProperty.radioProperty")
                    </div>
                    <div class="field-type-list-detail dropdown-property d-none">
                        @include("Form::fieldProperty.dropdownProperty")
                    </div>
                    {{-- 4040 --}}
                    <div class="field-type-list-detail datepicker-property d-none">
                        @include("Form::fieldProperty.datepickerProperty")
                    </div>
                    <div class="field-type-list-detail uploadfile-property d-none">
                        @include("Form::fieldProperty.uploadfileProperty")
                    </div>
                    <div class="field-type-list-detail toggle-property d-none">
                        @include("Form::fieldProperty.togglefileProperty")
                    </div>
                    {{-- 404 --}}
                    <div class="field-type-list-detail button-property d-none">
                        <div class="form-group"><label>Button Text</label> 
                            <input type="text" class="form-control" id="button_text_setting" property="button" onchange="setValue(this)">
                        </div>
                    </div>
                    <div class="list-unstyled field-type" id="draggable">
                        <div class="field-type-list">
                            <i class="fas fa-ellipsis-v mr-10"></i><i class="fas fa-font"></i>
                            <span class="ml-10">Text Box</span>
                        </div>                                      
                        <div class="field-type-list">
                            <i class="fas fa-ellipsis-v mr-10"></i><i class="fas fa-align-justify"></i>
                            <span class="ml-10">Textarea</span>
                        </div>
                        <div class="field-type-list">
                            <i class="fas fa-ellipsis-v mr-10"></i><i class="fas fa-indent"></i>
                            <span class="ml-10">Editor</span>
                        </div>
                        <div class="field-type-list">
                            <i class="fas fa-ellipsis-v mr-10"></i><i class="far fa-envelope"></i>
                            <span class="ml-10">Email Address</span>
                        </div>
                        <div class="field-type-list">
                            <i class="fas fa-ellipsis-v mr-10"></i><i class="far fa-check-square"></i>
                            <span class="ml-10">Check Box</span>
                        </div>
                        <div class="field-type-list">
                            <i class="fas fa-ellipsis-v mr-10"></i><i class="far fa-dot-circle"></i>
                            <span class="ml-10">Radio Button</span>
                        </div>
                        <div class="field-type-list">
                            <i class="fas fa-ellipsis-v mr-10"></i><i class="far fa-arrow-alt-circle-down"></i>
                            <span class="ml-10">Drop Down</span>
                        </div>
                        <div class="field-type-list">
                            <i class="fas fa-ellipsis-v mr-10"></i><i class="far fa-calendar-alt"></i>
                            <span class="ml-10">Date</span>
                        </div>
                        <div class="field-type-list">
                            <i class="fas fa-ellipsis-v mr-10"></i><i class="fas fa-arrow-circle-up"></i>
                            <span class="ml-10">Upload File</span>
                        </div>
                        <div class="field-type-list">
                            <i class="fas fa-ellipsis-v mr-10"></i><i class="fas fa-toggle-off"></i>
                            <span class="ml-10">Toggle</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-10 custom-field-setting">
                <div class="font-20">Field Setting</div>
                <hr>
                <div class="form-group">
                    <label class="">Label</label>
                    <input type="text" class="form-control">
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-9 col-xl-9 pb-10 pt-30 scroll-height">
            <form method="POST" action="{{url('admin/form/store')}}" id="formSubmit">
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
                <div class="text-center button_setting mt-10">
                    @if($previous != "")
                        <input type="button" class="btn btn-primary form-button" name="submit" value="Previous" onclick="formSubmit('previous',{{ $data->page_content_id }})">
                    @endif
                    @if($next != "")
                        <input type="button" class="btn btn-primary form-button" name="submit" value="Next" onclick="formSubmit('next',{{ $data->page_content_id }})">
                    @endif
                    @if($next == "")
                        <input type="button" class="btn btn-primary form-button" name="submit" value="Save" onclick="formSubmit('save',{{ $data->page_content_id }})">
                    @endif
                </div>
            </form>
        </div>
    </div>                    
</div>