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


    <form role="form" method="POST" enctype="multipart/form-data" action="{{ route('admin.category.insert.submit') }}">
        {{ csrf_field() }}
        <div id="subform" class="box-body">



            <div class="form-group">
                <label for="exampleInputEmail1">نام دسته بندی </label>
                <input  class="form-control" id="nameCategory" name="nameCategory" placeholder="نام دسته بندی ">
                @if ($errors->has('nameCategory'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('nameCategory') }}</strong>
                                    </span>
                @endif
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">وضعیت</label>
                <select name="statusCategory" class="form-control" style="width:350px">
                    <option value="true">فعال</option>
                    <option value="false">غیر فعال</option>

                </select>                @if ($errors->has('statusCategory'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('statusCategory') }}</strong>
                                    </span>
                @endif

            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">الویت نمایش</label>
                <input type="" class="form-control" name="orderCategory" id="orderCategory" value="0" >
                @if ($errors->has('orderCategory'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('orderCategory') }}</strong>
                                    </span>
                @endif

            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">اپلود ایکون </label>
                {{--<input  class="form-control" type="file" id="imageProfile" name="imageProfile" >--}}
                {!! Form::file('imageّIcon', array('class' => 'image')) !!}
                @if ($errors->has('imageّIcon'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('imageّIcon') }}</strong>
                                    </span>
                @endif
            </div>


        </div><!-- /.box-body -->

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">ثبت</button>
        </div>
    </form>


@endsection