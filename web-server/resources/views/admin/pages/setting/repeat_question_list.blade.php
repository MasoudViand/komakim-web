@extends('admin.template.admin_template')

@section('content')

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
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">سوال</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">ویرایش</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($repeadQuestions as $item)



                        <tr role="row" class="odd">
                            <td class="sorting_1">{{$item->question}}</td>

                            <td><a href="{{route('admin.repeat.question.update',['repeat_question_id' => $item['id']])}}"><i class="fa fa-edit"></i></a></td>
                            <td><a href="{{route('admin.repeat.question.delete',['repeat_question_id' => $item->id])}}"  onclick="return confirm('ایا از حذف این  سوال متداول  اطمینان دارید')"><i class="fa fa-remove"></i></a></td>


                        </tr>

                    @endforeach


                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-5">
                {{--<div class="dataTables_info" id="example1_info" role="status" aria-live="polite"> نشان دادن {{count($mailsTemplate)}} از {{$total_count}}</div>--}}
            </div>
            <div class="col-sm-7">
                <div class="row-lg-1 row-centered"> {{ $repeadQuestions->links() }}</div>
            </div>
            <a href="{{ route('admin.repeat.question.insert') }}">
                <button class="btn btn-block btn-primary btn-lg">اضافه کردن سوال متداول جدید</button>
            </a>

        </div>
    </div>

@endsection

