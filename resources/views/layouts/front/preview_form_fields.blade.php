    @foreach($data as $key=>$row)
        <div class="row">
            <div class="col-12">
                {!! getPreviewFieldByTypeandId($row->type,$row->id,$row->form_id, $page_id); !!}
            </div>
        </div>
        
    @endforeach
