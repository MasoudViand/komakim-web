@extends('admin.template.admin_template')

@section('content')
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <div class="box-body">








            <div class="row" style="margin-top: 50px">
                <div class="col-sm-4">
                    <label> از تاریخ :</label>
                    <input type="text" id="from" {{key_exists('from',$queryparam)?$queryparam['from']:''}}>
                </div>
                <div class="col-sm-4">
                    <label> تا تاریخ :</label>
                    <input type="text"  id="to" {{key_exists('to',$queryparam)?$queryparam['to']:''}}>
                </div>
            <div class="col-sm-4">
                <button class="btn btn-primary" id="btn_date_search"> ثبت </button>
            </div>
            </div>'


        <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">

            <div class="row">
                <div class="col-sm-12">
                    <table id="user_table" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                        <thead>
                        <tr role="row">
                            <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">تعداد تراکنش ها</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">جمع درامد خالص</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">جمع درامد ناخالص</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">نوع</th>

                        </tr>
                        </thead>
                        <tbody id="tuserbody">
                        <tr role="row" class="odd">
                            <td class="sorting_1">{{$count_sum}}</td>
                            <td class="sorting_1">{{$commission_sum}}</td>
                            <td class="sorting_1">{{$total_price_sum}}</td>
                            <td class="sorting_1">{{key_exists('mode',$queryparam)?($queryparam['mode']=='daily'?'روزانه':($queryparam['mode']=='weekly'?'هفتگی':'ماهانه')):'روزانه'}}</td>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
                <div id="blockChart"  class="col-md-12">
                    <!-- AREA CHART -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Area Chart</h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="chart">
                                {{--<canvas id="areaChart" style="height:250px"></canvas>--}}
                                <canvas id="line-chart" width="800" height="450"></canvas>

                            </div>
                        </div><!-- /.box-body -->



                    </div>

                </div>
        <div class="row">
            <div class="col-sm-4">
                <label> تعداد داده ها</label>
                <input type="number" value="{{key_exists('limit',$queryparam)?$queryparam['limit']:''}}" id="limit">
            </div>
            <div class="col-sm-4">
                <label> نو ع گزارش گیری</label>
                <select id ="mode" name="mdoe" class="form-control" style="">
                    <option value="{{key_exists('mode',$queryparam) ? $queryparam['mode']:''}}">{{key_exists('mode',$queryparam) ? ($queryparam['mode']=='daily'?'روزانه':($queryparam['mode']=='weekly'?'هفتگی':'ماهانه')):'--- انتخاب حالت گزارش گیری--'}}</option>

                    <option value="daily">روزانه</option>
                    <option value="weekly">هفتگی</option>
                    <option value="monthly">ماهانه</option>

                </select>
            </div>
            <div class="col-sm-4">
                <button class="btn btn-primary" id="search_filter"> جست جو</button>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-6"><a href="{{route('admin.financial',$preQueryparam)}}"> <button class="btn-primary btn"> قبلی </button></a></div>
            <div class="col-sm-6"><a href="{{route('admin.financial',$nextQueryparam)}}"> <button class="btn-primary btn"> بعدی </button></a></div>
        </div>
    </div>






    <script>
       $(document).ready(function() {
            {{--modesearch = {{key_exists('mode',$queryparam)?$queryparam['mode']:'daily'}}--}}
            {{--modesearch = {{json_encode(key_exists('mode',$queryparam)?$queryparam['mode']:'daily')}}--}}

            $("#btn_date_search").click(function () {
                console.log('asdasd');



                from =$("#from").val();
                to_value=$("#to").val();
                mode =$("#mode").val()


                var data={};
                if(from)
                    data.from =from;
                if (to_value)
                    data.to =to_value;
                if (mode)
                    data.mode = mode;




                var myJSON = JSON.stringify(data);

                if(data) {

                    $.ajax({
                        type: "POST",
                        url: 'financial/filter',
                        data :myJSON,
                        success:function(data) {

                            console.log(data.total_commission)
                            var tablebody = $( "#tuserbody" );

                            tablebody.empty();
                            tablebody.append(' <tr role="row" class="odd">'+' <td class="sorting_1">'+data.total_count+'</td>'+' <td class="sorting_1">'+data.total_prices+'</td>'+' <td class="sorting_1">'+data.total_commission+'</td>'+' <td class="sorting_1">'+mode+'</td>'+'</tr>')



                            {{--for (i = 0; i < data.length; i++) {--}}
                                {{--console.log(data[i]._id);--}}

                                {{--if (data[i].role=='worker')--}}
                                    {{--type = 'خدمه';--}}
                                {{--else--}}
                                    {{--type ='مشتری';--}}

                                {{--tablebody.append(' <tr role="row" class="odd">'+' <td class="sorting_1">'+data.total_commission+'</td>'+' <td class="sorting_1">'+data[i].family+'</td>'+' <td class="sorting_1">'+data[i].phone_number+'</td>'+' <td class="sorting_1">'+type+'</td>'+' <td>'+'<a href="/admin/user/update/'+data[i]._id+'">'+'<i class="fa fa-edit">'+'</i>'+'</td>'+'</tr>')--}}
{{--//                                    tablebody.append(' <td class="sorting_1">'+data[i].name+'</td>');--}}
{{--//                                    tablebody.append(' <td class="sorting_1">'+data[i].family+'</td>');--}}
{{--//                                    tablebody.append(' <td class="sorting_1">'+data[i].phone_number+'</td>');--}}
{{--//                                    tablebody.append(' <td class="sorting_1">'+type+'</td>');--}}
{{--//                                    tablebody.append(' <td>'+'<a href="/admin/user/update/'+data[i]._id+'">'+'<i class="fa fa-edit">'+'</i>'+'</td>');--}}

                                {{--<td class="sorting_1">{{$user->name}}</td>--}}
                                {{--<td class="sorting_1">{{$user->family}}</td>--}}
                                {{--<td class="sorting_1">{{$user->phone_number}}</td>--}}
                                {{--<td>{{ $user->role=='client' ?'عادی':'خدمه' }}</td>--}}
                                {{--                                    <td><a href="{{rou/te('admin.user.update',['user_id' => $user->id])}}"><i class="fa fa-edit"></i></a></td>--}}

                                {{--//                               tablebody.append('</tr>');--}}
                            {{--}--}}


                        },
                        dataType: "json"
                    });
                }else{

                }


            })
            $("#search_filter").click(function () {

               limit = $("#limit").val()
                mode = $("#mode").val();


                console.log(limit);

                var params = [];

                if(mode)
                    params.push("mode="+mode)
                if (limit)
                    params.push("limit="+limit)

                console.log(params);


                window.location.href =
                    "http://" +
                    window.location.host +
                    window.location.pathname +
                    '?' + params.join('&');


            })




        });

    </script>










@endsection

@section('foot')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <script>
        var labels= ("{{ json_encode($x_axis) }}");
        labels =JSON.parse(labels.replace(/&quot;/g,'"'));


        var commissions= ("{{ json_encode($y_commission) }}");

        console.log(commissions);
        commissions =JSON.parse(commissions.replace(/&quot;/g,'"'));
        console.log(commissions);
        var total_prices= ("{{ json_encode($y_total_price) }}");
        total_prices =JSON.parse(total_prices.replace(/&quot;/g,'"'));
        console.log(total_prices);

        new Chart(document.getElementById("line-chart"), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: commissions,
                    label: "درامد خالص",
                    borderColor: "#3e95cd",
                    fill: false
                }, {
                    data: total_prices,
                    label: "درامد ناخالص",
                    borderColor: "#8e5ea2",
                    fill: false
                }
                ]
            },
            options: {
                title: {
                    display: true,
                    text: 'گزارش مالی روزانه'
                }
            }
        });
    </script>






@endsection

