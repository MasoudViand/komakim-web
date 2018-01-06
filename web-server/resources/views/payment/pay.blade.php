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
                <div class="panel-heading">انتقال به صحفه بانک</div>


                <div class="panel-body">

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif


                </div>
                <div class="panel-body">


                    <form action='https://sep.shaparak.ir/Payment.aspx' method='POST'>
                        <input type='hidden' id='Amount' name='' value={{$amount}}> <!-- مبلغ -->
                        <input type='hidden' id='MID' name='MID' value='23e'> <!-- شماره مشتری بانک سامان -->
                        <input type='hidden' id='ResNum' name='ResNum' value={{$order_id}}> <!-- شماره فاکتور -->
                        <input type='hidden' id='RedirectURL' name='RedirectURL' value={{URL::to('/').'pay/callback'}}> <!-- آدرس بازگشت -->
                        <input type=submit value='pay'>
                    </form>


                </div>
            </div>


        </div>
    </div>
</div>
@endsection


