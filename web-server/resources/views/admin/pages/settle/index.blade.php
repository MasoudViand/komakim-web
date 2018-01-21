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


            <a href="{{route('admin.settle.export',$queryParam)}}"> <button id="export_csv" class="btn btn-primary" >خروجی اکسل</button></a>
            <div class="row">
                <div class="col-sm-6">
                    <label> تعداد داده ها برای هر صحفه</label>
                    <input type="number" id="per_page_num">
                </div>
                <div class="col-sm-6">
                    <button class="btn btn-primary" id="per_page_num_button">ثبت</button>
                </div>

            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table id="user_table" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                        <thead>
                        <tr role="row">
                            <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">نام</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">نام خوانوادگی</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">شماره تلفن</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">اعتبار</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">اعتبار</th>
                        </tr>
                        </thead>
                        <tbody id="tuserbody">
                        @foreach($wallets as $wallet)

                            <tr role="row" class="odd">
                                <td class="sorting_1">{{$wallet['user'][0]['name']}}</td>
                                <td class="sorting_1">{{$wallet['user'][0]['family']}}</td>
                                <td class="sorting_1">{{$wallet['user'][0]['phone_number']}}</td>
                                <td class="sorting_1">{{$wallet['amount']}}</td>
                                <td hidden class="sorting_1">{{$wallet['_id']}}</td>
                                <td><input type="checkbox"></td>

                            </tr>

                        @endforeach


                        </tbody>
                    </table>
                </div>
                <div class="row">


                </div>
                <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example1_paginate">
                        <ul class="pagination">

                            @if($count>count($wallets))

                                @if($page>8)
                                    <li class="paginate_button active"><a href="{{route("admin.settle.dept.list",array_merge($queryParam,['page'=>1]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">1</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.settle.dept.list",array_merge($queryParam,['page'=>2]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">2</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.settle.dept.list",array_merge($queryParam,['page'=>3]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">3</a></li>
                                    <li class="paginate_button active"><a href="#" aria-controls="example1" data-dt-idx="1" tabindex="0">.</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.settle.dept.list",array_merge($queryParam,['page'=>$page-3]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$page-3}}</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.settle.dept.list",array_merge($queryParam,['page'=>$page-2]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$page -2}}</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.settle.dept.list",array_merge($queryParam,['page'=>$page-1]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$page -1}}</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.settle.dept.list",array_merge($queryParam,['page'=>$page]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$page }}</a></li>




                                @else
                                    @for($i=0;$i<$page ;$i++ )

                                        <li class="paginate_button active"><a href="{{route("admin.settle.dept.list",array_merge($queryParam,['page'=>$i+1]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$i+1}}</a></li>

                                    @endfor


                                @endif


                            @endif
                        </ul>
                    </div>
                </div>

                <label>انتخاب همه</label>
                <input type="checkbox" onclick="checkAll(this)">
                <button id="settle_worker" class="btn btn-primary" >تسویه حساب</button>


            </div>
            <div class="row">
                <div class="col-sm-5">
                    <div class="dataTables_info" id="example1_info" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div>
                </div>



            </div>
        </div>
    </div>


    <script>
        function checkAll(bx) {
            var cbs = document.getElementsByTagName('input');
            for(var i=0; i < cbs.length; i++) {
                if(cbs[i].type == 'checkbox') {
                    cbs[i].checked = bx.checked;
                }
            }
            $('#user_table input[type=checkbox]:checked').each(function() {




                var row = $(this).parent().parent();
                console.log(row);
                var rowcells = row.find('td');
                console.log(rowcells[0].innerHTML);
                // rowcells contains all td's in the row
                // you can do
                // rowcells.each(function() {var tdhtml = $(this).html(); });
                // to cycle all of them

            });
        }

        $(document).ready(function() {

//
            $("#per_page_num_button").click(function () {


                var per_page_num = $( "#per_page_num" ).val();


                var params=[];

                params.push("limit="+per_page_num)


                window.location.href =
                    "http://" +
                    window.location.host +
                    window.location.pathname +
                    '?' + params.join('&');



            });
            $( "#settle_worker" ).click(function () {


                var numbers=[];

                $('#user_table input[type=checkbox]:checked').each(function() {




                    var row = $(this).parent().parent();
                    console.log(row);
                    var rowcells = row.find('td');
                    number =rowcells[4].innerHTML;
                    numbers.push(number)
                    // rowcells contains all td's in the row
                    // you can do
                    // rowcells.each(function() {var tdhtml = $(this).html(); });
                    // to cycle all of them

                });
                console.log(numbers);
                var myJSON = JSON.stringify(numbers);
                console.log(myJSON);
                $.ajax({
                    type: "POST",
                    url: 'settle/done',
                    data :myJSON,
                    success:function(data) {

                        console.log(data);

                        location.reload();

                    },
                    dataType: "json"
                });


            })

        });
    </script>





@endsection



