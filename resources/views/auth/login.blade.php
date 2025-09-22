@extends('auth.app')
@section("title"," Login | ".env("APP_NAME"))
@section('content')
<div class="row align-items-center full-height">
    <div class="col-12 col-xl-5 mx-auto m-w-600">
        <div class="bg-white rounded pt-50 pb-50 pt-lg-30">
            <div class="logo-wrapper text-center mb-15 mb-lg-30 mt-10 mt-lg-5 px-15 px-md-30">
                <h2 class="text-uppercase text-center"><a href="" class="text-success"> <span><img src="{{url('/')}}/resources/assets/admin/images/login.png" alt=""></span></a> </h2>
            </div>
            <hr>
            <div class="p-30 px-md-80">
                @if(Session::has("mess"))
                    <div class="alert alert-danger"> {{Session::get("mess")}}</div>
                @endif
                <form class="form-horizontal" method="POST" id="loginform"  action="{{ route('login') }}">
                    {{ csrf_field() }}
                    <div class="form-group position-relative pl-50">
                        <label for="" class="font-18 {{ $errors->has('email') ? ' has-error' : '' }}">Username</label>
                        <input class="form-control" type="text"  name="email" id="emailaddress" required="" placeholder="" value="{{old('email')}}" maxlength="255" autofocus>
                        <div class="pre-icon"> <i class="far fa-user-circle"></i></div>
                        @if ($errors->has('email'))
                            <span class="error">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group position-relative pl-50">
                        <label for="password" class="font-18">Password</label>
                        <input class="form-control" type="password" required="" name="password" id="password" placeholder=""  autocomplete="off" maxlength="255">
                        <div class="pre-icon fingerprint"><i class="fas fa-fingerprint"></i></div>
                        @if ($errors->has('password'))
                            <span class="error">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="buttons-w">
                        <div class="form-group position-relative text-right mb-0 pt-10">
                            <button type="submit" class="btn-secondary btn-lg w-120 font-18 d-inline-block text-center rounded-0">Login</button> 
                            <div class="form-check-inline"> 
                                <a href="{{url('')}}/password/reset" title="">Reset Password</a>
                                {{-- <a href="http://10.0.10.52/webProjects/UWProjects/password/reset" title="">Reset Password</a> --}}
                            </div> 
                        </div>
                    </div>
                </form>
            </div>
            <!-- end card-box--> 
        </div>
        <!-- end wrapper --> 
    </div>
</div>
@endsection
@section('content1')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="error">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

