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


    <form role="form" method="POST" action="{{ route('admin.discount_code.insert.submit') }}">
        {{ csrf_field() }}
        <div id="subform" class="box-body">



            <div class="form-group">
                <label for="exampleInputEmail1">کد </label>
                <input  class="form-control" id="discount_code" name="discount_code" placeholder="کد">
                @if ($errors->has('discount_code'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('discount_code') }}</strong>
                                    </span>
                @endif
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">نوع</label>
                <select name="type" class="form-control" style="width:350px">
                    <option value="const_amount">مقدار ثابت</option>
                    <option value="percent">درصدی</option>

                </select>

            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">مقدار </label>
                <input  class="form-control" id="value" name="value" type="number" placeholder="مقدار">
                @if ($errors->has('value'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('value') }}</strong>
                    </span>
                @endif
            </div>



        </div><!-- /.box-body -->

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">ثبت</button>
        </div>
    </form>


@endsection