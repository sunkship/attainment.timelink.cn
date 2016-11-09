<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <link rel="shortcut icon" href="{{ asset('/images/favicon.ico') }}" type="image/png">

    <title>@yield('title')</title>

    <link href="{{ asset('/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/style-responsive.css') }}" rel="stylesheet">

    @yield('css')

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
</head>

<body class="login-body">
@yield('content')
</body>

<script src="{{ asset('/js/jquery-1.10.2.min.js') }}"></script>
<script src="{{ asset('/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('/js/modernizr.min.js') }}"></script>

<script>
    $(document).ready(function(){
        $('#verify-code').bind('click',function(){
            $(this).attr('src','{{ url('captcha/flat') }}?'+ Math.floor(Math.random()*1000))
        });
    });
</script>
@yield('js')
</html>
