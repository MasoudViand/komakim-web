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
            <h3 class="box-title">سفارشی سازی قالب ایمیل </h3>

        </div><!-- /.box-header -->
        <div class="box-body pad">
            <form method="post" action="{{route('admin.create.email.template.submit')}}">

                {{ csrf_field() }}

                <div class="form-group" style="width:350px">
                    <label for="exampleInputEmail1">نام قالب </label>
                    <input  class="form-control" id="nameEmailTemplate" name="nameEmailTemplate" >
                    @if ($errors->has('nameEmailTemplate'))
                        <span class="help-danger">
                                        <strong>{{ $errors->first('nameEmailTemplate') }}</strong>
                                    </span>
                    @endif
                </div>
                <textarea  id="Template" name="Template" class="textarea" placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                @if ($errors->has('Template'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('Template') }}</strong>
                                    </span>
                @endif


                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
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

