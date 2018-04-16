<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>کمکیم</title>
{{--    <link rel="stylesheet" href="{{asset("client/css/bootstrap.min.css") }}">--}}
    <link rel="stylesheet" href="{{asset("AdminLTE-RTL/bootstrap/css/bootstrap.min.css") }}">

    <link rel="stylesheet" href="{{asset("client/css/bootstrap-rtl.min.css") }}">
    <link rel="stylesheet" href="{{asset("client/css/style.css") }}">
    <script src="{{asset("AdminLTE-RTL/plugins/jQuery/jQuery-2.1.4.min.js") }}"></script>

    @yield('header')
</head>





<body >


@include('client.template.header')


@yield('content')

@include('client.template.footer')


@yield('foot')

</body>
</html>