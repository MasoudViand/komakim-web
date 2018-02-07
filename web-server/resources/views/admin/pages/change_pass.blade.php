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


    <form role="form" method="POST" enctype="multipart/form-data" action="{{ route('admin.change.pass.submit') }}">
        {{ csrf_field() }}
        <div id="subform" class="box-body">


            <div class="form-group{{ $errors->has('currentPassword') ? ' has-error' : '' }}">
                <label for="exampleInputPassword1">گذر واژه فعلی</label>
                <input id="currentPassword" type="password" class="form-control" name="currentPassword" required>
                @if ($errors->has('currentPassword'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('currentPassword') }}</strong>
                                    </span>
                @endif

            </div>



            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="exampleInputPassword1">گذر واژه</label>
                <input id="password" type="password" class="form-control" name="password" required>
                @if ($errors->has('password'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                @endif

            </div>
            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <label for="exampleInputPassword1">تایید گذر واژه</label>
                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                @if ($errors->has('password_confirmation'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                @endif

            </div>




        </div><!-- /.box-body -->

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">تغییر</button>
        </div>
    </form>


@endsection