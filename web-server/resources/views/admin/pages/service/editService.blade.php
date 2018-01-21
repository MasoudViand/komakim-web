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
                    <form role="form" method="POST" action="{{ route('admin.service.update.submit') }}">
                        {{ csrf_field() }}
                        <div id="subform" class="box-body">

                            <div class="form-group">
                                <label for="title">انتخاب دسته بندی</label>
                                <select name="category" class="form-control" style="width:350px">
                                    <option value="{{ $category->id }}">{{$category->name}}</option>
                                    @foreach ($categories as $category )
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="title">انتخاب زیر دسته بندی</label>
                                <select name="subcategory" class="form-control" style="width:350px">
                                    <option value="{{ $subcategory->id }}">{{$subcategory->name}}</option>
                                    @foreach ($subcategories as $subcategory )
                                        <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                    @endforeach

                                </select>
                            </div>

                            {{$service->name}}
                            <div class="form-group" style="width:350px">
                                <label for="exampleInputEmail1">نام سرویس </label>
                                <input  class="form-control" id="nameservice" name="nameService" value={{$service->name}}>
                                @if ($errors->has('nameService'))
                                    <span class="help-danger">
                                        <strong>{{ $errors->first('nameService') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <input  class="form-control"type="hidden" id="idService" name="idService" value={{$service->id}}>

                            <div class="form-group" style="width:350px">
                                <label for="exampleInputPassword1">قیمت سرویس</label>
                                <input  type="number" class="form-control" name="priceService" id="price" value={{$service->price}}>
                                @if ($errors->has('priceService'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('priceService') }}</strong>
                                    </span>
                                @endif

                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">واحد سرویس</label>
                                <input type="" class="form-control" name="unitService" id="unitService" placeholder="واحد سرویس">
                                @if ($errors->has('unitService'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('unitService') }}</strong>
                                    </span>
                                @endif

                            </div>
                            <div class="form-group" style="width:350px">
                                <label for="exampleInputPassword1">حداقل سفارش</label>
                                <input type="number" class="form-control" name="minOrderService" id="minOrder" value={{$service->minimum_number}}>
                                @if ($errors->has('minOrderService'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('minOrderService') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group" style="width:350px">
                                <label for="exampleInputPassword1">توضیحات </label>
                                <textarea class="form-control" name="descService" rows="5" id="desc" >{{$service->description}} </textarea>

                            </div>




                        </div><!-- /.box-body -->

                        <div class="box-footer" style="width:350px">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>


                </div>
            </div>


        </div><!-- /.col (LEFT) -->
        <div class="col-md-6">
            <div class="box box-primary">
                @foreach($questions as $question)
                    <div class="box-header with-border">
                        <h3 class="box-title">سوال</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-sm btn-danger"><a href="{{route('admin.service.question.delete',['question_id'=>$question->id])}}">حذف</a></button>
                        </div>
                    </div>
                    <div class="box-body">

                       {{$question->questions}}
                    </div>

                @endforeach
                    <form role="form" method="POST" action="{{ route('admin.service.question.insert.submit') }}">
                        {{ csrf_field() }}
                        <input  class="form-control"type="hidden" id="idService" name="idService" value={{$service->id}}>
                        <div id="subform" class="box-body">

                            <div class="form-group" style="width:350px">
                                <label for="exampleInputPassword1">اضافه کردن سوال جدید </label>
                                <textarea class="form-control" name="questionService" rows="5" id="desc" > </textarea>

                            </div>

                        </div><!-- /.box-body -->

                        <div class="box-footer" style="width:350px">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>


            </div>


        </div>
    </div>




    <script type="text/javascript">
        $(document).ready(function() {

            $('select[name="category"]').on('change', function() {
                var stateID = $(this).val();

                if(stateID) {
                    $.ajax({
                        url: '/admin/getsubcategory/'+stateID,
                        type: "GET",
                        dataType: "json",
                        success:function(data) {

                            console.log(data[0])


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