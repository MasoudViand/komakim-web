@extends('admin.template.admin_template')

@section('content')
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>




    @if(Session::has('error'))
        <div class="alert alert-danger" role="alert">
            <strong>
                {{ Session::get('error') }}
            </strong>
        </div>
    @endif
    @if(Session::has('success'))
        <div class="alert alert-success" role="alert">
            <strong>
                {{ Session::get('success') }}
            </strong>
        </div>
    @endif


    <form role="form" method="POST" action="{{ route('admin.service.insert.submit') }}">
        {{ csrf_field() }}
        <div id="subform" class="box-body">

            <div class="form-group">
                <label for="title">انتخاب دسته بندی</label>
                <select name="category" class="form-control" style="width:350px">
                    <option value="">--- انتخاب دسته بندی ---</option>
                    @foreach ($categoris as $category )
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="title">انتخاب زیر دسته بندی</label>
                <select name="subcategory" class="form-control" style="width:350px">
                    @if ($errors->has('subcategory'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('subcategory') }}</strong>
                                    </span>
                    @endif
                </select>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">نام سرویس </label>
                <input  class="form-control" id="nameservice" name="nameService" placeholder="نام سرویس">
                @if ($errors->has('nameService'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('nameService') }}</strong>
                                    </span>
                @endif
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">قیمت سرویس</label>
                <input  type="number" class="form-control" name="priceService" id="price" placeholder="قیمت سرویس">
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
            <div class="form-group">
                <label for="exampleInputPassword1">حداقل سفارش</label>
                <input type="number" class="form-control" name="minOrderService" id="minOrder" placeholder="حداقل سفارش">
                @if ($errors->has('minOrderService'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('minOrderService') }}</strong>
                                    </span>
                @endif

            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">توضیحات </label>
                <textarea class="form-control" name="descService" rows="5" id="desc"></textarea>

            </div>




        </div><!-- /.box-body -->

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>





    <script type="text/javascript">
        $(document).ready(function() {

            $('select[name="category"]').on('change', function() {
                var stateID = $(this).val();

                if(stateID) {
                    $.ajax({
                        url: 'subcategory/'+stateID,
                        type: "GET",
                        dataType: "json",
                        success:function(data) {

                            console.log(data[0])


                            $('select[name="city"]').empty();
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


@endsection