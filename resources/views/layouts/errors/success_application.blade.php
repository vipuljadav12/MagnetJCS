@extends('layouts.front.app')

@section('content')
        <div class="mt-20">
        <div class="card bg-light p-20">
            <div class="text-center font-20 b-600 p-50 bg-success mb-10 text-white">Your Confirmation number is<br> {{$confirmation_no}}</div>
            <div class="">
                {!! getconfig()[$msg_type] ?? '<p>Please print this pag and/or record this confirmation number for your records. If you provied an email address, you will receive an email confirmaation at the email address provided.<br><br>
                Also, remember that you must register your student  for school enrollment via the online New Student Registration at schoolsite where available.' !!}
            </div>
        </div>
    </div>

@endsection