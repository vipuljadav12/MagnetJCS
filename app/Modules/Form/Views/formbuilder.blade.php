<style type="text/css">
    .input-border
    {
        border:1px solid rgba(192,192,192,0.5) !important;
        padding:1px !important;
    }
    .mt5
    {
        margin-top: 5px !important;
    }
    .mb5
    {
        margin-bottom: 5px !important;
    }
    .form-group-input{padding: 20px !important}
    .form-group{margin-bottom: 0px !important}
    .form-group-input:hover,.FormPageTitle:hover,.FormPageTitle:focus
    {
        /*border:1px solid rgba(192,192,192,0.2) !important;
        padding:1px !important;
        padding:10px;
        margin-left: 5px !important;
        margin-right: 5px !important;*/


        /*padding-right:10px;
        padding-right:10px;
        padding-right:10px;*/
        padding: 20px !important;
        background: #F2F4F8 !important;
        border: 1px solid #cfd8dc;
    }
    .selectedForChange
    {
        padding: 20px !important;
                background: #F2F4F8 !important;
                border: 1px solid #cfd8dc;

       /* border:1px solid #00346b;
        margin-left: 5px !important;
        margin-right: 5px !important;
        padding: 20px !important;*/

    }
    .m-t-0
    {
        margin-top: 0px !important;
    }
    .m-t-5
    {
        margin-top: 5px !important;
    }
    
    .m-b-0
    {
        margin-bottom: 0px !important;
    }
    .w-30
    {
        width: 30% !important;
    }
    .editor-col-spaces
    {
        margin-top: 5px;
        padding-bottom: 5px;
        border:0.1px solid rgba(192,192,192,0);
    }
    .editorBox
    {
        margin-right:0.1px !important;
        margin-left:0.5px !important;
    }
</style>
<div class="">
    <div class="row">
        <div class="col-12 col-md-2">
            <ul class="list-unstyled" id="draggable">
                @forelse($formFields as $fld=>$formField)
                    <li class="field-type-list field-type-menu-item border mb-5 p-5" data-type="{{$formField->type}}" data-type-id="{{$formField->id}}" data-form-id="{{$form->id}}">
                        <i class="fas fa-ellipsis-v mr-10"></i><i class="{{$formField->icon ?? ""}}"></i> <span>{{ucwords($formField->name) ?? ""}}</span>
                    </li>
                @empty
                @endforelse
            </ul>
        </div>
        <div class="col-12 col-md-7">
            <input type="hidden" name="form_source_code" value="">
            <div id="form_data">
                <div class='card'>
                    <div class='card-header'>Step {{$page_id}} -  <span contenteditable="true" class="FormPageTitle" data-page-id="{{$page_id}}" data-form-id="{{$form->id}}"">{!!getFormPageTitle($form->id,$page_id) ?? "Please enter your student's requested information" !!}</span></div>
                    <input type="hidden" name="form_id" id="form_id"> 
                    <div class='card-body input_container_builder' id="input_container_builder">
                       
                    </div>

                    @if($form->no_of_pages > 1)

                         <div class="text-center float-center" style="margin-top: 10px;margin-bottom: 10px;">
                            @if($page_id > 1)
                                <a class="btn btn-secondary saveFormCreate" href="{{url('/admin/Form/edit/'.($page_id-1).'/'.$form->id)}}"><i class="fa fa-arrow-left"></i> Previous Page</a>
                            @endif
                            @if($page_id < $form->no_of_pages)
                                <a class="btn btn-secondary saveFormCreate" href="{{url('/admin/Form/edit/'.($page_id+1).'/'.$form->id)}}">Next Page <i class="fa fa-arrow-right"></i></a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3 property_section">
            <div class="card p-5 d-none" id="fieldEditor">
                <ul class="list-unstyled">
                    {{-- {{getValuesForms()}} --}}
                </ul>
            </div>
        </div>
    </div>
</div>