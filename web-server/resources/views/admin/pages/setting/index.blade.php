@extends('admin.template.admin_template')

@section('content')
    {{--<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>


    <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">

        <div class="row">
            <div class="col-sm-12">

                <table id="user" class="table table-bordered table-striped" style="clear: both">
                    <tbody>
                    <tr>
                        <td width="35%">شعاع جست جو</td>
                        <td width="35%" id="radius_main">{{$radius}}</td>
                        <td width="65%"><button class="btn btn-primary" id="edit_radius_button"> ویرایش</button> <input hidden type="number" id="input_radius_search"><div id="div_register_radius_button" hidden><button  id="register_radius_button" class="btn btn-primary" >ثبت</button></div></td>
                    </tr>
                    <tr>
                        <td width="35%">کمسیون</td>
                        <td width="35%" id="commission_main">{{$commission}}</td>
                        <td width="65%"><button class="btn btn-primary" id="edit_commission_button"> ویرایش</button> <input hidden type="number" id="input_commission_search"><div id="div_register_commission_button" hidden><button  id="register_commission_button" class="btn btn-primary" >ثبت</button></div></td>
                    </tr>


                    </tbody>
                </table>

            </div>



        </div>


    </div>
    <script>
        $(document).ready(function() {

            $( "#edit_radius_button" ).click(function () {

                $("#edit_radius_button").hide()
                $("#input_radius_search").show()
                $("#div_register_radius_button").show()

            })
            $( "#edit_commission_button" ).click(function () {

                $("#edit_commission_button").hide()
                $("#input_commission_search").show()
                $("#div_register_commission_button").show()

            })
            $( "#register_radius_button" ).click(function () {

                var radius_value = $( "#input_radius_search" ).val();

                var data ={};

                data.radius=(radius_value);

                var myJSON = JSON.stringify(data);
                console.log(myJSON);
                $.ajax({
                    type: "POST",
                    url: 'setting/radius/edit',
                    data :myJSON,
                    success:function(data) {

                        radius = data.setting.value
                        $( "#radius_main" ).text(radius);
                        $("#edit_radius_button").show()
                        $("#input_radius_search").hide()
                        $("#div_register_radius_button").hide()


                    },
                    dataType: "json"
                });
;

            })
            $( "#register_commission_button" ).click(function () {

                var commission_value = $( "#input_commission_search" ).val();

                var data ={};

                data.commission=(commission_value);

                var myJSON = JSON.stringify(data);
                console.log(myJSON);
                $.ajax({
                    type: "POST",
                    url: 'setting/commission/edit',
                    data :myJSON,
                    success:function(data) {

                        radius = data.setting.value
                        $( "#commission_main" ).text(radius);
                        $("#edit_commission_button").show()
                        $("#input_commission_search").hide()
                        $("#div_register_commission_button").hide()


                    },
                    dataType: "json"
                });
                ;

            })

        });
    </script>

@endsection

