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
        <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">




            <div class="row">
                <div class="col-sm-12">
                    <table id="user_table" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                        <thead>
                        <tr role="row">
                            <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">نام و نام خانوادگی</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">امتیاز</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">دلایل عدم رضایت</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 189px;">توضیحات</th>
                        </tr>
                        </thead>
                        <tbody id="tuserbody">
                        @foreach($reviews as $review)

                            <tr role="row" class="odd">
                                <td class="sorting_1">{{$review['user']->name.'  '.$review['user']->family}}</td>
                                <td class="sorting_1">{{$review['score']}}</td>
{{--                                <td class="sorting_1">{{$review['reasons'][0]['reason']}}</td>--}}
                                <td class="sorting_1">
                                        @foreach($review['reasons'] as $item)
                                       {{$item->reason}}{{" ُ"}}
                                       @endforeach

                                  </td>
                                <td class="sorting_1">{{$review['desc']}}</td>

                            </tr>

                        @endforeach


                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-5">
                </div>
                <div class="col-sm-7">
                    <div class="row-lg-1 row-centered"> {{ $reviewModel->links() }}</div>
                </div>


            </div>
        </div>
    </div>



@endsection

