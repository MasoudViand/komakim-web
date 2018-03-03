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


    <form role="form" method="POST" enctype="multipart/form-data" action="{{ route('admin.dissatisfied.reason.update.submit') }}">
        {{ csrf_field() }}
        <div id="subform" class="box-body">

            <div class="form-group">
                <label for="exampleInputEmail1">دلیل عدم رضایت </label>
                <input  class="form-control" id="DissatisfiedReason" name="DissatisfiedReason" value="{{$dissatisfiedReason->reason}}">
                @if ($errors->has('DissatisfiedReason'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('DissatisfiedReason') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">اپلود ایکون </label>
                {{--<input  class="form-control" type="file" id="imageProfile" name="imageProfile" >--}}
                {!! Form::file('imageّIcon', array('class' => 'image')) !!}
                <img src="{{$dissatisfiedReason->filepath}}" style="max-width: 200px">
                @if ($errors->has('imageّIcon'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('imageّIcon') }}</strong>
                                    </span>
                @endif
            </div>

            <input  class="form-control" type="hidden" id="idDissatisfiedReason" name="idDissatisfiedReason" value="{{$dissatisfiedReason->id}}">


        </div>


        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>


@endsection