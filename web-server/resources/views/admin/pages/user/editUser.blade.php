@extends('admin.template.admin_template')



@section('content')
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








    <div class="row">
        <div class="col-md-12">
            <!-- AREA CHART -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="user-panel">
                        @if($workerProfile)


                            <div class="text-center" >
                                <img src="{{ $filepath  }}" class="center-block img-responsive img-circle" style="width:100px">
                            </div>

                            <div class="text-info text-center" style="margin-top: 12px">

                               <a href="{{route('admin.user.review',['user_id' => $user->id])}}" >{{$meanReview}} </a>

                            </div>
                        @endif

                    </div>
                    <form role="form" method="POST" action="{{ route('admin.user.update.submit') }}">
                        {{ csrf_field() }}
                        <div id="subform" class="box-body">



                            <div class="form-group">
                                <label for="exampleInputEmail1">نام </label>
                                <input  class="form-control" id="nameUser" name="nameUser" value="{{$user->name}}">

                            </div>


                            <div class="form-group">
                                <label for="exampleInputEmail1">نام خانوادگی </label>
                                <input  class="form-control" id="familyUser" name="familyUser" value="{{$user->family}}">
                                @if ($errors->has('familyUser'))
                                    <span class="help-danger">
                                        <strong>{{ $errors->first('familyUser') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">شماره همراه </label>
                                <input  class="form-control" id="mobileUser" name="mobileUser" value="{{$user->phone_number}}">
                                @if ($errors->has('mobileUser'))
                                    <span class="help-danger">
                                        <strong>{{ $errors->first('mobileUser') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">نوع کاربر </label>
                                <label  class="form-control" id="roleUser"  >{{ $user->role=='client' ?'مشتری':'خدمه' }}</label>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">ایمیل </label>
                                <input  class="form-control" id="emailUser" name="emailUser" value="{{$user->email}}">
                                @if ($errors->has('emailUser'))
                                    <span class="help-danger">
                                        <strong>{{ $errors->first('emailUser') }}</strong>
                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">وضعیت </label>
                                <select class="form-control" id="status" name="status">
                                    <option value="{{$user->status=='active' ?'active':'inactive'}}">--- {{ $user->status=='active' ?'فعال':'غیرفعال' }} ---</option>
                                    <option value="active">فعال</option>
                                    <option value="inactive">غیر فعال</option>
                                </select>


                            </div>

                            <input  class="form-control" type="hidden" id="idUser" name="idUser" value="{{$user->id}}">



                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">ثبت</button>
                        </div>
                    </form>



                </div>
            </div>


        </div><!-- /.col (LEFT) -->
        <div class="col-md-12">
            <div class="box box-primary">
                @if($workerProfile)



                        {!! Form::open(array('route' => 'admin.worker.profile.update.submit','enctype' => 'multipart/form-data')) !!}


                        {{ csrf_field() }}
                        <div id="subform" class="box-body">



                            <div class="form-group">
                                <label for="exampleInputEmail1">کد ملی </label>
                                <input  class="form-control" id="nationCodeProfile" name="nationCodeProfile" value="{{$workerProfile->national_code}}">
                                @if ($errors->has('nationCodeProfile'))
                                    <span class="help-danger">
                                        <strong>{{ $errors->first('nationCodeProfile') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">شماره تلفن ثابت </label>
                                <input  class="form-control" id="phoneProfile" name="phoneProfile" value="{{$workerProfile->home_phone_number}}">
                                @if ($errors->has('phoneProfile'))
                                    <span class="help-danger">
                                        <strong>{{ $errors->first('phoneProfile') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">ادرس </label>
                                <input  class="form-control" id="addressProfile" name="addressProfile" value="{{$workerProfile->address}}">
                                @if ($errors->has('addressProfile'))
                                    <span class="help-danger">
                                        <strong>{{ $errors->first('addressProfile') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">تاریخ تولد  </label>
                                <input  class="form-control" id="birthdayProfile" name="birthdayProfile" value="{{$date}}">
                                @if ($errors->has('birthdayProfile'))
                                    <span class="help-danger">
                                        <strong>{{ $errors->first('birthdayProfile') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="title">حوزه های همکاری</label>
                                <select id="fields"  name="fields[]"  class="selectpicker" multiple data-hide-disabled="true" >
                                    @foreach($fields as $field)
                                        <option @foreach($workerProfile->fields as $item) @if( $item==$field->name) selected @endif  @endforeach >{{$field->name}}</option>
                                    @endforeach

                                </select>

                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">اخرین مدرک تحضیلی  </label>
                                <input  class="form-control" id="lastEducationProfile" name="lastEducationProfile" value="{{$workerProfile->last_education}}">
                                @if ($errors->has('lastEducationProfile'))
                                    <span class="help-danger">
                                        <strong>{{ $errors->first('lastEducationProfile') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="title">وضعیت ازدواج</label>
                                <select name="marriage_status" class="form-control" style="width:350px">
                                    <option value="{{$workerProfile->marriage_status}}">--- {{ $workerProfile->marriage_status=='married' ?'متاهل':'مجرد' }} ---</option>

                                    <option value="married">married</option>
                                    <option value="single">single</option>

                                </select>
                            </div>
                            <div class="form-group">
                                <label for="title">جنسیت</label>
                                <select name="gender" class="form-control" style="width:350px">
                                    <option value="{{$workerProfile->gender}}">--- {{ $workerProfile->gender=='male' ?'مرد':'زن' }} ---</option>

                                    <option value="male">مرد</option>
                                    <option value="female">زن</option>

                                </select>
                            </div>
                            <div class="form-group">
                                <label for="title">وضعیت فعالیت</label>
                                <select name="statusProfile" class="form-control" style="width:350px">
                                    <option value="{{$workerProfile->status}}">--- {{ $workerProfileStatus }} ---</option>

                                    <option value="pending">منتظر تایید</option>
                                    <option value="accept">قبول درخواست</option>
                                    <option value="reject">رد درخواست</option>

                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">سایر توان مندی ها</label>
                                <textarea rows="5"  class="form-control" id="anotherCapabilityProfile" name="anotherCapabilityProfile" >{{$workerProfile->another_capability}}</textarea>
                                @if ($errors->has('anotherCapabilityProfile'))
                                    <span class="help-danger">
                                        <strong>{{ $errors->first('anotherCapabilityProfile') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">دوره های گذرانده شده</label>
                                <textarea rows="5"  class="form-control" id="certificatesProfile" name="certificatesProfile" >{{$workerProfile->certificates}}</textarea>
                                @if ($errors->has('certificatesProfile'))
                                    <span class="help-danger">
                                        <strong>{{ $errors->first('certificatesProfile') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">سوابق کاری</label>
                                <textarea rows="5"  class="form-control" id="experienceProfile" name="experienceProfile" >{{$workerProfile->experience}}</textarea>
                                @if ($errors->has('experienceProfile'))
                                    <span class="help-danger">
                                        <strong>{{ $errors->first('experienceProfile') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">اپلود پروفایل </label>
                                {{--<input  class="form-control" type="file" id="imageProfile" name="imageProfile" >--}}
                                {!! Form::file('imageProfile', array('class' => 'image')) !!}
                                @if ($errors->has('imageProfile'))
                                    <span class="help-danger">
                                        <strong>{{ $errors->first('imageProfile') }}</strong>
                                    </span>
                                @endif
                            </div>



                            <input  class="form-control" type="hidden" id="idUser" name="idUser" value="{{$user->id}}">



                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">ثبت</button>
                        </div>
                    {{--</form>--}}
                    {!! Form::close() !!}


                @endif


            </div>


        </div>
    </div>
    <script>
        kamaDatepicker('birthdayProfile', { buttonsColor: "red" });



    </script>

@endsection

