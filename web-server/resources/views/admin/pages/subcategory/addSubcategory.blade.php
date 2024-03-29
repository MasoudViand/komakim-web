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
    @if(Session::has('success'))
        <div class="alert alert-success" role="alert">
            <strong>
                {{ Session::get('success') }}
            </strong>
        </div>
    @endif

<div class="box col-sm-6">
    <form role="form" method="POST" action="{{ route('admin.subcategory.insert.submit') }}">
        {{ csrf_field() }}
        <div id="subform" class="box-body">

        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">دسته بندی</label>
            <select name="idSubCategory" class="form-control" style="width:350px">

                @foreach( $categories as $category)
                <option value={{$category->id}}>{{$category->name}}</option>

                @endforeach

            </select>

        </div>



            <div class="form-group">
                <label for="exampleInputEmail1">نام زیر دسته بندی </label>
                <input  class="form-control" id="nameSubCategory" name="nameSubCategory" placeholder="نام زیر دسته بندی ">
                @if ($errors->has('nameSubCategory'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('nameSubCategory') }}</strong>
                                    </span>
                @endif

            <div class="form-group">
                <label for="exampleInputPassword1">الویت نمایش</label>
                <input type="" class="form-control" name="orderSubCategory" id="orderSubCategory" placeholder="الویت نمایش">
                @if ($errors->has('orderSubCategory'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('orderSubCategory') }}</strong>
                                    </span>
                @endif

            </div>


        </div><!-- /.box-body -->

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">ثبت</button>
        </div>
    </form>
</div>
<div class="clearfix"></div>

@endsection