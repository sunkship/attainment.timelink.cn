@extends('layout.login')

@section('title','')

@section('content')

<style>
    .language{
        display: inline-block;
        position: absolute;
        right: 10px;
        top: 0;
    }

    .language li{
        display: inline-block;
        color: white;
    }

    .language li .active{
        color:rgb(50,50,50);
    }

    .language li a{
        color: white;
        text-decoration: none;
    }


</style>
<div class="container">
    <ul class="language">
        {{--<li><a href="{{ (App::getLocale()=='zh')?"#":"?locale=zh" }}" class="{{ (App::getLocale()=='zh')?'active':'' }}">Chinese</a></li>--}}
        {{--<li>|</li>--}}
        {{--<li><a href="{{ (App::getLocale()=='en')?"#":"?locale=en" }}" class="{{ (App::getLocale()=='en')?'active':'' }}">English</a></li>--}}
    </ul>
    <div class="form-signin">
        <div class="form-signin-heading text-center">
            <img src="{{ asset("/images/attainment.bmp") }}" height="70px" style="margin-top: 20px;" alt=""/>
            <p style="text-align: center; font-size: 20px; margin-top: 10px">{{ trans('admin_page.attainment_wall') }}</p>
        </div>
        <div class="login-wrap">
            <a href="{{ url('/WechatLogin') }}">
                <button class="btn btn-login btn-block">
                    <i class="icon">
                        {{ trans('admin_page.Wechat_login') }}
                    </i>
                </button>
            </a>
        </div>
    </div>
</div>
@endsection
