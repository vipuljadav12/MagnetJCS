@extends('layouts.front.app')

@section('content')
  <div class="mt-20">
    <div class="card bg-light p-20">
      <div class="row">
          <div class="col-sm-12 col-xs-12 font-20 b-600 text-danger text-center">Preview</div>
    </div>
      <div class="row">
          <div class="col-sm-6 col-xs-12 font-16 b-600">Student Name: <span> Ansley Visone</span></div>
          <div class="col-sm-6 text-right font-16 col-xs-12 b-600">Student's Submission ID: <span> AGT-0001-0001 </span></div>
      </div>
      <div class="row">
          <div class="col-sm-6 col-xs-12 font-16 b-600 mt-10">Program Name: <span>{{getProgramName($data['program_id'])}}</span></div>
          <div class="col-sm-6 text-right font-16 col-xs-12 b-600 mt-10">Next Grade: <span> 6 </span></div>
      </div>
    </div>
  </div>
  <form class="p-20 border mt-20 mb-20">
    <div class="row pt-20">
        <div class="col-12">
        <p>{!! $data['intro_txt'] or '' !!}</p>
      </div>
      </div>
    <div class="box-0">

      @if(!empty($data['wp_question']))
        @foreach($data['wp_question'] as $key => $wp)
          <div class="form-group row">
            <label class="control-label col-12 col-md-12 col-xl-12 b-600" for="qry01">{{$wp}} : </label>
            <div class="col-12 col-md-12 col-xl-12">
              <textarea class="form-control" name="writing_sample[{{$key}}]" rows="7" id="qry01"></textarea>
            </div>
          </div>
        @endforeach
      @endif
      
      <div class="form-group row">
        <div class="col-12 col-md-12 col-xl-12"> <button type="button" onclick="alert('This functionality will not work in preview mode.')" class="btn btn-secondary btn-xxl" title="" style="height: 55px; width: 140px;">Submit</a> </div>
      </div>
    </div>
  </form>
@endsection

@section('scripts')
<script type="text/javascript">
  
</script>
@endsection