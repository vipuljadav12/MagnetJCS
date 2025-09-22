@section('putformstyle')
    @foreach($page_form as $key=>$value)
    @php
        if(isset($value['form_fields_style']) && $value['form_fields_style'] != '')
            $formstyle = json_decode($value['form_fields_style']);     

        if(isset($value['form_style']) && $value['form_style'] != '')
            $formcss = $value['form_style'];     
    @endphp
    @endforeach

    {!! $formcss ?? '' !!}       
@endsection
<div class="card-body pb-0 pt-0">
    <div class="row">
        <div class="col-12 col-sm-3 col-xl-3 bg-light rounded">
            <div class="mt-10 add-field-setting active">
                <form method="POST" action="{{url('admin/form/store/style')}}" id="frmDesignSetting">
                {{csrf_field()}}
                    <input type="hidden" name="page_id" value="{{ $page->page_id }}" >
                    <input type="hidden" name="pageContentId" value="{{ $page_content_id }}" >
                    <div class="font-20">Form Design</div>
                    <hr>
                    <div>
                        <div class="list-unstyled">                     
                            <div class="field-type-list card card-body" style="padding:10px !important;">
                                <div style="padding-bottom: 14px;border-bottom: 1px solid rgba(192,192,192,0.5);">
                                    <label class="mb-10">
                                        Headings
                                    </label>
                                    <input type="hidden" name="fieldName[]" value="heading">
                                    <div class="row text-center">
                                        <div class="col-sm-6" style="padding-right:0px;">
                                            <select class="select-css" data-name="heading" name="heading_font_style" id="heading_font_style">
                                                @foreach(getFontStyleList() as $key => $value)
                                                <option value="{{$value}}" style='font-family:{{$value}}'
                                                @if(isset($formstyle->heading_font_style) && $formstyle->heading_font_style == $value)selected="selected" @endif>{{$key}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-2" style="padding-right: 5px;padding-left: 5px;">
                                            <select class="select-css" data-name="heading" id="heading_font_size" name="heading_font_size">
                                                @foreach(getFontSizeList() as $key => $value)
                                                <option value="{{$value}}" @if(isset($formstyle->heading_font_size) && $formstyle->heading_font_size == $value) selected="selected" @endif>{{$key}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-4 d-flex" style="padding-left: 0px">
                                            <div class="fontdisplay ">
                                                <input type="checkbox" style="display:none;" data-name="heading" name="heading_isBold" id="heading_isBold" @if(isset($formstyle->heading_isBold)) checked @endif>
                                                <label for="heading_isBold">B</label>
                                            </div>
                                            <div class="fontdisplay ">
                                                <input type="checkbox" style="display:none;" data-name="heading" name="heading_isItalic" id="heading_isItalic" @if(isset($formstyle->heading_isItalic)) checked @endif>
                                                <label for="heading_isItalic" class="italic">I</label>
                                            </div>
                                            <div class="fontdisplay ">
                                                <input type="checkbox" style="display:none;" data-name="heading" name="heading_isUnderline" id="heading_isUnderline" @if(isset($formstyle->heading_isUnderline)) checked @endif>
                                                <label for="heading_isUnderline" class="underline">U</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div style="padding-bottom: 14px;border-bottom: 1px solid rgba(192,192,192,0.5);">
                                    <label class="mb-10">
                                        Labels
                                    </label>
                                    <input type="hidden" name="fieldName[]" value="label">
                                    <div class="row text-center">
                                        <div class="col-sm-6" style="padding-right:0px;">
                                            <select class="select-css" data-name="label" id="label_font_style" name="label_font_style">
                                                @foreach(getFontStyleList() as $key => $value)
                                                <option value="{{$value}}" style='font-family:{{$value}}' @if(isset($formstyle->label_font_style) && $formstyle->label_font_style == $value) selected="selected" @endif>{{$key}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-2" style="padding-right: 5px;padding-left: 5px;">
                                            <select class="select-css" data-name="label" name="label_font_size" id="label_font_size">
                                                @foreach(getFontSizeList() as $key => $value)
                                                    <option value="{{$value}}" @if(isset($formstyle->label_font_size) && $formstyle->label_font_size == $value) selected="selected" @endif>{{$key}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-4 d-flex" style="padding-left: 0px">
                                            <div class="fontdisplay ">
                                                <input type="checkbox" style="display:none;" data-name="label" name="label_isBold" id="label_isBold" @if(isset($formstyle->label_isBold)) checked @endif>
                                                <label for="label_isBold">B</label>
                                            </div>
                                            <div class="fontdisplay ">
                                                <input type="checkbox" style="display:none;" data-name="label" name="label_isItalic" id="label_isItalic" @if(isset($formstyle->label_isItalic)) checked @endif>
                                                <label for="label_isItalic" class="italic">I</label>
                                            </div>
                                            <div class="fontdisplay ">
                                                <input type="checkbox" style="display:none;" data-name="label" name="label_isUnderline" id="label_isUnderline" @if(isset($formstyle->label_isUnderline)) checked @endif>
                                                <label for="label_isUnderline" class="underline">U</label>   
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="padding-bottom: 14px;border-bottom: 1px solid rgba(192,192,192,0.5);">
                                    <label class="mb-10">
                                        Text
                                    </label>
                                    
                                    <input type="hidden" name="fieldName[]" value="text">
                                    <div class="row text-center">
                                        <div class="col-sm-6" style="padding-right:0px;">
                                            <select class="select-css" data-name="text" name="text_font_style" id="text_font_style">
                                                @foreach(getFontStyleList() as $key => $value)
                                                <option value="{{$value}}" style='font-family:{{$value}}' @if(isset($formstyle->text_font_style) && $formstyle->text_font_style == $value) selected="selected" @endif>{{$key}}</option>       
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-2" style="padding-right: 5px;padding-left: 5px;">
                                            <select class="select-css" data-name="text" name="text_font_size" id="text_font_size">
                                                @foreach(getFontSizeList() as $key => $value)
                                                <option value="{{$value}}" @if(isset($formstyle->text_font_size) && $formstyle->text_font_size == $value) selected="selected" @endif>{{$key}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-4 d-flex" style="padding-left: 0px">
                                            <div class="fontdisplay">
                                                <input type="checkbox" style="display:none;" data-name="text" name="text_isBold" id="text_isBold" @if(isset($formstyle->text_isBold)) checked @endif>
                                                <label for="text_isBold">B</label>
                                            </div>
                                            <div class="fontdisplay">
                                                <input type="checkbox" style="display:none;" data-name="text" name="text_isItalic" id="text_isItalic" @if(isset($formstyle->text_isItalic)) checked @endif>
                                                <label for="text_isItalic" class="italic">I</label>
                                            </div>
                                            <div class="fontdisplay">
                                                <input type="checkbox" style="display:none;" data-name="text" name="text_isUnderline" id="text_isUnderline" @if(isset($formstyle->text_isUnderline)) checked @endif>
                                                <label for="text_isUnderline" class="underline">U</label>   
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div style="padding-bottom: 14px;border-bottom: 1px solid rgba(192,192,192,0.5);">
                                    <label class="mb-10">
                                        Links
                                    </label>
                                    <input type="hidden" name="fieldName[]" value="link">
                                    <div class="row text-center">
                                        <div class="col-sm-6" style="padding-right:0px;">

                                            <select class="select-css" data-name="link" id="link_font_style" name="link_font_style">
                                                @foreach(getFontStyleList() as $key => $value)
                                                <option value="{{$value}}" style='font-family:{{$value}}' @if(isset($formstyle->link_font_style) && $formstyle->link_font_style == $value) selected="selected" @endif>{{$key}}</option>       
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-2" style="padding-right: 5px;padding-left: 5px;">

                                            <select class="select-css" data-name="link" id="link_font_size" name="link_font_size">
                                                @foreach(getFontSizeList() as $key => $value)
                                                <option value="{{$value}}" @if(isset($formstyle->link_font_size) && $formstyle->link_font_size == $value) selected="selected" @endif>{{$key}}</option> 
                                                @endforeach
                                            </select>
                                        </div> 

                                        <div class="col-sm-4 d-flex" style="padding-left: 0px">                                           
                                            <div class="fontdisplay">
                                                <input type="checkbox" style="display:none;" data-name="link" name="link_isBold" id="link_isBold" @if(isset($formstyle->link_isBold)) checked @endif>
                                                <label for="link_isBold">B</label>
                                            </div>
                                            <div class="fontdisplay">
                                                <input type="checkbox" style="display:none;" data-name="link" name="link_isItalic" id="link_isItalic" @if(isset($formstyle->link_isItalic)) checked @endif>
                                                <label for="link_isItalic" class="italic">I</label>
                                            </div>
                                            <div class="fontdisplay">
                                                <input type="checkbox" style="display:none;" data-name="link" name="link_isUnderline" id="link_isUnderline" @if(isset($formstyle->link_isUnderline)) checked @endif>
                                                <label for="link_isUnderline" class="underline">U</label>   
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div style="padding-bottom: 14px;border-bottom: 1px solid rgba(192,192,192,0.5);">
                                    
                                    <label class="mb-10">
                                        Button Text
                                    </label>
                                    
                                    <input type="hidden" name="fieldName[]" value="button">
                                    <div class="row text-center">
                                        <div class="col-sm-6" style="padding-right:0px;">

                                            <select class="select-css" data-name="button" name="button_font_style" id="button_font_style" >
                                                @foreach(getFontStyleList() as $key => $value)
                                                <option value="{{$value}}" style='font-family:{{$value}}' @if(isset($formstyle->button_font_style) && $formstyle->button_font_style == $value) selected="selected" @endif>{{$key}}</option>       
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-2" style="padding-right: 5px;padding-left: 5px;">

                                            <select class="select-css" data-name="button" name="button_font_size" id="button_font_size">
                                                @foreach(getFontSizeList() as $key => $value)
                                                <option value="{{$value}}" @if(isset($formstyle->button_font_size) && $formstyle->button_font_size == $value) selected="selected" @endif>{{$key}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-4 d-flex" style="padding-left: 0px">
                                            <div class="fontdisplay">

                                                <input type="checkbox" style="display:none;" data-name="button" name="button_isBold" id="button_isBold" @if(isset($formstyle->button_isBold)) checked @endif>
                                                <label for="button_isBold">B</label>
                                            </div>
                                            <div class="fontdisplay">
                                                <input type="checkbox" style="display:none;" data-name="button" name="button_isItalic" id="button_isItalic" @if(isset($formstyle->button_isItalic)) checked @endif>
                                                <label for="button_isItalic" class="italic">I</label>
                                            </div>
                                            <div class="fontdisplay">

                                                <input type="checkbox" style="display:none;" data-name="button" name="button_isUnderline" id="button_isUnderline" @if(isset($formstyle->button_isUnderline)) checked @endif>
                                                <label for="button_isUnderline" class="underline">U</label>   
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="field-type-list card card-body">                            
                                <input type="submit" class="form-control btn btn-primary" value="Save">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-12 col-sm-9 col-xl-9 pb-10 pt-30 scroll-height">
            <form id="frmStyleChange">
                {{csrf_field()}}
                @if(count($page_form) > 0)
                    @php $no_item = count($page_form); @endphp
                    @foreach($page_form as $key=>$value)
                        @if($key == 0)                
                        <div class="row align-items-start"> 
                        @endif
                        @php
                            $layout = $value->layout;
                            $class = getClassbylayout($value->layout);
                        @endphp  
                        <div class="custom-fields pl-15 pr-15 {{ $class }}" id="{{ $value->page_content_id.'_'.$layout.'_'.$key }}">
                            <div class="row pl-15 pr-15">
                                @if(count($value['child']) > 0)

                                    @foreach($value['child'] as $keys=>$values)
                                        <div class="col-12">
                                            <div class="cust-box-out">                                                
                                                {!! getFormtypeView($values->type_id,$values) !!}
                                            </div>
                                        </div>
                                    @endforeach
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
                @endif
                <div class="text-center" id="button_setting">
                    @if($previous != "")
                        <button class="btn btn-primary form-button" name="Previous" onclick="event.preventDefault();">Previous</button>
                    @endif
                    @if($next != "")
                        <button class="btn btn-primary form-button" name="Next" onclick="event.preventDefault();">Next</button>
                    @endif
                    @if($next == "")
                        <button class="btn btn-primary form-button" name="Save" onclick="event.preventDefault();"DW>Save</button>
                    @endif
                </div>
            </form>        
        </div>
    </div>                    
</div>