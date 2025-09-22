@extends('Offers::app')

@section('content')
    <div class="container">
        <div class="mt-20">
            <form method="post" action="{{url('/Offers/Contract/Option/store')}}">
            {{csrf_field()}}
            <input type="hidden" name="submission_id" value="{{$submission->submission_id}}">
            <input type="hidden" name="program_id" value="{{$program_id}}">

            <div class="card bg-light p-20 pt-lg-50 pb-lg-150 pl-lg-100 pr-lg-100">
                {!! $msg !!}
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-4 mb-10"><button type="submit" name="online_contract_now" value="{{$program_id}}" class="h-100 pt-20 pb-20 d-flex align-items-center justify-content-center btn-block text-center btn btn-success">I would like to review and sign the MCPSS Magnet Schools Parent Contract Now</button></div>
                    <div class="col-12 col-lg-4 mb-10"><button type="submit" name="online_contract_later" value="{{$program_id}}" class="h-100 pt-20 pb-20 d-flex align-items-center justify-content-center btn-block text-center btn btn-danger">I prefer to review and sign the MCPSS Magnet Schools Parent Contract Later</button></div>
                </div>
            </div>
            </form>
        </div>
    </div>
@endsection

