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

                <div class="col-sm-3">
                    <div id="example1_filter" class="dataTables_filter">
                        <label>ایمیل<input type="search" id="email" class="form-control input-sm" placeholder="" aria-controls="example1">
                        </label>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div id="example1_filter" class="dataTables_filter">
                        <label>تلفن همراه<input type="search"id="mobile" class="form-control input-sm" placeholder="" aria-controls="example1">
                        </label>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div id="example1_filter" class="dataTables_filter">

                            <select id ="status" name="status" class="form-control" style="">
                                <option value="">--- انتخاب وضعیت ---</option>

                                    <option value="active">فعال</option>
                                    <option value="inactive">غیرفعال</option>

                            </select>
                        </label>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div id="example1_filter" class="dataTables_filter">

                        <select name="typeOfuser" id="typeOfuser" onchange="onhideWorker()" class="form-control" style="">
                            <option value="">--- انتخاب نوع ---</option>

                            <option value="client">مشتری</option>
                            <option value="worker">خدمه</option>

                        </select>
                        </label>
                    </div>
                </div>

            </div>

        </br> </br> </br>

            <div class="row"  id="worker_filter_field" style="display: none">

                <div class="col-sm-2">
                    <div id="example1_filter" class="dataTables_filter">
                        <label>کدملی<input type="search" id="nationalCode" name="nationalCode" class="form-control input-sm" placeholder="" aria-controls="example1">
                        </label>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div id="example1_filter" class="dataTables_filter">

                        <select name="field" id="field" class="form-control" style="">
                            <option value="">--- انتخاب زمینه ---</option>
                            <option value="1">1</option>
                            <option value="2">2</option>

                        </select>

                    </div>
                </div>

                <div class="col-sm-2">
                    <div id="example1_filter" class="dataTables_filter">

                        <select name="gender"id="gender" class="form-control" style="">
                            <option value="">- انتخاب جنسیت -</option>

                            <option value="female">زن</option>
                            <option value="male">مرد</option>

                        </select>

                    </div>
                </div>
                <div class="col-sm-2">
                    <div id="example1_filter" class="dataTables_filter">

                        <select name="adminStatus" id="adminStatus" class="form-control" style="">
                            <option value="">--- وضعیت تایید ادمین ---</option>

                            <option value="pending">منتظر تایید</option>
                            <option value="accept">تایید شده</option>
                            <option value="reject">رد شده</option>

                        </select>
                        </label>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div id="example1_filter" class="dataTables_filter">

                        <select id="availabilitystatus" name="availabilitystatus" class="form-control" style="">
                            <option value="">--- وضعیت قابلیت ارایه سرویس ---</option>

                            <option value="available">اماده برای ارایه سرویس</option>
                            <option value="unavailable">جارج از سرویس دهی</option>

                        </select>
                        </label>
                    </div>
                </div>

            </div>

            <button id="search_filter" class="btn btn-primary" > جست جو</button>
            <div class="row">
                <div class="col-sm-12">
                    <table id="user_table" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                        <thead>
                        <tr role="row">
                            <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">نام</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">نام خوانوادگی</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">شماره تلفن</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">نوع کاربر</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">جزییات بیشتر و ویرایش</th>
                        </tr>
                        </thead>
                        <tbody id="tuserbody">
                        @foreach($users as $user)

                            <tr role="row" class="odd">
                                <td class="sorting_1">{{$user->name}}</td>
                                <td class="sorting_1">{{$user->family}}</td>
                                <td class="sorting_1">{{$user->phone_number}}</td>
                                <td>{{ $user->role=='client' ?'عادی':'خدمه' }}</td>
                                <td><a href="{{route('admin.user.update',['user_id' => $user->id])}}"><i class="fa fa-edit"></i></a></td>

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
                var email = $( "#email" ).val();
                var mobile = $( "#mobile" ).val();
                var status = $( "#status" ).val();
                var role = $( "#typeOfuser" ).val();
                var national_code = $( "#nationalCode" ).val();
                var field = $( "#field" ).val();
                var gender = $( "#gender" ).val();
                var admin_status = $( "#adminStatus" ).val();
                var availabilitystatus = $( "#availabilitystatus" ).val();

                var data ={};
                if(email)
                    data['email']=email;
                if(mobile)
                    data.mobile=mobile;
                if (status)
                    data.status=status;
                if(role)
                    data.role =role;
                if (national_code)
                    data.national_code=national_code;
                if (field)
                    data.field=field;
                if (gender)
                    data.gender =gender;
                if (admin_status)
                    data.admin_status=admin_status;
                if (availabilitystatus)
                    data.availabilitystatus=availabilitystatus;


               // var obj = { "name":"John", "age":30, "city":"New York"};
                var myJSON = JSON.stringify(data);



                console.log(myJSON);

                if(data) {

                    $.ajax({
                        type: "POST",
                        url: '/admin/user/filter',
                        data :myJSON,
                        success:function(data) {
                           var tablebody = $( "#tuserbody" );

                            tablebody.empty();


                            for (i = 0; i < data.length; i++) {
                                console.log(data[i].name);

                                if (data[i].role=='worker')
                                    type = 'خدمه';
                                else
                                    type ='مشتری';
                                tablebody.append(' <tr role="row" class="odd">')
                                    tablebody.append(' <td class="sorting_1">'+data[i].name+'</td>');
                                    tablebody.append(' <td class="sorting_1">'+data[i].family+'</td>');
                                    tablebody.append(' <td class="sorting_1">'+data[i].phone_number+'</td>');
                                    tablebody.append(' <td class="sorting_1">'+type+'</td>');
                                    tablebody.append(' <td>'+'<a href="/admin/user/update/'+data[i]._id+'">'+'<i class="fa fa-edit">'+'</i>'+'</td>');

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

