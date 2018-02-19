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



                    <div class="alert alert-danger" role="alert">
                        <strong>
                            {{ $error or '' }}
                        </strong>
                    </div>



                <div class="panel-body">


                        <div class="alert alert-success"@if(empty($seccess)) hidden @endif>
                            {{ $success or '' }}
                        </div>




                </div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-sm-12">
                            <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                                <thead>
                                <tr role="row">
                                    <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">شماره پیگیری</th>
                                </tr>
                                </thead>
                                <tbody>




                                    <tr role="row" class="odd">
                                        <td class="sorting_1">{{$ref_num or ''}}</td>

                                    </tr>




                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
            </div>


        </div>
    </div>
</div>
@endsection


