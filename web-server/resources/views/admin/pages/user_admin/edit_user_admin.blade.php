@extends('admin.template.admin_template')

@section('content')
    {{--<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>




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


    <form role="form" method="POST" enctype="multipart/form-data" action="{{ route('admin.user_admin.update.submit') }}">
        {{ csrf_field() }}
        <div id="subform" class="box-body">



            <div class="form-group">
                <label for="exampleInputEmail1">نام کاربر </label>
                <input  class="form-control" id="username" name="username" value="{{$userAdmin->name}}">
                @if ($errors->has('username'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                @endif
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">سطح دسترسی</label>
                <select name="role" class="form-control" style="width:350px">
                    <option value="{{$userAdmin->role}}">{{$userAdmin->role=='admin'?'مدیرکل':($userAdmin->role=='operator'?"اپراتور":'مالی')}}</option>
                    <option value="operator">اپراتور</option>
                    <option value="financial">مالی</option>
                    <option value="admin">مدیر کل</option>

                </select>

            </div>
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="exampleInputPassword1">ایمیل</label>
                <input type="email" class="form-control" name="email" id="email" value="{{$userAdmin->email}}" required>
                @if ($errors->has('email'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                @endif

            </div>
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="exampleInputPassword1">گذر واژه</label>
                <input id="password" type="password" class="form-control" name="password" >
                @if ($errors->has('password'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                @endif

            </div>
            <input type="hidden"value="{{$userAdmin->id}}" name="id">
            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <label for="exampleInputPassword1">تایید گذر واژه</label>
                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" >
                @if ($errors->has('password_confirmation'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                @endif

            </div>




        </div><!-- /.box-body -->

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">ثبت</button>
        </div>
    </form>


@endsection