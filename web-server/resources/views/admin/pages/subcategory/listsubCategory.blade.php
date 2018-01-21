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

                <div class="col-sm-6">
                    <div id="example1_filter" class="dataTables_filter">
                        <label>جست جو:<input type="search" class="form-control input-sm" placeholder="" aria-controls="example1">
                        </label></div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                        <thead>
                        <tr role="row">
                            <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">دسته بندی</th>
                            <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;"> زیر دسته بندی</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">الویت</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">ویرایش</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">حذف</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($subcategoryArr as $subcategory)
                            <tr role="row" class="odd">
                                <td class="sorting_1">{{$subcategory['categoryName']}}</td>
                                <td class="sorting_1">{{$subcategory['subcategoryName']}}</td>
                                <td class="sorting_1">{{$subcategory['subcategoryOrder']}}</td>
                                <td><a href="{{route('admin.subcategory.update',['subcategory_id' => $subcategory['subcategoryId']])}}"><i class="fa fa-edit"></i></a></td>
                                <td><a href="{{route('admin.subcategory.delete',['subcategory_id' => $subcategory['subcategoryId']])}}"  onclick="return confirm('ایا از حذف زیر دسته بندی اطمینان دارید')"><i class="fa fa-remove"></i></a></td>

                            </tr>

                        @endforeach


                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-5">
                    <div class="dataTables_info" id="example1_info" role="status" aria-live="polite"> نشان دادن {{count($subcategoryArr)}} از {{$total_count}}</div>
                </div>
                <div class="col-sm-7">
                    <div class="row-lg-1 row-centered"> {{ $subcategories->links() }}</div>
                </div>
                <a href="{{ route('admin.subcategory.insert') }}">
                <button class="btn btn-block btn-primary btn-lg">اضافه کردن زیر دسته بندی</button>
                </a>

            </div>
        </div>
    </div>
@endsection