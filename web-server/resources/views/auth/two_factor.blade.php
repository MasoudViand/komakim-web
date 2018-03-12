@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">ورود ادمین</div>

                <div class="panel-body">
                    <form role="form" method="POST" action="{{route('2fa')}}">
                        {{ csrf_field() }}
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <input id="2fa" type="text" class="form-control" name="2fa" placeholder="Enter the code you received here." required autofocus>
                            @if ($errors->has('2fa'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('2fa') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
