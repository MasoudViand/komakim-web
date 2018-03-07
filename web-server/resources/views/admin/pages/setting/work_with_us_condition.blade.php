@extends('admin.template.admin_template')

@section('content')


    @if(Session::has('success'))
        <div class="alert alert-sussecc" role="alert">
            <strong>
                {{ Session::get('success') }}
            </strong>
        </div>
    @endif


    <div class="box">
        <div class="box-header">
            <h3 class="box-title">سفارشی سازی قالب شرایط همکاری با ما </h3>

        </div><!-- /.box-header -->
        <div class="box-body pad">
            <form method="post" action="{{route('admin.work.with.us.condition.insert.submit')}}">

                {{ csrf_field() }}


                <textarea  id="workWithUsCondition" name="workWithUsCondition" class="textarea" placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{$text}}</textarea>
                @if ($errors->has('workWithUsCondition'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('workWithUsCondition') }}</strong>
                                    </span>
                @endif


                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">ثبت</button>
                </div>
            </form>

        </div>
    </div>







@endsection
@section('foot')
    <script src="https://cdn.ckeditor.com/4.4.3/standard/ckeditor.js"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="{{asset("AdminLTE-RTL/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js") }}"></script>
    <script>
        $(function () {


            //bootstrap WYSIHTML5 - text editor
            $(".textarea").wysihtml5();
        });
    </script>
@endsection

