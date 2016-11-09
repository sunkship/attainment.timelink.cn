<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ trans('front.attainment') }}</title>

    <link href="{{asset('/fonts/css/font-awesome.min.css')}}" rel="stylesheet" />
    <link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/front.css') }}" rel="stylesheet">
    <script src="{{ asset('/js/jquery-1.10.2.min.js') }}"></script>
    @yield('css')
    <!--[if lt IE 9]>
        <script src="{{ asset('/js/html5shiv.js')}}"></script>
        <script src="{{ asset('/js/respond.min.js')}}"></script>
        <script src="{{ asset('/js/excanvas.js')}}"></script>
        <script src="{{ asset('/js/placeholders.js') }}"></script>
    <![endif]-->
</head>
<body>
<div class="content">
    @yield('header')
    <div class="wrapper">
        @yield('slide')
        @yield('describe')
        @yield('content')
    </div>
</div>
@yield('footer')

<script src="{{ asset('/js/bootstrap.min.js') }}"></script>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "//hm.baidu.com/hm.js?4f4f575e5a2139cb9bae2346d8328ca9";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();


    $(function () {
        function touch_event(click_obj,show_obj,tag) {
            var isSupportTouch = "ontouchend" in document ? true : false;
            if (isSupportTouch) {
                click_obj.removeClass(tag);
                click_obj.click(function () {
                    if (show_obj.css('display') == '' || show_obj.css('display') == 'none') {
                        show_obj.css('display', 'block');
                    } else {
                        show_obj.css('display', 'none');
                    }
                })
            } else {
                return;
            }
        }
        touch_event($('.loginsucess'),$('.personalinfobox'),'tag2');
        touch_event($('.wrap'),$('.wrap .pagenavbar ul li ul'),'tag1');
    })
</script>


@yield('js')
</body>
</html>

