@extends('admin.template.admin_template')

@section('content')
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
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
                        <label>کدپیگیری<input type="search"id="tracking_number" class="form-control input-sm" placeholder="" aria-controls="example1">
                        </label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div id="example1_filter" class="dataTables_filter">

                            <select id ="status" name="status" class="form-control" style="">
                                <option value="">--- انتخاب وضعیت--</option>

                                    <option value="{{\App\OrderStatusRevision::WAITING_FOR_WORKER_STATUS}}">منتظر تایید خدمه</option>
                                    <option value="{{\App\OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS}}">قبول شده توسط خدمه</option>
                                    <option value="{{\App\OrderStatusRevision::START_ORDER_BY_WORKER_STATUS}}">شروع کار توسط خدمه</option>
                                    <option value="{{\App\OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS}}">اتمام توسط خدمه</option>
                                    <option value="{{\App\OrderStatusRevision::PAID_ORDER_BY_CLIENT_STATUS}}">پرداخت شده توسط خدمه</option>
                                    <option value="{{\App\OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS}}">لغو توسط خدمه</option>
                                    <option value="{{\App\OrderStatusRevision::CANCEL_ORDER_BY_CLIENT_STATUS}}">لغو توسط مشتری</option>

                            </select>
                        </label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div id="example1_filter" class="dataTables_filter">

                        <select name="typeOfuser" id="field" onchange="onhideWorker()" class="form-control" style="">
                            <option value="">--- انتخاب زمینه همکاری ---</option>
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
            <div class="row">
                <div class="col-sm-12">
                    <table id="user_table" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                        <thead>
                        <tr role="row">
                            <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">نام و نام خانوادگی</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">تاریخ در خواست</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">وضعیت درخواست	</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">جزییات سفارش</th>
                        </tr>
                        </thead>
                        <tbody id="tuserbody">
                        @foreach($orders as $order)


                            <tr role="row" class="odd">
                                <td class="sorting_1">{{$order['user']->name.'  '.$order['user']->family}}</td>
                                <td class="sorting_1">{{$order['created_at']}}</td>
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
                    <div class="dataTables_info" id="example1_info" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div>
                </div>
                <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example1_paginate">
                        <ul class="pagination">
                            <li class="paginate_button previous disabled" id="example1_previous"><a href="#" aria-controls="example1" data-dt-idx="0" tabindex="0">Previous</a></li>
                            <li class="paginate_button active"><a href="#" aria-controls="example1" data-dt-idx="1" tabindex="0">1</a></li>
                            <li class="paginate_button "><a href="#" aria-controls="example1" data-dt-idx="2" tabindex="0">2</a></li>
                            <li class="paginate_button "><a href="#" aria-controls="example1" data-dt-idx="3" tabindex="0">3</a></li>
                            <li class="paginate_button "><a href="#" aria-controls="example1" data-dt-idx="4" tabindex="0">4</a></li>
                            <li class="paginate_button "><a href="#" aria-controls="example1" data-dt-idx="5" tabindex="0">5</a></li>
                            <li class="paginate_button "><a href="#" aria-controls="example1" data-dt-idx="6" tabindex="0">6</a></li>
                            <li class="paginate_button next" id="example1_next"><a href="#" aria-controls="example1" data-dt-idx="7" tabindex="0">Next</a></li>
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

                console.log(field);

                var data ={};
                if(tracking_number)
                    data.tracking_number=tracking_number;
                if (status)
                    data.status=status;
                if (field)
                    data.field=field;
               // var obj = { "name":"John", "age":30, "city":"New York"};
                var myJSON = JSON.stringify(data);



                console.log(myJSON);

                if(data) {

                    $.ajax({
                        type: "POST",
                        url: 'order/filter',
                        data :myJSON,
                        success:function(data) {

                            console.log('sdd');
                           var tablebody = $( "#tuserbody" );

                            tablebody.empty();


                            for (i = 0; i < data.length; i++) {
                              //  console.log(data[i]['user']['name']);


                                tablebody.append(' <tr role="row" class="odd"><td class="sorting_1">'+data[i]['user']['name']+' '+data[i]['user']['family']+'</td>'+'<td class="sorting_1">'+data[i]['created_at']+'</td>'+'<td class="sorting_1">'+data[i]['status']+'</td>'+'<td>'+'<a href="/admin/order/detail/'+data[i]['order_id']+'">'+'<i class="fa fa-edit">'+'</i>'+'</td>'+'</tr> ' )
                                   // tablebody.append(' <td class="sorting_1">'+data[i]['user']['name']+'  '+  data[i]['user']['family']+'</td>');
                                  //  tablebody.append(' <td class="sorting_1">'+data[i]['created_at']+'</td>');
                                    //tablebody.append(' <td class="sorting_1">'+data[i]['status']+'</td>');
                                                                                                                                                                                                                                                      //  tablebody.append(' <td>'+'<a href="/admin/order/detail/'+data[i]['order_id']+'">'+'<i class="fa fa-edit">'+'</i>'+'</td>');

                                    {{--<td class="sorting_1">{{$user->name}}</td>--}}
                                    {{--<td class="sorting_1">{{$user->family}}</td>--}}
                                    {{--<td class="sorting_1">{{$user->phone_number}}</td>--}}
                                    {{--<td>{{ $user->role=='client' ?'عادی':'خدمه' }}</td>--}}
{{--                                    <td><a href="{{rou/te('admin.user.update',['user_id' => $user->id])}}"><i class="fa fa-edit"></i></a></td>--}}

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

            console.log(x)
            if (x == "worker"){
                console.log('ccccccc');
                x =document.getElementById("worker_filter_field");

                if (x.style.display="block")
                {
                    console.log('sddd');
                }

            }else {
                x =document.getElementById("worker_filter_field");

                if (x.style.display="none")
                {
                    console.log('sddd');
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

@endsection

