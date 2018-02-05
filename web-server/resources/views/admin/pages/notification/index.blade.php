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


    <form role="form" method="POST" action="{{ route('admin.notification.send.submit') }}">
        {{ csrf_field() }}
        <div id="subform" class="box-body">



            <div class="form-group">
                <label for="exampleInputPassword1">نوع کاربر</label>
                <select name="type" class="form-control" style="width:350px">
                    <option value="client">مشتری</option>
                    <option value="worker">خدمه</option>

                </select>

            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">عنوان </label>
                <input  class="form-control" id="title" name="title" type="text" placeholder="عنوان">
                @if ($errors->has('value'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">محتوا </label>
                <textarea class="form-control" name="content" rows="5" id="content"></textarea>

            </div>



        </div><!-- /.box-body -->

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">ثبت</button>
        </div>
    </form>


@endsection