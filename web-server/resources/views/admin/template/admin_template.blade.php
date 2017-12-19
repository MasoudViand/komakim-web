<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<meta charset="UTF-8">
<title>AdminLTE 2 | Dashboard</title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<!-- Bootstrap 3.3.4 -->
<link rel="stylesheet" href="{{asset("AdminLTE-RTL/bootstrap/css/bootstrap.min.css") }}">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{asset("https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css") }}">
<!-- Ionicons 2.0.0 -->
<link rel="stylesheet" href="{{asset("https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css") }}">
<!-- Theme style -->
<link rel="stylesheet" href="{{asset("AdminLTE-RTL/dist/css/AdminLTE.min.css") }}">
<!-- AdminLTE Skins. Choose a skin from the css/skins
     folder instead of downloading all of them to reduce the load. -->
<link rel="stylesheet" href="{{asset("AdminLTE-RTL/dist/css/skins/_all-skins.min.css") }}">
<!-- iCheck -->
<link rel="stylesheet" href="{{asset("AdminLTE-RTL/plugins/iCheck/flat/blue.css") }}">
<!-- Morris chart -->
<link rel="stylesheet" href="{{asset("AdminLTE-RTL/plugins/morris/morris.css") }}">
<!-- jvectormap -->
<link rel="stylesheet" href="{{asset("AdminLTE-RTL/plugins/jvectormap/jquery-jvectormap-1.2.2.css") }}">
<!-- Date Picker -->
<link rel="stylesheet" href="{{asset("AdminLTE-RTL/plugins/datepicker/datepicker3.css") }}">
<!-- Daterange picker -->
<link rel="stylesheet" href="{{asset("AdminLTE-RTL/plugins/daterangepicker/daterangepicker-bs3.css") }}">
<!-- bootstrap wysihtml5 - text editor -->
<link rel="stylesheet" href="{{asset("AdminLTE-RTL/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css") }}">

<link rel="stylesheet" href="{{asset("AdminLTE-RTL/dist/fonts/fonts-fa.css") }}">
<link rel="stylesheet" href="{{asset("AdminLTE-RTL/dist/css/bootstrap-rtl.min.css") }}">
<link rel="stylesheet" href="{{asset("AdminLTE-RTL/dist/css/rtl.css") }}">
@yield('head')
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

<![endif]-->
</head>

<body class="skin-blue">
<div class="wrapper">

    <!-- Header -->
@include('admin.template.header')

<!-- Sidebar -->
@include('admin.template.sidebar')

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                {{ $page_title or "Page Title" }}
                <small>{{ $page_description or null }}</small>
            </h1>
            <!-- You can dynamically generate breadcrumbs here -->
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                <li class="active">Here</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Your Page Content Here -->
            @yield('content')
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <!-- Footer -->
    @include('admin.template.footer')

</div><!-- ./wrapper -->

<!-- jQuery 2.1.4 -->
<script src="{{asset("AdminLTE-RTL/plugins/jQuery/jQuery-2.1.4.min.js") }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.4 -->
<script src="{{asset("AdminLTE-RTL/bootstrap/js/bootstrap.min.js") }}"></script>
<!-- Morris.js charts -->
<script src="{{asset("AdminLTE-RTL/https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js") }}"></script>
<script src="{{asset("AdminLTE-RTL/plugins/morris/morris.min.js") }}"></script>
<!-- Sparkline -->
<script src="{{asset("AdminLTE-RTL/plugins/sparkline/jquery.sparkline.min.js") }}"></script>
<!-- jvectormap -->
<script src="{{asset("AdminLTE-RTL/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js") }}"></script>
<script src="{{asset("AdminLTE-RTL/plugins/jvectormap/jquery-jvectormap-world-mill-en.js") }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{asset("AdminLTE-RTL/plugins/knob/jquery.knob.js") }}"></script>
<!-- daterangepicker -->
<script src="{{asset("https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js") }}"></script>
{{--<script src="{{asset("AdminLTE-RTL/plugins/daterangepicker/daterangepicker.js") }}"></script>--}}
<!-- datepicker -->
{{--<script src="{{asset("AdminLTE-RTL/plugins/datepicker/bootstrap-datepicker.js") }}"></script>--}}
<!-- Bootstrap WYSIHTML5 -->
<script src="{{asset("AdminLTE-RTL/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js") }}"></script>
<!-- Slimscroll -->
<script src="{{asset("AdminLTE-RTL/plugins/slimScroll/jquery.slimscroll.min.js") }}"></script>
<!-- FastClick -->
<script src="{{asset("AdminLTE-RTL/plugins/fastclick/fastclick.min.js") }}"></script>
<!-- AdminLTE App -->
<script src="{{asset("AdminLTE-RTL/dist/js/app.min.js") }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{asset("AdminLTE-RTL/dist/js/pages/dashboard.js") }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset("AdminLTE-RTL/dist/js/demo.js") }}"></script>
@yield('foot')

</body>
</html>