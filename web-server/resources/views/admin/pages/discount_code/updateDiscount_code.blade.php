@extends('admin.template.admin_template')



@section('content')
    {{--<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <link href="http://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
    <style>
        body {
        }
        .rtl-col {
            float: right;
        }
        #bd-next-date2, #bd-prev-date2 {
            font-size: 20px;
        }
        .tooltip > .tooltip-inner {
            font-family: Vazir;
            font-size: 12px;
            padding: 4px;
            white-space: pre;
            max-width: none;
        }
        #options-table {
            border-collapse: collapse;
            width: 100%;
        }
        #options-table td, #options-table th {
            border: 1px solid #777;
            text-align: left;
            padding: 8px;
        }
        #options-table tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
    <link rel="stylesheet" href="{{asset("Persian-Jalali-Calendar-Data-Picker-Plugin-With-jQuery-kamaDatepicker/style/kamadatepicker.css") }}">
    <script src="{{asset("Persian-Jalali-Calendar-Data-Picker-Plugin-With-jQuery-kamaDatepicker/src/kamadatepicker.js") }}"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <link rel="stylesheet" href="{{asset("bootstrap-select-1.12.4/dist/css/bootstrap-select.css") }}">
    <script src="{{asset("bootstrap-select-1.12.4/dist/js/bootstrap-select.js") }}"></script>


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

<div class="box col-xs-6">
    <form role="form" method="POST" action="{{ route('admin.discount.update.submit') }}">
        {{ csrf_field() }}
        <div id="subform" class="box-body">


            <input type="hidden" value="{{$discount->id}}" name="discountId">



            <div class="form-group">
                <label for="exampleInputEmail1">کد </label>
                <input  class="form-control" id="discount_code" name="discount_code" disabled value="{{$discount->name}}">
                @if ($errors->has('discount_code'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('discount_code') }}</strong>
                                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">حداگثر تعدا استفاده </label>
                <input type="number" class="form-control" id="total_use_limit" name="total_use_limit" value="{{$discount->total_use_limit}}">
                @if ($errors->has('حداگثر تعدا استفاده '))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('total_use_limit') }}</strong>
                                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">تاریخ انقضا</label>
                <input type="text" class="form-control" id="expired_at" name="expired_at" value="{{$discount->expired_at=='unlimited'?'':$discount->expired_at}}" >
                @if ($errors->has('حداگثر تعدا استفاده '))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('expired_at') }}</strong>
                                    </span>
                @endif
            </div>


            <div class="form-group">
                <label for="exampleInputEmail1"  id="fields" >دسته بندی های هدف</label>



                    <select id="fields"  name="fields[]" class="selectpicker" multiple data-hide-disabled="true"  >
                        @foreach($fields as $field)

                            {{--<option value="{{$field->id}}">{{$field->name}}</option>--}}

                            <option  value="{{$field->id}}" @if ($discount->fields!='unlimited' ) @foreach($discount->fields as $item) @if( $item==$field->id) selected @endif  @endforeach @endif >{{$field->name}}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('fields'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('fields') }}</strong>
                                    </span>
                    @endif
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">حداکثر تخفیف</label>
                <input type="number" class="form-control" id="upper_limit_use" name="upper_limit_use" value="{{$discount->upper_limit_use}}">
                @if ($errors->has('upper_limit_use'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('upper_limit_use') }}</strong>
                                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">حداکثر استفاده برای هر کاربر</label>
                <input type="number" class="form-control" id="user_limit" name="user_limit" value="{{$discount->user_limit}}">
                @if ($errors->has('user_limit'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('user_limit') }}</strong>
                                    </span>
                @endif
            </div>



            <div class="form-group">
                <label for="exampleInputPassword1">نوع</label>
                <select name="type" class="form-control" style="width:350px">
                    <option  @if($discount->type ==\App\DiscountCode::CONST_AMOUNT_TYPE) selected @endif value="{{\App\DiscountCode::CONST_AMOUNT_TYPE}}">مقدار ثابت</option>
                    <option @if($discount->type ==\App\DiscountCode::PERCENT_TYPE) selected @endif value="{{\App\DiscountCode::PERCENT_TYPE}}">درصدی</option>

                </select>

            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">وضعیت</label>
                <select name="status"   class="form-control" style="width:350px">
                    <option  @if($discount->status) selected @endif value="true">فعال</option>
                    <option @if(!$discount->status) selected @endif value="false">غیر فعال</option>

                </select>

            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">مقدار </label>
                <input  class="form-control" id="value" name="value" type="number" value="{{$discount->value}}">
                @if ($errors->has('value'))
                    <span class="help-danger">
                                        <strong>{{ $errors->first('value') }}</strong>
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
    <script>
        kamaDatepicker('expired_at', { buttonsColor: "red" });

    </script>

@endsection