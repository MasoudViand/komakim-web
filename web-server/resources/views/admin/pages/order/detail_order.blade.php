@extends('admin.template.admin_template')

@section('content')
    {{--<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>


            <div class="container-fluid">
                <div class="col-sm-12">
                    <div class="box box-danger">
                        <table class="table table-bordered table-striped dataTable" role="grid" >
                            <thead>
                            <tr role="row">
                                <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">ادرس</th>
                                <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">تاریخ در خواست</th>
                                <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 45px;">کد رهگیری</th>
                                <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">قیمت کل</th>
                                <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 207px;">وضعیت سفارش</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr role="row" class="odd">
                                <td class="sorting_1">{{$order['address']['plain_text']  }}</td>
                                <td class="sorting_1">{{$order['created_at']}}</td>
                                <td class="sorting_1">{{$order['tracking_number']}}</td>
                                <td class="sorting_1">{{$order['total_price']}}</td>
                                <td class="sorting_1">{{$order['status']}} @if(key_exists('cancel_reason',$order))<hr>دلیل:<div class="col-sm-6">{{$order['cancel_reason']}} </div>@endif</td>

                            </tr>
                            </tbody>

                        </table>
                    </div>


                </div><!-- /.col (LEFT) -->



                <div class="col-md-12">
                    <div class="box box-danger">
                        <table class="table table-bordered table-striped dataTable" role="grid" >
                            <thead>
                            <tr role="row">
                                <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">نام و نام خانوادگی خدمه</th>
                                <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">شماره همراه خدمه</th>
                                <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">نام و نام خانوادگی مشتری</th>
                                <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">شماره مشتری</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr role="row" class="odd">
                                <td class="sorting_1">{{ array_key_exists('worker',$order) ?$order['worker']['name'].'  '.$order['worker']['family']:'هنور هیچ خدمه ای تایید نکرده است' }}</td>
                                <td class="sorting_1">{{array_key_exists('worker',$order) ?$order['worker']['phone_number']:'هنور هیچ خدمه ای تایید نکرده است'}}</td>
                                <td class="sorting_1">{{$order['user']['name'].'  '.$order['user']['family']  }}</td>
                                <td class="sorting_1">{{$order['user']['phone_number']}}</td>

                            </tr>
                            </tbody>

                        </table>

                    </div>


                </div>


                <div class="col-md-12">

                    @foreach($order['services'] as $service)
                    <div class="col-sm-6">
                        <div class="box box-danger">
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

                            @if(count($service['questions'])>0)

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


                </div>






                <div class="row">
                    <div class="col-sm-6">
                        <div class="box">
                            <h5 class="box-title"> نظر سنجی از کاربران</h5>




                                <table class="table table-bordered table-striped dataTable" role="grid" >
                                    <thead>
                                    <tr role="row">
                                        <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">امتیاز</th>
                                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">توضیحات</th>
                                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">دلایل</th>
                                    </tr>
                                    </thead>
                                    @if($review)

                                    <tbody>
                                    <tr role="row" class="odd">
                                        <td class="sorting_1">{{$review['score']  }}</td>
                                        <td class="sorting_1">{{$review['desc']  }}</td>
                                        <td class="sorting_1">
                                            @foreach($review['reasons'] as $item)
                                                {{$item}}{{" ُ"}}
                                            @endforeach

                                        </td>
                                        {{--<td class="sorting_1"><select name="field" id="field" class="form-control" style="">--}}
                                        {{--@foreach($review['reasons'] as $item)--}}
                                        {{--<option value="1">{{$item}}</option>--}}
                                        {{--@endforeach--}}

                                        {{--</select></td>--}}

                                    </tr>
                                    </tbody>
                                    @endif

                                </table>




                        </div>

                    </div>

                    <div class="col-sm-6">
                        <div class="box">
                            <h5 class="box-title">تغییرات سفارش</h5>
                            <table class="table table-bordered table-striped dataTable" role="grid" >
                                <thead>
                                <tr role="row">
                                    <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">وضعیت</th>
                                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">زمان</th>
                                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">توسط</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($revisions as $revision )
                                    <tr role="row" class="odd">
                                        <td class="sorting_1">{{$revision['status']  }}</td>
                                        <td class="sorting_1">{{$revision['created_at']  }}</td>
                                        <td class="sorting_1">{{$revision['whom']['name'].'  '.$revision['whom']['family']  }}</td>



                                    </tr>

                                @endforeach


                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>


                <div class="row" id="cancel_order_section">
                    <div class="col-sm-12">
                        <div <?php  if($order['status']=='لغو توسط ادمین'){ echo 'hidden';}   ?> >
                            <button  hidden class="btn btn-primary btn-danger" id="cancel_order">لغو سفارش</button>

                        </div>
                    </div>
                </div>


                <div class="row" id="cancel_orderform_section_" hidden>
                    <form action="{{route('admin.order.cancel')}}" method="post">
                        {{ csrf_field() }}                <div class="col-sm-6">
                            <label> دلیل لغو</label>
                            <button  class="btn btn-primary"  id="cancel_order">ثبت</button>
                            <input  class="form-control" type="hidden" id="idOrder" name="idOrder" value="{{$order['id']}}">

                        </div>
                        <div class="col-sm-6">
                            <input  type="text" name="cancel_order_text" id="cancel_order_text">
                        </div>


                    </form>

                </div>




            </div>









    <script>
        $(document).ready(function() {

            $( "#cancel_order" ).click(function () {

                $("#cancel_order_section").hide()
                $("#cancel_orderform_section_").show()

            })

        });
    </script>




@endsection