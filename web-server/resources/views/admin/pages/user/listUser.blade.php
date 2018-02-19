@extends('admin.template.admin_template')

@section('content')
    {{--<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <link rel="stylesheet" href="{{asset("bootstrap-select-1.12.4/dist/css/bootstrap-select.css") }}">
    <script src="{{asset("bootstrap-select-1.12.4/dist/js/bootstrap-select.js") }}"></script>
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
                        <label>ایمیل<input type="search" id="email" class="form-control input-sm" placeholder="" value="{{key_exists('email',$queryParam)?$queryParam['email']:''}}" aria-controls="example1">
                        </label>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div id="example1_filter" class="dataTables_filter">
                        <label>تلفن همراه<input type="search"id="mobile" class="form-control input-sm" value="{{key_exists('phone_number',$queryParam)?$queryParam['phone_number']:''}}" placeholder="" aria-controls="example1">
                        </label>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div id="example1_filter" class="dataTables_filter">

                            <select id ="status" name="status" class="form-control" style="">
                                <option value="{{key_exists('status',$queryParam)?$queryParam['status']:''}}">{{key_exists('status',$queryParam)?($queryParam['status']=='active'?'فعال':'غیرفعال'):'--انتخاب وضعیت--'}}</option>

                                    <option value="active">فعال</option>
                                    <option value="inactive">غیرفعال</option>

                            </select>
                        </label>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div id="example1_filter" class="dataTables_filter">

                        <select name="typeOfuser" id="typeOfuser" onchange="onhideWorker()" class="form-control" style="">
                            <option value="{{key_exists('role',$queryParam)?$queryParam['role']:''}}">{{key_exists('role',$queryParam)?($queryParam['role']=='worker'?'خدمه':'مشتری'):'--انتخاب نوع--'}}</option>
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
                        <label>کدملی<input type="search" id="nationalCode" name="nationalCode" class="form-control input-sm" value="{{key_exists('national_code',$queryParam)?$queryParam['national_code']:''}}" placeholder="" aria-controls="example1">
                        </label>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="title">حوزه های همکاری</label>
                        <select id="fields"  name="fields[]"  class="selectpicker" multiple data-hide-disabled="true" >
                            @foreach($fields as $field)
                                <option   <?php if(key_exists('fields',$queryParam)){ foreach ($queryParam['fields'] as $item){ if ($item==$field->name) echo 'selected';} }?> value="{{$field->name}}"  >{{$field->name}}</option>
                            @endforeach

                        </select>

                    </div>
                    {{--<div id="example1_filter" class="dataTables_filter">--}}
                        {{--<select name="field" id="field" class="form-control" style="">--}}

                            {{--<option value="{{key_exists('field',$queryParam)?$queryParam['field']:''}}">{{key_exists('field',$queryParam)?(\App\Category::where('name',$queryParam['field'])->first()->name):'--انتخاب زمینه--'}}</option>--}}
                            {{--<select id="fields"  name="fields[]"  class="selectpicker" multiple data-hide-disabled="true" >--}}
                                {{--@foreach($fields as $field)--}}
                                    {{--<option value="{{$field->id}}" >{{$field->name}}</option>--}}
                                {{--@endforeach--}}

                            {{--</select>--}}

                        {{--</select>--}}

                    {{--</div>--}}
                </div>
                <div class="col-sm-2">
                    <div id="example1_filter" class="dataTables_filter">

                        <select name="sort" id="sort" class="form-control" style="">
                            <option value="{{key_exists('sort',$queryParam)?$queryParam['sort']:''}}">{{key_exists('sort',$queryParam)?($queryParam['sort']=='asc'?'بالا به پایین':'پایین به بال'):'--- دسته بندی بر اساس امتیاز ---'}}</option>

                            <option value="asc">بالا به پایین</option>
                            <option value="desc">پایین به بالا</option>

                        </select>

                    </div>
                </div>

                <div class="col-sm-2">
                    <div id="example1_filter" class="dataTables_filter">

                        <select name="gender"id="gender" class="form-control" style="">
                            <option value="{{key_exists('gender',$queryParam)?$queryParam['gender']:''}}">{{key_exists('gender',$queryParam)?($queryParam['gender']=='male'?'مرد':'زن'):'- انتخاب جنسیت -'}}</option>


                            <option value="female">زن</option>
                            <option value="male">مرد</option>

                        </select>

                    </div>
                </div>
                <div class="col-sm-2">
                    <div id="example1_filter" class="dataTables_filter">

                        <select name="adminStatus" id="adminStatus" class="form-control" style="">
                            <option value="{{key_exists('admin_status',$queryParam)?$queryParam['admin_status']:''}}">{{key_exists('admin_status',$queryParam)?($queryParam['admin_status']=='pending'?'منتظر تایید':($queryParam['admin_status']=='accept'?'تایید شده':'رد شده')):'--- وضعیت تایید ادمین ---'}}</option>


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
                            <option value="{{key_exists('availability_status',$queryParam)?$queryParam['availability_status']:''}}">{{key_exists('availability_status',$queryParam)?($queryParam['availability_status']=='available'?'اماده برای ارایه سرویس':'جارج از سرویس دهی'):'--- وضعیت قابلیت ارایه سرویس ---'}}</option>


                            <option value="available">اماده برای ارایه سرویس</option>
                            <option value="unavailable">خارج از سرویس دهی</option>

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
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">کیف پول</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">جزییات بیشتر و ویرایش</th>
                        </tr>
                        </thead>
                        <tbody id="tuserbody">
                        @foreach($users as $user)

                            <tr role="row" class="odd">
                                <td class="sorting_1">{{key_exists('name',$user)?$user->name:'تکمیل نشده'}}</td>
                                <td class="sorting_1">{{key_exists('family',$user)?$user->family:'تکمیل نشده'}}</td>
                                <td class="sorting_1">{{$user->phone_number}}</td>
                                <td>{{ $user->role=='client' ?'مشتری':'خدمه' }}</td>
                                <td class="sorting_1">{{count($user['wallet'])>0?$user['wallet'][0]['amount']:0}}</td>
                                <td><a href="{{route('admin.user.update',['user_id' => $user->_id])}}"><i class="fa fa-edit"></i></a></td>

                            </tr>

                        @endforeach


                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-5">
                    <div class="dataTables_info" id="example1_info" role="status" aria-live="polite">نشان دادن {{ count($users)>0 ?((($queryParam['page']-1)*10)+1):0 }} تا {{((($queryParam['page']-1)*10))+count($users)}}از{{$count}}</div>
                </div>
                <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example1_paginate">
                        <ul class="pagination">

                            @if($total_page>1)


                            @if($total_page>8)
                                    <li class="paginate_button active"><a href="{{route("admin.user.list",array_merge($queryParam,['page'=>1]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">1</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.user.list",array_merge($queryParam,['page'=>2]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">2</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.user.list",array_merge($queryParam,['page'=>3]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">3</a></li>
                                    <li class="paginate_button active"><a href="#" aria-controls="example1" data-dt-idx="1" tabindex="0">.</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.user.list",array_merge($queryParam,['page'=>$total_page-3]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$total_page-3}}</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.user.list",array_merge($queryParam,['page'=>$total_page-2]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$total_page -2}}</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.user.list",array_merge($queryParam,['page'=>$total_page-1]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$total_page -1}}</a></li>
                                    <li class="paginate_button active"><a href="{{route("admin.user.list",array_merge($queryParam,['page'=>$total_page]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$total_page }}</a></li>




                                @else
                                    @for($i=0;$i<$total_page ;$i++ )

                                    <li class="paginate_button active"><a href="{{route("admin.user.list",array_merge($queryParam,['page'=>$i+1]))}}" aria-controls="example1" data-dt-idx="1" tabindex="0">{{$i+1}}</a></li>

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
                var email = $( "#email" ).val();
                var mobile = $( "#mobile" ).val();
                var status = $( "#status" ).val();
                var role = $( "#typeOfuser" ).val();
                var national_code = $( "#nationalCode" ).val();
                var sort = $( "#sort" ).val();
                var fields = $( "#fields" ).val();
                var gender = $( "#gender" ).val();
                var admin_status = $( "#adminStatus" ).val();
                var availabilitystatus = $( "#availabilitystatus" ).val();


                var params = [];
                if(email)
                    params.push("email="+email)
                if(mobile)
                    params.push("phone_number="+mobile)

                if (status)
                    params.push("status="+status)
                if(role)
                {
                    params.push("role="+role)
                    console.log(role)
                    if (role=='worker')
                    {

                        if(sort)
                            params.push("sort="+sort)
                        if (national_code)
                            params.push("national_code="+national_code)
                        if (fields)
                            params.push("fields="+fields)
                        if (gender)
                            params.push("gender="+gender)
                        if (admin_status)
                            params.push("admin_status="+admin_status)
                        if (availabilitystatus)
                            params.push("availability_status="+availabilitystatus)
                    }

                }







                window.location.href =
                    "http://" +
                    window.location.host +
                    window.location.pathname +
                    '?' + params.join('&');


            });
        });
    </script>
    <script type="text/javascript">
        var x = document.getElementById("typeOfuser").value;
        if (x == "worker"){
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

    </script>

@endsection

