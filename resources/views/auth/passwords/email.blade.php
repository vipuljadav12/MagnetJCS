@extends('auth.app')
@section('content')
<div class="row align-items-center full-height">
    <div class="col-12 col-xl-5 mx-auto m-w-600">
        <div class="bg-white rounded pt-50 pb-50 pt-lg-30">
            <div class="logo-wrapper text-center mb-15 mb-lg-30 mt-10 mt-lg-5 px-15 px-md-30">
                <h2 class="text-uppercase text-center"><a href="" class="text-success"> <span><img src="{{url('/')}}/resources/assets/admin/images/login.png" alt=""></span></a> </h2>
            </div>
            <hr>
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-md-offset-2">
                        <div class="panel panel-default">
                            <div class="panel-heading font-weight-bold" align="center">Reset Password</div>
                            <div class="panel-body">
                                @if (session('status'))
                                    <div class="alert alert-success mt-2">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
                                    {{ csrf_field() }}
                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <div class="col-md-12 mt-2">
                                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Please Enter Your E-Mail Address" maxlength="255" required>
                                            @if ($errors->has('email'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group" align="center">
                                        <div class="col-md-6 col-md-offset-4">
                                            <button type="submit" class="btn btn-primary">
                                                Send Password Reset Link
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end card-box--> 
        </div>
        <!-- end wrapper --> 
    </div>
</div>
@endsection
