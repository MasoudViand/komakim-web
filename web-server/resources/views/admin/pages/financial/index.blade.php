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


    <div class="col-sm-12 box">


            <div class="row" style="margin-top: 50px">
			
                <div class="col-sm-4 form-inline">
                    <label> از تاریخ :</label>
                    <input type="text" class="form-control" id="from" value="{{$queryparam['from_date']}}" >
                </div>
				
				<div class="clearfix"></div>
				
                <div class="col-sm-4 form-inline">
                    <label> تا تاریخ :</label>
                    <input type="text" class="form-control" id="to" value="{{$queryparam['to_date']}}">
                </div>
				
				<div class="clearfix"></div><br>
				
				<div class="col-sm-4">
					<button class="btn btn-primary" id="btn_date_search"> ثبت </button>
				</div>
			
			
            </div>

        <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">

            <div class="row">
                <div class="col-sm-12">
                    <table id="user_table" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                        <thead>
                        <tr role="row">
                            <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">تعداد تراکنش ها</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">جمع درامد خالص</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">جمع درامد ناخالص</th>

                        </tr>
                        </thead>
                        <tbody id="tuserbody">
                        <tr role="row" class="odd">
                            <td class="sorting_1">{{$count_field}}</td>
                            <td class="sorting_1">{{$commission_field}}</td>
                            <td class="sorting_1">{{$total_price_field}}</td>

                        </tr>
                        </tbody>
                    </table>
                </div>
				
            </div>
        </div>
		
		</div>
		
        <hr>
        <div class="row">
            <div class="col-sm-4">
                <label> تعداد داده ها</label>
                <input type="number" value="{{key_exists('limit',$queryparam)?$queryparam['limit']:''}}" id="limit">
            </div>
            <div class="col-sm-4">
                <form class="form-inline">
                    <label style="margin-left: 12px">نمایش نمودار</label>

                    <select id ="mode" name="mdoe" class="form-control" style="width: auto">
                        <option value="{{key_exists('mode',$queryparam) ? $queryparam['mode']:''}}">{{key_exists('mode',$queryparam) ? ($queryparam['mode']=='daily'?'روزانه':($queryparam['mode']=='weekly'?'هفتگی':'ماهانه')):'--- انتخاب حالت گزارش گیری--'}}</option>

                        <option value="daily">روزانه</option>
                        <option value="weekly">هفتگی</option>
                        <option value="monthly">ماهانه</option>

                    </select>

                </form>

            </div>
            <div class="col-sm-4">
                <button class="btn btn-primary" id="search_filter"> جست جو</button>
            </div>

        </div>
        <br>
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
            <div class="col-sm-6"><a href="{{route('admin.financial',$preQueryparam)}}"> <button class="btn-primary btn"> قبلی </button></a></div>
            <div class="col-sm-6"><a href="{{route('admin.financial',$nextQueryparam)}}"> <button class="btn-primary btn"> بعدی </button></a></div>
        </div>
    </div>
    <script>
        kamaDatepicker('from', { buttonsColor: "red" });
        kamaDatepicker('to', { buttonsColor: "red" });

    </script>

















@endsection

@section('foot')

    {{--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>--}}
    {{--<script src="{{asset("bootstrap-jalali-datepicker-master/bootstrap-datepicker.min.js") }}"></script>--}}
    {{--<script src="{{asset("bootstrap-jalali-datepicker-master/bootstrap-datepicker.fa.min.js") }}"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>




    <script>
        $("#btn_date_search").click(function () {



            from =$("#from").val();
            to_value=$("#to").val();
            mode =$("#mode").val()


            var params = [];

            if(from)
                params.push("from_date="+from)
            if (to_value)
                params.push("to_date="+to_value)

            console.log(params);


            window.location.href =
                "http://" +
                window.location.host +
                window.location.pathname +
                '?' + params.join('&');


         

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

        var labels= ("{{ json_encode($x_axis) }}");
        labels =JSON.parse(labels.replace(/&quot;/g,'"'));


        var commissions= ("{{ json_encode($y_commission) }}");

        commissions =JSON.parse(commissions.replace(/&quot;/g,'"'));
        var total_prices= ("{{ json_encode($y_total_price) }}");
        total_prices =JSON.parse(total_prices.replace(/&quot;/g,'"'));

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


