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

        <div class="row">
            <div class="col-sm-3">
                <label for="title">انتخاب دسته بندی</label>
                <select name="category" id="category" class="form-control" style="width:auto">
                    <option value="{{key_exists('category_id',$queryParam)?$queryParam['category_id']:''}}">{{key_exists('category_name',$queryParam)?$queryParam['category_name']:'--انتخاب دسته بندی--'}}</option>
                    @foreach ($categories as $category )
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-3">
                <label for="title">انتخاب زیر دسته بندی</label>
                <select name="subcategory" class="form-control"  id="subcategory">
                    <option value="{{key_exists('subcategory_id',$queryParam)?$queryParam['subcategory_id']:''}}">{{key_exists('subcategory_name',$queryParam)?$queryParam['subcategory_name']:'--انتخاب دسته بندی--'}}</option>
                    @if(!is_null($subcategories))
                    @foreach ($subcategories as $subcategory )
                        <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                    @endforeach

                        @endif


                </select>
            </div>
            <div class="col-sm-3">
                <label for="exampleInputEmail1">نام سرویس </label>
                <input  class="form-control" id="service_name" name="nameCategory" value="{{key_exists('service_name',$queryParam)?$queryParam['service_name']:''}}" >

            </div>

            <div class="col-sm-3">
                <button class="btn btn-primary" id="btn_search_service">اعمال</button>
            </div>
        </div>
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
    <script type="text/javascript">
        $(document).ready(function() {

            $('select[name="category"]').on('change', function() {
                var stateID = $(this).val();

                console.log(stateID)

                if(stateID) {
                    $.ajax({
                        url: 'service/subcategory/'+stateID,
                        type: "GET",
                        dataType: "json",
                        success:function(data) {




                            $('select[name="subcategory"]').empty();

                            $('select[name="subcategory"]').append('<option value="">'+ "--زیر دسته بندی را انتخاب کنید--" +'</option>')
                            $.each(data, function(key, value) {
                                $('select[name="subcategory"]').append('<option value="'+ value._id +'">'+ value.name +'</option>');
                            });

                        }
                    });
                }else{
                    $('select[name="subcategory"]').empty();
                }
            });

            $( "#btn_search_service" ).click(function() {
                var category_id = $( "#category" ).val();
                var subcategory_id = $( "#subcategory" ).val();
                var service_name = $( "#service_name" ).val();


                var params = [];

                if(category_id)
                    params.push("category_id="+category_id)
                if(subcategory_id)
                    params.push("subcategory_id="+subcategory_id)

                if (service_name)
                    params.push("service_name="+service_name)

                window.location.href =
                    "http://" +
                    window.location.host +
                    window.location.pathname +
                    '?' + params.join('&');


            });
        });
    </script>

@endsection