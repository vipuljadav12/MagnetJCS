@extends('layouts.front.app')

@section('content')
        <div class="mt-20">
        <div class="card bg-light p-20">
            <div class="text-center font-20 b-600 mb-10">{!! getconfig()['no_student_msg_title'] ?? '' !!}</div>
            <div class="">
                {!! getconfig()['no_student_msg'] ?? '' !!}
            </div>
        </div>
    </div>

@endsection