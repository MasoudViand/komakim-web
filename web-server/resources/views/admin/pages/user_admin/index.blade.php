@extends('admin.template.admin_template')

@section('content')
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


            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                        <thead>
                        <tr role="row">
                            <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">نام کاربر</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">ایمیل</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">سطح دسترسی</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">ویرایش</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">حذف</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($userAdmins as $userAdmin)



                            <tr role="row" class="odd">
                                <td class="sorting_1">{{$userAdmin->name}}</td>
                                <td>{{$userAdmin->email}}</td>
                                <td>{{$userAdmin->role=='admin'?'مدیرکل':($userAdmin->role=='operator'?"اپراتور":'مالی')}}</td>
                                <td><a href="{{route('admin.user_admin.update',['user_admin_id' => $userAdmin->id])}}"><i class="fa fa-edit"></i></a></td>
                                <td><a href="{{route('admin.user_admin.delete',['user_admin_id' => $userAdmin->id])}}"  onclick="return confirm('ایا از حذف کاربر ادمین اطمینان دارید')"><i class="fa fa-remove"></i></a></td>

                            </tr>

                        @endforeach


                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">

                <a href="{{ route('admin.user_admin.insert') }}">
                <button class="btn btn-block btn-primary btn-lg">اضافه کردن کاربر ادمین</button>
                </a>

            </div>
        </div>
    </div>
@endsection