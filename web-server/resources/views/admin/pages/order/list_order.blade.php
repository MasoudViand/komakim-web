@extends('admin.template.admin_template')

@section('content')
    {{--<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <div class="box-body">

        @if(Session::has('success'))
            <div class="alert alert-sussecc" role="alert">
                <strong>
                    {{ Session::get('success') }}
                </strong>
            </div>
        @endif
            @if(Session::has('error'))
                <div class="alert alert-sussecc" role="alert">
                    <strong>
                        {{ Session::get('error') }}
                    </strong>
                </div>
            @endif
        <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">


                <div class="col-sm-4">
                    <div id="example1_filter" class="dataTables_filter">
                        <label>کدپیگیری<input type="number"id="tracking_number"   class="form-control input-sm" value="{{key_exists('tracking_number',$queryParam)?$queryParam['tracking_number']:''}}" placeholder="" aria-controls="example1">
                        </label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div id="example1_filter" class="dataTables_filter">

                            <select id ="status" name="status" class="form-control" style="">
                                <option value="{{key_exists('status',$queryParam) ? $queryParam['status']:''}}">{{key_exists('status',$queryParam) ? $queryParam['status_plain_text']:'--- انتخاب وضعیت--'}}</option>

                                    <option value="{{\App\OrderStatusRevision::WAITING_FOR_WORKER_STATUS}}">منتظر تایید خدمه</option>
                                    <option value="{{\App\OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS}}">قبول شده توسط خدمه</option>
                                    <option value="{{\App\OrderStatusRevision::START_ORDER_BY_WORKER_STATUS}}">شروع کار توسط خدمه</option>
                                    <option value="{{\App\OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS}}">اتمام توسط خدمه</option>
                                    <option value="{{\App\OrderStatusRevision::PAID_ORDER_BY_CLIENT_STATUS}}">پرداخت شده توسط خدمه</option>
                                    <option value="{{\App\OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS}}">لغو توسط خدمه</option>
                                    <option value="{{\App\OrderStatusRevision::CANCEL_ORDER_BY_CLIENT_STATUS}}">لغو توسط مشتری</option>
                                    <option value="{{\App\OrderStatusRevision::CANCEL_ORDER_BY_ADMIN_STATUS}}">لغو توسط ادمین</option>

                            </select>
                        </label>
                    </div>
                </div>

                <div class="col-sm-4">

                    <div id="example1_filter" class="dataTables_filter">

                        <select name="typeOfuser" id="field" onchange="onhideWorker()" class="form-control" style="">

                            <option value="{{key_exists('category_id',$queryParam)?$queryParam['category_id']:''}}">{{key_exists('category_id',$queryParam)?\App\Category::find($queryParam['category_id'])->name:'انتخاب زمینه همکاری'}}</option>
                            @foreach($categories as $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach



                        </select>
                        </label>
                    </div>
                </div>

            </div>

        </div>




            </div>

            <button id="search_filter" class="btn btn-primary" > جست جو</button>
			<br><br>
            <div class="row">
                <div class="col-sm-12">
                    <table id="user_table" class="box table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                        <thead>
                        <tr role="row">
                            <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">نام و نام خانوادگی</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">تاریخ در خواست</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">زمان ثبت سفارش</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">وضعیت درخواست	</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">جزییات سفارش</th>
                        </tr>
                        </thead>
                        <tbody id="tuserbody">
                        @foreach($orders as $order)

                            <tr role="row" class="odd">
                                <td class="sorting_1">{{$order['user']->name.'  '.$order['user']->family}}</td>
                                <td class="sorting_1">{{$order['created_at']}}</td>
                                <td class="sorting_1">{{$order['created_at_hour']}}</td>
                                <td class="sorting_1">{{$order['status']}}</td>
                                <td><a href="{{route('admin.order.detail',['order_id' => $order['order_id']])}}"><i class="fa fa-edit"></i></a></td>

                            </tr>

                        @endforeach


                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-5">
                    <div class="dataTables_info" id="example1_info" role="status" aria-live="polite">نشان دادن {{ count($orders)>0 ?((($page-1)*10)+1):0 }} تا {{((($page-1)*10))+count($orders)}}از{{$count}}</div>

                </div>
                <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example1_paginate">
                        <ul class="pagination">

                            @if($count>count($orders))

                                @if($page>8)
                                    <li class="paginate_button active"><a href="{{route("admin.order.list",array_merge($queryParam,['page'=>1]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">1</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.order.list",array_merge($queryParam,['page'=>2]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">2</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.order.list",array_merge($queryParam,['page'=>3]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">3</a></li>
                                    <li class="paginate_button active"><a href="#" aria-controls="example1" data-dt-idx="1" tabindex="0">.</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.order.list",array_merge($queryParam,['page'=>$total_page-3]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$total_page-3}}</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.order.list",array_merge($queryParam,['page'=>$total_page-2]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$total_page -2}}</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.order.list",array_merge($queryParam,['page'=>$total_page-1]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$total_page -1}}</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.order.list",array_merge($queryParam,['page'=>$total_page]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$total_page }}</a></li>




                                @else
                                    @for($i=0;$i<$total_page ;$i++ )

                                        <li class="paginate_button active"><a href="{{route("admin.order.list",array_merge($queryParam,['page'=>$i+1]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$i+1}}</a></li>

                                    @endfor


                                @endif


                            @endif
                        </ul>
                    </div>
                </div>


            </div>
        </div>
    </div>


    <script type="text/javascript">
        $(document).ready(function() {

            $( "#search_filter" ).click(function() {
                var tracking_number = $( "#tracking_number" ).val();
                var status = $( "#status" ).val();
                var field = $( "#field" ).val();


                var params = [];


                if(tracking_number)
                    params.push("tracking_number="+tracking_number)
                if (status)
                    params.push("status="+status)
                if (field)
                    params.push("category_id="+field);


                window.location.href =
                    "http://" +
                    window.location.host +
                    window.location.pathname +
                    '?' + params.join('&');



                console.log(myJSON);

                if(data) {

                    $.ajax({
                        type: "POST",
                        url: 'order/filter',
                        data :myJSON,
                        success:function(data) {

                           var tablebody = $( "#tuserbody" );

                            tablebody.empty();


                            for (i = 0; i < data.length; i++) {


                                tablebody.append(' <tr role="row" class="odd"><td class="sorting_1">'+data[i]['user']['name']+' '+data[i]['user']['family']+'</td>'+'<td class="sorting_1">'+data[i]['created_at']+'</td>'+'<td class="sorting_1">'+data[i]['status']+'</td>'+'<td>'+'<a href="/admin/order/detail/'+data[i]['order_id']+'">'+'<i class="fa fa-edit">'+'</i>'+'</td>'+'</tr> ' )


                               tablebody.append('</tr>');
                            }


                        },
                        dataType: "json"
                    });
                }else{

                }

            });
        });
    </script>
    <script type="text/javascript">

        function onhideWorker() {
            var x = document.getElementById("typeOfuser").value;

            if (x == "worker"){
                x =document.getElementById("worker_filter_field");

                if (x.style.display="block")
                {
                }

            }else {
                x =document.getElementById("worker_filter_field");

                if (x.style.display="none")
                {
                }
            }
        }
        function search() {
            var  email =document.getElementById("email").value;
            var mobile = document.getElementById("mobile").value;
            var status = document.getElementById("status").value;
            var typeOfuser = document.getElementById("typeOfuser").value;
            var queryUser={};
            if (email)
                queryUser.email=email;
            if (mobile)
                queryUser.phone_number=mobile;
            if(status)
                queryUser.status=status;
            if (typeOfuser)
                queryUser.role=typeOfuser;

            var  nationalCode =document.getElementById("nationalCode").value;
            var field = document.getElementById("field").value;
            var gender = document.getElementById("gender").value;
            var adminStatus = document.getElementById("adminStatus").value;
            var availabilitystatus = document.getElementById("availabilitystatus").value;

            var queryWorker={};
            if (nationalCode)
                queryWorker.nationalCode=nationalCode;
            if (field)
                queryUser.field=field;
            if(gender)
                queryUser.gender=gender;
            if (adminStatus)
                queryUser.status=status;


            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("txtHint").innerHTML = this.responseText;
                }
            };
            xmlhttp.open();
            xmlhttp.send();




        }
    </script>

   <?php


   ?>

@endsection

