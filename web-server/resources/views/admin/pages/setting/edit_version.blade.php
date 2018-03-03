@extends('admin.template.admin_template')

@section('content')
    {{--<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>





    @if(Session::has('error'))
        <div class="alert alert-danger" role="alert">
            <strong>
                {{ Session::get('error') }}
            </strong>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <!-- AREA CHART -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <form role="form" method="POST" action="{{ route('admin.edit.version.submit') }}">
                        {{ csrf_field() }}
                        <div id="subform" class="box-body">
                            <div class="form-group" style="width:350px">
                                <label for="exampleInputPassword1">نوع اپلیکایشن</label>
                                <input  type="text" class="form-control"  readonly value={{$version->app_type==\App\User::WORKER_ROLE?'خدمه':'مشتری'}} >


                            </div>





                            <div class="form-group" style="width:350px">
                                <label for="exampleInputEmail1">ورژن </label>
                                <input  class="form-control" id="version" name="version" value="{{$version->version}}">

                            @if ($errors->has('version'))
                                    <span class="help-danger">
                                        <strong>{{ $errors->first('version') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <input  class="form-control"type="hidden" id="idVersion" name="idVersion" value={{$version->id}}>

                            <div class="form-group" style="width:350px">
                                <label for="exampleInputPassword1">ادرس دانلود</label>
                                <input  type="text" class="form-control" name="download_url" id="download_url" value={{$version->download_url}}>
                                @if ($errors->has('download_url'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('download_url') }}</strong>
                                    </span>
                                @endif


                            </div>


                            <div class="form-group">
                                <label for="exampleInputPassword1">به روز رسانی اجباری</label>
                                <select name="force_update" class="form-control" style="width:350px">
                                    <option value="{{$version->force_update?'true':'false'}}">{{$version->force_update?'فعال':'غیرفعال'}}</option>
                                    <option value="{{!($version->force_update)?'true':'false'}}">{{!$version->force_update?'فعال':'غیرفعال'}}</option>
                                </select>

                            </div>



                        </div><!-- /.box-body -->

                        <div class="box-footer" style="width:350px">
                            <button type="submit" class="btn btn-primary">ثبت</button>
                        </div>
                    </form>


                </div>
            </div>


        </div><!-- /.col (LEFT) -->
        <div class="col-md-6">


        </div>
    </div>




    <script type="text/javascript">
        $(document).ready(function() {

            $('select[name="category"]').on('change', function() {
                var stateID = $(this).val();

                if(stateID) {
                    $.ajax({
                        url: "{{URL::to('/')}}"+"/admin/service/subcategory/"+stateID,
                        type: "GET",
                        dataType: "json",
                        success:function(data) {




                            $('select[name="subcategory"]').empty();
                            $.each(data, function(key, value) {
                                $('select[name="subcategory"]').append('<option value="'+ value._id +'">'+ value.name +'</option>');
                            });

                        }
                    });
                }else{
                    $('select[name="subcategory"]').empty();
                }
            });
        });
    </script>
    <script>
        $(document).ready(function(){
            $("#btn1").click(function(){
               var question = $("#question").val();


                $("#subform").append("<b>"+question+"</b></br>");
               // $("p").append(" <b>Appended text</b>.");
            });

        });
    </script>




@endsection