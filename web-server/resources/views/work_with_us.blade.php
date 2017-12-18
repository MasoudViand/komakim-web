@extends('layouts.app')

@section('header')
    <link rel="stylesheet" href="{{asset("AdminLTE-RTL/bootstrap/css/bootstrap.min.css") }}">
    <link rel="stylesheet" href="{{asset("AdminLTE-RTL/bootstrap/css/bootstrap.min.css") }}">

    <link rel="stylesheet" href="{{asset("bootstrap-persian-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css") }}">

    <script src="{{asset("AdminLTE-RTL/plugins/jQuery/jQuery-2.1.4.min.js") }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.0/locale/af.js"></script>
    <script src="{{asset("bootstrap-persian-datetimepicker-master/build/js/bootstrap-persian-datetimepicker.js") }}"></script>


@endsection



@section('content')
<div class="container">
    @if(Session::has('error'))

        <div class="alert alert-danger" role="alert">
            <strong>
                {{ Session::get('error') }}
            </strong>
        </div>
    @endif
    @if(Session::has('success'))
        <div class="alert alert-success" role="alert">
            <strong>
                {{ Session::get('success') }}
            </strong>
        </div>
    @endif
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">ثبت نام</div>


                <div class="panel-body">

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif


                </div>
                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('register.worker.submit') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">نام</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('family') ? ' has-error' : '' }}">
                            <label for="family" class="col-md-4 control-label">نام خانوادگی</label>

                            <div class="col-md-6">
                                <input id="family" type="text" class="form-control" name="family" value="{{ old('family') }}" required autofocus>

                                @if ($errors->has('family'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('family') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('nationalCode') ? ' has-error' : '' }}">
                            <label for="nationalCode" class="col-md-4 control-label">کدملی</label>

                            <div class="col-md-6">
                                <input id="nationalCode" type="text" class="form-control" name="nationalCode" value="{{ old('nationalCode') }}" required autofocus>

                                @if ($errors->has('nationalCode'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nationalCode') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('phoneNumber') ? ' has-error' : '' }}">
                            <label for="phoneNumber" class="col-md-4 control-label">شماره تلفن ثابت</label>

                            <div class="col-md-6">
                                <input id="phoneNumber" type="text" class="form-control" name="phoneNumber" value="{{ old('phoneNumber') }}" required autofocus>

                                @if ($errors->has('phoneNumber'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phoneNumber') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('mobileNumber') ? ' has-error' : '' }}">
                            <label for="mobileNumber" class="col-md-4 control-label">تلفن همراه</label>

                            <div class="col-md-6">
                                <input id="mobileNumber" type="text" class="form-control" name="mobileNumber" value="{{ old('mobileNumber') }}" required autofocus>

                                @if ($errors->has('mobileNumber'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mobileNumber') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                            <label for="address" class="col-md-4 control-label">ادرس</label>

                            <div class="col-md-6">
                                <input id="address" type="text" class="form-control" name="address" value="{{ old('address') }}" required autofocus>

                                @if ($errors->has('address'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('lastEducation') ? ' has-error' : '' }}">
                            <label for="lastEducation" class="col-md-4 control-label">اخرین مدرک تحصیلی</label>

                            <div class="col-md-6">
                                <input id="lastEducation" type="text" class="form-control" name="lastEducation" value="{{ old('lastEducation') }}" >

                                @if ($errors->has('lastEducation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lastEducation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('birthday') ? ' has-error' : '' }}">
                            <label for="birthday" class="col-md-4 control-label">تاریخ تولد</label>

                            <div class="col-md-6">
                                <input id="birthday" type="text" class="form-control" name="birthday" value="{{ old('birthday') }}" required autofocus>

                                @if ($errors->has('birthday'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('birthday') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('field') ? ' has-error' : '' }}">
                            <label for="birthDay" class="col-md-4 control-label">حوزه های همکاری</label>

                            <div class="col-md-6">
                                <select class="form-control" id="field" name="field">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                </select>
                                @if ($errors->has('field'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('field') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('marriageStatus') ? ' has-error' : '' }}">
                            <label for="birthDay" class="col-md-4 control-label">وضعیت تاهل</label>
                            <div class="col-md-6">
                                <label for="radio1"><span><span></span></span>متاهل</label>
                                <input checked="checked" id="marriageStatus" name="marriageStatus" type="radio" value="married">
                                <label for="radio2"><span><span></span></span>مجرد</label>
                                <input id=marriageStatus name="marriageStatus" type="radio" value="single">

                            @if ($errors->has('marriageStatus'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('marriageStatus') }}</strong>
                                    </span>
                                @endif
                            </div>




                        </div>
                        <div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">
                            <label for="birthDay" class="col-md-4 control-label">جنسیت</label>
                            <div class="col-md-6">
                                <label for="radio1"><span><span></span></span>مرد</label>
                                <input checked="checked" id="gender" name="gender" type="radio" value="male">
                                <label for="radio2"><span><span></span></span>زن</label>
                                <input id="gender" name="gender" type="radio" value="female">


                                @if ($errors->has('gender'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('gender') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">ایمیل</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" >


                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('anotherCapability') ? ' has-error' : '' }}">
                            <label for="anotherCapability" class="col-md-4 control-label">سایر توانایی ها</label>

                            <div class="col-md-6">
                                <textarea class="form-control" rows="5" id="anotherCapability" name="anotherCapability">{{ old('anotherCapability') }}</textarea>
                            @if ($errors->has('anotherCapability'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('anotherCapability') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('certificates') ? ' has-error' : '' }}">
                            <label for="certificates" class="col-md-4 control-label">دوره های گذرانده شده</label>

                            <div class="col-md-6">
                                <textarea class="form-control" rows="5" id="certificates" name="certificates">{{ old('certificates') }}</textarea>
                                @if ($errors->has('certificates'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('certificates') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('experience') ? ' has-error' : '' }}">
                            <label for="certificates" class="col-md-4 control-label">سوابق کاری</label>

                            <div class="col-md-6">
                                <textarea class="form-control" rows="5" id="experience" name="experience">{{ old('experience') }}</textarea>
                                @if ($errors->has('experience'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('experience') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


        </div>
    </div>
</div>
@endsection
