@extends('admin.template.admin_template')


@section('content')
    <link href="http://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
    <style>
        body {
        }
        .rtl-col {
            float: right;
        }
        #bd-next-date2, #bd-prev-date2 {
            font-size: 20px;
        }
        .tooltip > .tooltip-inner {
            font-family: Vazir;
            font-size: 12px;
            padding: 4px;
            white-space: pre;
            max-width: none;
        }
        #options-table {
            border-collapse: collapse;
            width: 100%;
        }
        #options-table td, #options-table th {
            border: 1px solid #777;
            text-align: left;
            padding: 8px;
        }
        #options-table tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>


    <link rel="stylesheet" href="{{asset("Persian-Jalali-Calendar-Data-Picker-Plugin-With-jQuery-kamaDatepicker/style/kamadatepicker.css") }}">
    <script src="{{asset("Persian-Jalali-Calendar-Data-Picker-Plugin-With-jQuery-kamaDatepicker/src/kamadatepicker.js") }}"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <div class="box-body">

        @if(Session::has('success'))
            <div class="alert alert-success" role="alert">
                <strong>
                    {{ Session::get('success') }}
                </strong>
            </div>
        @endif
            @if(Session::has('error'))
                <div class="alert alert-success" role="alert">
                    <strong>
                        {{ Session::get('error') }}
                    </strong>
                </div>
            @endif

<div class="box col-sm-3">
            <div class="row" style="margin-top: 50px">
                <div class="col-sm-12">
                    <label> از تاریخ :</label>
                    <input  id="from" type="text" value="{{key_exists('from',$queryParam) ?$queryParam['from']:''}}">
                </div>
				<div class="clearfix"></div>
                <div class="col-sm-12">
                    <label> تا تاریخ :</label>
                    <input id="to" type="text" value="{{key_exists('to',$queryParam) ?$queryParam['to']:''}}">
                </div>
				<div class="clearfix"></div>
                <div class="col-sm-12">
                    <label> نوع :</label><br>
                    <select name="typeOfuser" id="type"  style="">
                        <option value="{{key_exists('type',$queryParam) ?$queryParam['type']:''}}">{{key_exists('type',$queryParam)?(($queryParam['type']==\App\Transaction::PAY_ORDER ?'پرداخت مشتری':(($queryParam['type']==\App\Transaction::DONE_ORDER ?'واریز به کیف پول خدمه':($queryParam['type']==\App\Transaction::CHARGE_FROM_BANK ?'شارژ از بانک':'تسفیه حساب با مشتری'))))):'--- انتخاب نوع ---'}}</option>

                        <option value="{{\App\Transaction::PAY_ORDER}}">پرداخت مشتری</option>
                        <option value="{{\App\Transaction::DONE_ORDER}}">واریز به کیف پول خدمه</option>
                        <option value="{{\App\Transaction::CHARGE_FROM_BANK}}">شارژ از بانک</option>
                        <option value="{{\App\Transaction::BALANCE_ACCOUNT}}">تسفیه حساب با مشتری</option>

                    </select>
                </div>
				<div class="clearfix"></div>
					<div  class="col-sm-6">
						<label> تعداد نمایش در هر صحفه</label><br>
						<input type="number" id="per_record_in_page" value="{{key_exists('limit',$queryParam)?$queryParam['limit']:''}}">
					</div>
					<div class="clearfix"></div><br>
                    <button id="filter_button" class="btn btn-primary btn-group" style="margin:15px;">اعمال</button>
					<div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
</div>
        <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">

            <div class="row">
                <div class="col-sm-12">
                    <table id="example1" class="box table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                        <thead>
                        <tr role="row">
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">مقدار</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">کاربر</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">تاریخ</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">نوع</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $transaction)



                            <tr role="row" class="odd">
                                <td class="sorting_1">{{$transaction['amount']}}</td>
                                <td class="sorting_1">{{$transaction['user']['name'].' '.$transaction['user']['family']}}</td>
                                <td class="sorting_1">{{$transaction['created_at']}}</td>
                                <td class="sorting_1">{{$transaction['type']}}</td>

                            </tr>

                        @endforeach


                        </tbody>
                    </table>
                </div>
            </div>

                <div class="col-sm-5">
                    <div class="dataTables_info" id="example1_info" role="status" aria-live="polite"> نشان دادن {{count($transactions)}} از {{$total_count}}</div>
                </div>
            <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example1_paginate">
                    <ul class="pagination">

                        @if($total_page>1)

                            @if($total_page>8)
                                <li class="paginate_button active"><a href="{{route("admin.transactions.list",array_merge($queryParam,['page'=>1]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">1</a></li>
                                <li class="paginate_button active"><a href="{{route("admin.transactions.list",array_merge($queryParam,['page'=>2]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">2</a></li>
                                <li class="paginate_button active"><a href="{{route("admin.transactions.list",array_merge($queryParam,['page'=>3]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">3</a></li>
                                <li class="paginate_button active"><a href="#" aria-controls="example1" data-dt-idx="1" tabindex="0">.</a></li>
                                <li class="paginate_button active"><a href="{{route("admin.transactions.list",array_merge($queryParam,['page'=>$total_page-3]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$total_page-3}}</a></li>
                                <li class="paginate_button active"><a href="{{route("admin.transactions.list",array_merge($queryParam,['page'=>$total_page-2]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$total_page -2}}</a></li>
                                <li class="paginate_button active"><a href="{{route("admin.transactions.list",array_merge($queryParam,['page'=>$total_page-1]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$total_page -1}}</a></li>
                                <li class="paginate_button active"><a href="{{route("admin.transactions.list",array_merge($queryParam,['page'=>$total_page]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$total_page }}</a></li>




                            @else
                                @for($i=0;$i<$total_page ;$i++ )

                                    <li class="paginate_button active"><a href="{{route("admin.transactions.list",array_merge($queryParam,['page'=>$i+1]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$i+1}}</a></li>

                                @endfor


                            @endif


                        @endif
                    </ul>
                </div>
            </div>

            <div>
                <a href="{{route('admin.transaction.list.export',$queryParam)}}"><button class="btn btn-primary">خروجی اکسل</button></a>
            </div>

        </div>
            <script>
                kamaDatepicker('from', { buttonsColor: "red" });
                kamaDatepicker('to', { buttonsColor: "red" });

            </script>

        <script>


            $(document).ready(function() {
                $("#per_record_in_page_button").click(function () {





                    var limit = $("#per_record_in_page").val()
                    console.log(limit);

                    var params = [];


                    if(limit)
                        params.push("limit="+limit)

                    window.location.href =
                        "http://" +
                        window.location.host +
                        window.location.pathname +
                        '?' + params.join('&');

                })

                $( "#filter_button" ).click(function() {
                    var from = $( "#from" ).val();
                    var to = $( "#to" ).val();
                    var type = $( "#type" ).val();
                    var limit = $("#per_record_in_page").val()


                    console.log(type);
                    var params = [];


                    if(from)
                        params.push("from="+from)
                    if (to)
                        params.push("to="+to)
                    if (type)
                        params.push("type="+type)
                    if(limit)
                        params.push("limit="+limit)


                    window.location.href =
                        "http://" +
                        window.location.host +
                        window.location.pathname +
                        '?' + params.join('&');

                });
            });
        </script>
    </div>

@endsection


