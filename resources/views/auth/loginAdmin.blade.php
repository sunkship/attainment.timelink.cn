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
    <form class="form-signin" action="{{ url('/login') }}" method="post">
        <div class="form-signin-heading text-center">
            <img src="{{ asset("/images/attainment.bmp") }}" height="70px" style="margin-top: 20px;" alt=""/>
            <p style="text-align: center; font-size: 20px; margin-top: 10px">{{ trans('admin_page.attainment_wall') }}</p>
        </div>
        <div class="login-wrap">
            @if (Session::has('flash_notification.message'))
                <div class="notice text-center">
                    <span class="text-{{ Session::get('flash_notification.level') }} text-center">{{ Session::get('flash_notification.message') }}</span>
                </div>
            @endif
            <input type="text" name="username" class="form-control" placeholder="{{ trans('admin_page.username') }}" id="" autofocus required value="{{ old('username') }}">
            <input type="password" name="password" class="form-control" placeholder="{{ trans('admin_page.password') }}" id="" required>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="input-group">
                    <input type="text" class="form-control" name="captcha" placeholder="{{ trans('admin_page.verify_code') }}" aria-describedby="basic-addon2" maxlength="4" required>
                    <a href="javascript:void (0);"  class="input-group-addon" id="basic-addon2"><img src="{{ captcha_src('flat') }}" id="verify-code"></a>
                </div>
            <button class="btn btn-login btn-block" type="submit">
                <i class="icon">
                    {{ trans('admin_page.login') }}
                </i>
            </button>
        </div>
    </form>
</div>
@endsection
