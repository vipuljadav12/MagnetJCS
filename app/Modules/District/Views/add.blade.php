@extends('layouts.admin.app')
@section('title')Add District @endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Add District</div>
            <div class=""><a href="{{url('district')}}" class="btn btn-sm btn-secondary" title="">Back</a></div>
        </div>
    </div>
    <form action="{{url('district/store')}}" method="post" name="add_district" enctype= "multipart/form-data">
        {{csrf_field()}}
        <div class="card shadow">
            <div class="card-body">
                <div class="form-group">
                    <label class="control-label">District Name : </label>
                    <div class=""><input type="text" name="name" class="form-control" value="{{old('name')}}"></div>
                    @if($errors->first('name'))
                        <div class="mb-1 text-danger">
                            {{ $errors->first('name')}}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="control-label">District Logo : </label>
                    <div class="row">
                        <div class="col-12 col-md-7"><input name="logo"  type="file" class="form-control"  value="{{old('logo')}}" accept="image/*"></div>
                        <div class="col-12 col-md-5">
                            <div class=" ">
                                <img src="" alt="img" title="" width="70" id="img" class="img-thumbnail mr-3">
                            </div>
                        </div>
                    </div>
                </div>
                @if($errors->first('logo'))
                    <div class="mb-1 text-danger">
                        {{ $errors->first('logo')}}
                    </div>
                @endif
                <div class="form-group">
                    <label class="control-label">District URL : </label>
                    <div class="d-flex align-items-end"><input type="text" name="url" id="url" class="form-control"  value="{{old('url')}}">.magent.com</div>
                    @if($errors->first('url'))
                        <div class="mb-1 text-danger">
                            {{ $errors->first('url')}}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="control-label">District Address : </label>
                    <div class=""><input type="text" class="form-control"  value="{{old('address')}}" name="address"></div>
                    @if($errors->first('address'))
                        <div class="mb-1 text-danger">
                            {{ $errors->first('address')}}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="control-label">City : </label>
                    <div class=""><input type="text" class="form-control"  value="{{old('city')}}" name="city"></div>
                    @if($errors->first('city'))
                        <div class="mb-1 text-danger">
                            {{ $errors->first('city')}}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="control-label">State : </label>
                    <div class=""><input type="text" class="form-control"  value="{{old('state')}}" name="state"></div>
                    @if($errors->first('state'))
                        <div class="mb-1 text-danger">
                            {{ $errors->first('state')}}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="control-label">ZIP Code : </label>
                    <div class=""><input type="text" class="form-control"  id="zip_code" value="{{old('zip_code')}}" name="zip_code" maxlength="6"></div>
                    @if($errors->first('zip_code'))
                        <div class="mb-1 text-danger">
                            {{ $errors->first('zip_code')}}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="control-label">Phone # : </label>
                    <div class=""><input type="text" class="form-control"  value="{{old('phone')}}" name="phone" maxlength="13"></div>
                    @if($errors->first('phone'))
                        <div class="mb-1 text-danger">
                            {{ $errors->first('phone')}}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="control-label">Theme Color : </label>
                    <div class="">
                        <div class="input-group mb-3">
                            <input id="cp2" type="text" class="form-control" value="#2FB6FF" name="theme_color" maxlength="7">
                            <div class="input-group-append">
                                <span class="input-group-text"><i id="chgcolor" class="color-box" style="background-color: #2FB6FF;"></i></span>
                            </div>
                        </div>
                    </div>
                    @if($errors->first('theme_color'))
                        <div class="mb-1 text-danger">
                            {{ $errors->first('theme_color')}}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="" class="control-label">Status : </label>
                    <div class=""><input id="chk_0" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" checked name="status" /></div>
                </div>
            </div>
        </div>
        <div class="box content-header-floating" id="listFoot">
            <div class="row">
                <div class="col-lg-12 text-right hidden-xs float-right">
                    <button class="btn btn-warning btn-xs" ><i class="fa fa-save"></i> Save </button>
                    {{--                <a class="btn btn-success btn-xs" href="district.html"><i class="fa fa-save"></i> Save &amp; Exit</a> --}}
                    {{--                <a class="btn btn-danger btn-xs" href="javascript:void(0);"><i class="far fa-trash-alt"></i> Delete</a> </div>--}}
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
    <script src="{{asset('resources/assets/common/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('resources/assets/common/js/additional-methods.min.js')}}"></script>
    <script>
        $(function () {
            $("#zip_code").keypress(function (e) {
                //if the letter is not digit then display error and don't type anything
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    //display error message
                    // $("#errmsg").html("Digits Only").show().fadeOut("slow");
                    return false;
                }
            });

            $("input[name='logo']").change(function () {
                readURLimg(this);
            });
            function readURLimg(input) {
                console.log(input.files)
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#img')
                            .attr('src', e.target.result)
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
            //accessurl
            $("input[name='name']").on('input',function () {
                $("input[name='url']").val($(this).val().toLowerCase().trim().replace(/[^a-z0-9\s]/gi, '').replace(/\s{1,}/g,'-'));
            });
            $("input[name='url']").on('input',function () {
                $(this).val($(this).val().toLowerCase().trimStart().replace(/\s{1,}/g,'-').replace(/-{2,}/g,'-').replace(/[^a-z0-9-]/gi, ''));
            });

            jQuery.validator.addMethod( "mobile", function( value, element ) {
                return this.optional(element) || /^(\+\d{1,3}[- ]?)?\d{10}$/.test(value);
            }, "The Mobile number is not valid" );
            $("form[name='add_district']").validate({
                rules: {
                    name:{
                        required: true,
                        maxlength:50,
                    },
                    logo:{
                        required: true,
                        accept: "jpg,png,jpeg,gif",
                    },
                    url:{
                        required: true,
                        maxlength:100,
                        remote:{
                            url: "{{url("district/uniqueurl")}}",
                            type: "GET",
                            data: {
                                url: function () {
                                    return $( "#url" ).val();
                                }
                            },
                        }
                    },
                    address:{
                        required: true,
                        maxlength:200,
                    },
                    city:{
                        required: true,
                        maxlength:20,
                    },
                    state:{
                        required: true,
                        maxlength:20,
                    },
                    zip_code:{
                        required: true,
                        integer:true,
                        maxlength:6,
                        minlength:6,
                    },
                    phone:{
                        required: true,
                        mobile:true,
                    },
                    theme_color:{
                        required:true,
                        maxlength:7,
                    }
                },
                messages: {
                    name:{
                        required: 'The District name field is required.',
                        maxlength:'The District name may not be greater than 50 characters.'
                    },
                    logo:{
                        required: 'The District logo is required.',
                        accept:'The file must be jpg,png,jpeg,gif.'
                    },
                    url:{
                        required: 'The District url field is required.',
                        maxlength:'The District url may not be greater than 100 characters.',
                        remote:'The URL has already been taken.',
                    },
                    address:{
                        required: 'The Address field is required.',
                        maxlength:'The Address may not be greater than 200 characters.'
                    },
                    city:{
                        required: 'The city field is required.',
                        maxlength:'The city name may not be greater than 20 characters.'
                    },
                    state:{
                        required: 'The state field is required.',
                        maxlength:'The State name may not be greater than 20 characters.'
                    },
                    zip_code:{
                        required: 'The Zip code field is required.',
                        maxlength:'The zip code must be 6 digit.',
                        minlength:'The zip code must be 6 digit.',
                        integer:'The Zip code must be Integer'
                    },
                    phone:{
                        required: 'The phone field is required.',
                    },
                    theme_color:{
                        required: 'The Theme color field is required.',
                        maxlength:'The Theme color may not be greater than 7 characters.'
                    }
                },errorPlacement: function(error, element)
                {
                    error.appendTo( element.parents('.form-group'));
                    error.css('color','red');
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });

        });
    </script>
@endsection