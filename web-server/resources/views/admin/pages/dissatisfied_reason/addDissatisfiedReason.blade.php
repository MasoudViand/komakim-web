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


    <form role="form" method="POST" action="{{ route('admin.dissatisfied.reason.insert.submit') }}">
        {{ csrf_field() }}
        <div id="subform" class="box-body">



            <div class="form-group">
                <label for="exampleInputEmail1">دلیل عدم رضایت </label>
                <input  class="form-control" id="DissatisfiedReason" name="DissatisfiedReason" placeholder="نام دسته بندی ">
                @if ($errors->has('DissatisfiedReason'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('DissatisfiedReason') }}</strong>
                                    </span>
                @endif
            </div>


        </div><!-- /.box-body -->

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>


@endsection