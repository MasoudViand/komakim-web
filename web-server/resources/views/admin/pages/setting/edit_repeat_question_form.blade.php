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


    <form role="form" method="POST" enctype="multipart/form-data" action="{{ route('admin.repeat.question.update.submit') }}">
        {{ csrf_field() }}
        <div id="subform" class="box-body">



            <div class="form-group">
                <label for="exampleInputEmail1">سوال </label>
                <input  class="form-control" id="question" name="question"  value="{{$repeatQuestion->question}}">
                @if ($errors->has('question'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('question') }}</strong>
                                    </span>
                @endif
            </div>

            <input name="id" class="form-control" type="hidden"  value="{{$repeatQuestion->id}}">

            <div class="form-group">
                <label for="exampleInputEmail1">جواب</label>
                <input  class="form-control" id="answer" name="answer" value="{{$repeatQuestion->answer}}">
                @if ($errors->has('answer'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('answer') }}</strong>
                                    </span>
                @endif
            </div>





        </div><!-- /.box-body -->

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">ثبت</button>
        </div>
    </form>


@endsection