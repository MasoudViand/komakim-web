@extends('admin.template.admin_template')

@section('content')
    {{--<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>


            <div class="container-fluid">






                <div class="col-md-12">

                    @foreach($revisions as $revision)

                        <div class="box box-danger">
                            <table class="table table-bordered table-striped dataTable" role="grid" >
                                <thead>
                                <tr role="row">
                                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">تاریخ در خواست</th>
                                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 45px;">کد رهگیری</th>
                                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">قیمت کل</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr role="row" class="odd">
                                    <td class="sorting_1">{{$revision['created_at']  }}</td>
                                    <td class="sorting_1">{{$revision['tracking_number']}}</td>
                                    <td class="sorting_1">{{$revision['total_price']}}</td>

                                </tr>
                                </tbody>

                            </table>

                            @foreach($revision['services'] as $service)
                                <div class="col-sm-6">
                                    <div class="box ">
                                        <div class="box box-title">جزییات سفارش</div>


                                        <table class="table table-bordered table-striped dataTable" role="grid" >
                                            <thead>
                                            <tr role="row">
                                                <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">نام سرویس</th>
                                                <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">قیمت پایه</th>
                                                <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">تعداد سفارش </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr role="row" class="odd">
                                                <td class="sorting_1"> {{$service['entity']['name'] }}</td>
                                                <td class="sorting_1">{{$service['entity']['price']}}</td>
                                                <td class="sorting_1">{{$service['unit_count'] }}</td>

                                            </tr>
                                            </tbody>
                                        </table>

                                        <br>
                                        <table class="table table-bordered table-striped dataTable" role="grid" >
                                            <thead>
                                            <tr role="row">
                                                <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">توضیحات</th>
                                                <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">مجموع قیمت</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr role="row" class="odd">
                                                <td class="sorting_1">  {{$service['description'] }}</td>
                                                <td class="sorting_1"> {{$service['price']}}</td>

                                            </tr>
                                            </tbody>
                                        </table>

                                        @if(key_exists('question',$service)and count($service['questions'])>0)

                                            <hr>


                                            <h5 class="box-title">سوالات سرویس</h5>
                                            <div class="divider" style="margin-top: 23px"></div>
                                            <table class="table table-bordered table-striped dataTable" role="grid" >
                                                <thead>
                                                <tr role="row">
                                                    <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">سوال</th>
                                                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">جواب</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @foreach( $service['questions'] as $question)
                                                    {{--<tr role="row" class="odd">--}}
                                                    <tr>
                                                        <td class="sorting_1">{{$question['text']}}</td>
                                                        <td class="sorting_1">{{$question['answer']?'اری ':'خیر'}}</td>
                                                    </tr>

                                                @endforeach

                                                {{--</tr>--}}
                                                </tbody>
                                            </table>

                                        @endif
                                    </div>

                                </div>

                            @endforeach
                            <div class="clearfix"></div>
                        </div>


                    @endforeach




                </div>


            </div>














@endsection