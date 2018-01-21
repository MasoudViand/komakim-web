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
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 207px;">کد</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">نوع</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">مقدار</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">وضعیت</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 45px;">غیر فعال کردن</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($discountCodes as $discountCode)



                            <tr role="row" class="odd">
                                <td class="sorting_1">{{$discountCode->name}}</td>
                                <td>{{ $discountCode->type=='amount' ?'مقدار ثابت':'درصدی' }}</td>
                                <td>{{$discountCode->value}}</td>
                                <td>{{$discountCode->status ?'فعال':'غیر فعال'}}</td>
                                <td><a href="{{route('admin.discount_code.inactive',['discount_code_id' => $discountCode->id])}}"  onclick="return confirm('ایا از غیر فعال کردن کد تخفیف  اطمینان دارید')" {{$discountCode->status ?"": 'hidden'}}><i class="fa fa-remove"></i></a></td>

                            </tr>

                        @endforeach


                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-5">
                    <div class="dataTables_info" id="example1_info" role="status" aria-live="polite"> نشان دادن {{count($discountCodes)}} از {{$total_count}}</div>
                </div>
                <div class="col-sm-7">
                    <div class="row-lg-1 row-centered"> {{ $discountCodes->links() }}</div>
                </div>
                <a href="{{ route('admin.discount_code.insert') }}">
                <button class="btn btn-block btn-primary btn-lg">اضافه کردن کد تخفیف</button>
                </a>

            </div>
        </div>
    </div>
@endsection