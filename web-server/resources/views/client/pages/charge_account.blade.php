@extends('client.template.client_template')

@section('content')
<div class="container">
<h1>شارژ حساب</h1>
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
         <hr>

         <label>رسید پیگیری</label>
         <div>
            {{ Session::get('ref_num') }}
         </div>
      </div>
   @endif


   <div class="container" style="width:400px;">
   <p class="text-center text-success">از طریق این فرم می توانید حساب کاربری خود را شارژ کنید</p>
      <form style="margin-top: 50px" method="post" action="{{route('charge_account_submit')}}">
         {{ csrf_field() }}
         <div class="col-sm-12">
            <input type="text" class="f-input form-group form-control" name="mobile_number" placeholder="شماره موبایل" />
            @if ($errors->has('mobile_number'))

               <span class="help-danger">
                                        <strong>{{ $errors->first('mobile_number') }}</strong>
                                    </span>
            @endif
         </div>
         <div class="col-sm-12">
            <input type="number" class="f-input form-group form-control" name="amount" placeholder="مبلغ (تومان)" />
            @if ($errors->has('amount'))
               <span class="help-danger">
                                        <strong>{{ $errors->first('amount') }}</strong>
                                    </span>
            @endif
         </div>
         <div class="submit col-sm-12">
            <input class="btn btn-success btn-group  btn-block"  type="submit" value="پرداخت" />
         </div>
      </form>


   </div>
</div>
@endsection