<header class="" dataheader-bg="theme01" datanavbar="sticky">
    <div class="row align-items-center justify-content-between h-100">
        <div class="col-auto ml-30">
                        <div class="w-200"><img src="{{url('/resources/assets/admin/images/login.png')}}"></div>
                    </div>
        <div class="col-auto flex-grow-1">

            <nav class="navbar-custom d-flex align-items-center justify-content-end">
                <ul class="list-inline mb-0 d-flex align-items-center">
                    <li class="list-inline-item p-0">
                        {!! get_enrollment_combo() !!}
                    </li>
                    <li class="list-inline-item p-0">
                        {!! get_district_combo() !!}
                    </li>                                
                    <li class="list-inline-item dropdown notification-list"> <a class="nav-link dropdown-toggle nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <img src="{{Auth::check()&&Auth::user()->profile!=''?url('resources/assets/admin/images/').'/'.Auth::user()->profile:url('resources/assets/admin/images/user.png')}}" alt="{{ Auth::user()->username ?? ""}}" class="rounded-circle"> </a>
                        <div class="dropdown-menu dropdown-menu-right profile-dropdown" aria-labelledby="Preview">
                            <div class="dropdown-item noti-title">
                                <h5 class="text-overflow">Welcome ! {{Auth::check()?Auth::user()->username:''}}</h5>
                            </div>
                            <a href="javascript:void(0);" class="dropdown-item notify-item" data-toggle="modal" data-target="#userprofile"> <i class="pe-7s-user"></i> <span>Profile</span></a> 
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="dropdown-item notify-item"> <i class="pe-7s-power"></i> <span>Logout</span> </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>
<!-- User Profile Modal -->
@if(Auth::check())
    <div class="modal fade" id="userprofile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Profile</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- <form class="" id="change_user_detail" action="{{url('admin/Users/updateprofile/'.Auth::user()->id)}}" method="post" enctype= "multipart/form-data"> --}}
            <form class="" id="update_profile" action="{{url('admin/Users/updateprofile/'.Auth::user()->id)}}" method="post" enctype= "multipart/form-data">
                {{csrf_field()}}
                <input type="hidden" name="id" id="id" value="{{Auth::user()->id}}">
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="" class="control-label col-lg-4">First Name : </label>
                        <div class="col-lg-8 contain_input">
                            <input type="text" class="form-control" value="{{Auth::user()->first_name}}" name="first_name">
                            @if($errors->first('first_name'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('first_name')}}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="control-label col-lg-4">Last Name : </label>
                        <div class="col-lg-8 contain_input">
                            <input type="text" class="form-control" value="{{Auth::user()->last_name}}" name="last_name">
                            @if($errors->first('last_name'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('last_name')}}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="control-label col-lg-4">User Name : </label>
                        <div class="col-lg-8 contain_input">
                            <input type="text" class="form-control" value="{{Auth::user()->username}}" name="username">
                            @if($errors->first('username'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('username')}}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="control-label col-lg-4">Email : </label>
                        <div class="col-lg-8">
                            <input type="text" disabled class="form-control" value="{{Auth::user()->email}}" name="email">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="control-label col-lg-4">Profile : </label>
                        <div class="col-lg-6 contain_input">
                            <input type="file" class="form-control" name="profile" value="{{old('profile')}}" accept="image/*">
                            @if($errors->first('profile'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('profile')}}
                                </div>
                            @endif
                        </div>
                        <div class="co-lg-2">
                            <img src="{{Auth::user()->profile!=''?url('resources/assets/admin/images/').'/'.Auth::user()->profile:url('resources/assets/admin/images/user.png')}}" alt="Logo" title="" width="60" id="profile_logo"  class="rounded-circle">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="control-label col-lg-4">Change Password : </label>
                        <div class="col-lg-8">
                            <input  type="checkbox" data-plugin="1switchery"  class=" js-switch1 js-switch-11 js-switch-xs1 1change_pass_btn" data-size="Small"  id="changePassword" checked="false" />
                        </div>
                    </div>
                    <div class="d-none change_pass">
                        <div class="form-group row">
                            <label for="" class="control-label col-lg-4">Old Password : </label>
                            <div class="col-lg-8 contain_input">
                                <input type="password" class="form-control" value="{{old("old_password")}}" name="old_password">
                                @if($errors->first('old_password'))
                                    <div class="mb-1 text-danger">
                                        {{$errors->first('old_password')}}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="control-label col-lg-4">New Password : </label>
                            <div class="col-lg-8 contain_input">
                                <input type="password" class="form-control" value="{{old("password")}}" id="password" name="password">
                                @if($errors->first('password'))
                                    <div class="mb-1 text-danger">
                                        {{ $errors->first('password')}}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="control-label col-lg-4">Confirm Password : </label>
                            <div class="col-lg-8 contain_input">
                                <input type="password" class="form-control" value="{{old("password_confirmation")}}" name="password_confirmation">
                            </div>
                        </div>
                    </div>
                    </div>
                <div class="modal-footer ">
                    <button class="btn btn-secondary">Save</button>
                    <button class="btn btn-primary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
