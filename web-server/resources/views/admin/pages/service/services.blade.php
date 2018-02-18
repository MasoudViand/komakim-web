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
        <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">

            <div class="row">
                <div class="col-sm-12">
                    <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                        <thead>
                        <tr role="row">
                            <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">دسته بندی</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">زیر دسته بندی</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">نام سرویس</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 139px;">قیمت پایه</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 139px;">کمسیون</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 102px;">حداقل سفارش</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 102px;">واحد سرویس</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 102px;">توضیحات</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">ویرایش</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">حذف</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($serviceArr as $item)



                            <tr role="row" class="odd">
                                <td class="sorting_1">{{$item['serviceCategoryName']}}</td>
                                <td>{{$item['serviceSubCategoryName']}}</td>
                                <td>{{$item['serviceName']}}</td>
                                <td>{{$item['servicePrice']}}</td>
                                <td>{{$item['serviceCommission']}}</td>
                                <td>{{$item['serviceMinimumNumber']}}</td>
                                <td>{{$item['serviceUnit']}}</td>
                                <td>{{$item['serviceDescription']}}</td>

                                <td><a href="{{route('admin.service.update',['service_id' => $item['id']])}}"><i class="fa fa-edit"></i></a></td>
                                <td><a href="{{route('admin.service.delete',['service_id' => $item['id']])}}"  onclick="return confirm('ایا از حذف سرویس اطمینان دارید')"><i class="fa fa-remove"></i></a></td>

                            </tr>

                        @endforeach


                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-5">
                    <div class="dataTables_info" id="example1_info" role="status" aria-live="polite"> نشان دادن {{count($serviceArr)}} از {{$total_count}}</div>
                </div>
                <div class="row-lg-1 row-centered"> {{ $services->links() }}</div>
                <a href="{{ route('admin.service.insert') }}">
                <button class="btn btn-block btn-primary btn-lg">اضافه کردن سرویس</button>
                </a>

            </div>
        </div>
    </div>
@endsection