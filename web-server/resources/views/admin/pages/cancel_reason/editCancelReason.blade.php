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

<div class="box col-xs-6">
    <form role="form" method="POST" action="{{ route('admin.cancel.reason.update.submit') }}">
        {{ csrf_field() }}
        <div id="subform" class="box-body">

            <div class="form-group">
                <label for="exampleInputEmail1">دلیل لغو سفارش </label>
                <input  class="form-control" id="CancelReason" name="CancelReason" value="{{$cancelReason->reason}}">
                @if ($errors->has('CancelReason'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('CancelReason') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">وضعیت</label>
                <select name="type" class="form-control" style="width:350px">
                    <option value="{{\App\User::WORKER_ROLE}}">خدمه</option>
                    <option value="{{\App\User::CLIENT_ROLE}}">مشتری</option>


                </select>                @if ($errors->has('type'))
                    <span class="help-block">
                        <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                @endif

            </div>

            <input  class="form-control" type="hidden" id="idCancelReason" name="idCancelReason" value="{{$cancelReason->id}}">


        </div>

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">ثبت</button>
        </div>
    </form>
</div>
<div class="clearfix"></div>

@endsection