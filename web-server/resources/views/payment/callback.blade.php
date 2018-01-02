@extends('layouts.app')

@section('header')
    <link rel="stylesheet" href="{{asset("AdminLTE-RTL/bootstrap/css/bootstrap.min.css") }}">


    <link rel="stylesheet" href="{{asset("bootstrap-jalali-datepicker-master/bootstrap-datepicker.css") }}">

@endsection



@section('content')
<div class="container">

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">ثبت نام</div>
                @if($error)
                    <div class="alert alert-danger" role="alert">
                        <strong>
                            {{ $error }}
                        </strong>
                    </div>
                @endif


                <div class="panel-body">

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif


                </div>
                <div class="panel-body">


                </div>
            </div>


        </div>
    </div>
</div>
@endsection


