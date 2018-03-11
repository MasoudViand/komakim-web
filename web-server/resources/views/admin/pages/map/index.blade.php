@extends('admin.template.admin_template')



@section('content')
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <link rel="stylesheet" href="{{asset("bootstrap-select-1.12.4/dist/css/bootstrap-select.css") }}">
    <script src="{{asset("bootstrap-select-1.12.4/dist/js/bootstrap-select.js") }}"></script>
    <div class="box-body">

        @if(Session::has('success'))
            <div class="alert alert-sussecc" role="alert">
                <strong>
                    {{ Session::get('success') }}
                </strong>
            </div>
        @endif
            @if(Session::has('error'))
                <div class="alert alert-sussecc" role="alert">
                    <strong>
                        {{ Session::get('error') }}
                    </strong>
                </div>
            @endif
            <div class="row">
                <div class="col-sm-2">
                    <div id="example1_filter" class="dataTables_filter form-inline">
                        <label>تلفن همراه</label>
						<input type="search"id="mobile" class="form-control" value="{{key_exists('phone_number',$queryParam)?$queryParam['phone_number']:''}}" placeholder="" aria-controls="example1">
                        
                    </div>
                </div>
                <div class="col-sm-2">
                    <div id="example1_filter" class="dataTables_filter form-inline">
                        <label>کدملی</label>
						<input type="search" id="nationalCode" name="nationalCode" class="form-control" value="{{key_exists('national_code',$queryParam)?$queryParam['national_code']:''}}" placeholder="" aria-controls="example1">
                        
                    </div>
                </div>
				
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>حوزه های همکاری</label>
                        <select id="fields"  name="fields[]"  class="selectpicker" multiple data-hide-disabled="true" >
                            @foreach($fields as $field)
                                <option   <?php if(key_exists('fields',$queryParam)){ foreach ($queryParam['fields'] as $item){ if ($item==$field->name) echo 'selected';} }?> value="{{$field->name}}"  >{{$field->name}}</option>
                            @endforeach

                        </select>

                    </div>

                </div>
                <div class="col-sm-2">
                    <div id="example1_filter" class="dataTables_filter">

                        <select name="gender"id="gender" class="form-control" style="">
                            <option value="{{key_exists('gender',$queryParam)?$queryParam['gender']:''}}">{{key_exists('gender',$queryParam)?($queryParam['gender']=='male'?'مرد':'زن'):'- انتخاب جنسیت -'}}</option>


                            <option value="female">زن</option>
                            <option value="male">مرد</option>

                        </select>

                    </div>
                </div>
                <div class="col-sm-1">

                    <button id="search_filter" class="btn btn-primary" > جست جو</button>

                </div>
                <div class="clearfix"></div><br>
                <div id="nodataLabel" > <p> کاربری برای نمایش وجود ندارد ، فیلتر جستجو را بررسی کنید</p></div>
				<br>
            </div>

            <div id="map" style="height: 400px; width: 100%;direction: ltr;"></div>


    </div>




        <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">

                <script type="text/javascript">
                    $(document).ready(function() {

                        $( "#search_filter" ).click(function() {
                            var mobile = $( "#mobile" ).val();
                            var national_code = $( "#nationalCode" ).val();
                            var fields = $( "#fields" ).val();
                            var gender = $( "#gender" ).val();


                            var params = [];

                            if(mobile)
                                params.push("phone_number="+mobile)
                            if (national_code)
                                params.push("national_code="+national_code)
                            if (fields)
                                params.push("fields="+fields)
                            if (gender)
                                params.push("gender="+gender)



                            window.location.href =
                                "http://" +
                                window.location.host +
                                window.location.pathname +
                                '?' + params.join('&');


                        });
                    });
                </script>



                <script>

                    function initMap() {

                        var map = new google.maps.Map(document.getElementById('map'), {
                            zoom: 10,
                            center: locations[0]
                        });

                        // Create an array of alphabetical characters used to label the markers.
                        var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

                        // Add some markers to the map.
                        // Note: The code uses the JavaScript Array.prototype.map() method to
                        // create an array of markers based on a given "locations" array.
                        // The map() method here has nothing to do with the Google Maps API.

                        var infowindow = new google.maps.InfoWindow();

//                        var markers = locations.map(function(location, i) {
//                            return new google.maps.Marker({
//                                position: location,
//                                label: labels[i % labels.length]
//                            });
//                        });

                        locations.forEach(function (element) {
                            console.log(element['lat']);
                            x =document.getElementById("nodataLabel");

                            if (x.style.display="none")
                            {

                            }

                            marker = new google.maps.Marker({
                                position: new google.maps.LatLng(element['lat'], element['lng']),
                                map: map
                            });
//
                            google.maps.event.addListener(marker, 'click', function() {
                            infowindow.setContent(element['name']);
                            infowindow.open(map, this);
                        });

                        })
//

//

                        // Add a marker clusterer to manage the markers.
                        var markerCluster = new MarkerClusterer(map, markers,
                            {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
                    }
//

                    var labels= ("{{ json_encode($locations) }}");


                    locations =JSON.parse(labels.replace(/&quot;/g,'"'));








                </script>
                <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
                </script>
                <script async defer
                        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyApwOOZWsVcjRTQAwYmNhGUIbrgPvUafho&callback=initMap">
                </script>


            </div>
            <div class="row">
                <div class="col-sm-12">
                </div>
            </div>
            <div class="row">



            </div>
        </div>
    </div>
@endsection