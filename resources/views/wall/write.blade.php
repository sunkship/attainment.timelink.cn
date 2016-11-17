
@extends('layout.main')

@section('css')
    <link rel="stylesheet" href="{{asset('/css/front.css')}}">
@endsection

@include('layout.header')

@section('content')

    <div>
        <iframe  frameborder="0" src="{{ url($target) }}" scrolling="auto"  width="100%"  height="100%" name="targetFrame" >
        </iframe>
    </div>
    <form action="{{ url('/write') }}" method="post">
        <label class="label" for="attainment" style="font-size: 30px;color:#2ca02c;">{{trans('admin_page.opinion')}}</label>
        <textarea name="attainment" id="attainment" required style="height: 20%; width: 80%;"></textarea>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="target" value="{{ $target }}">
        <button class="btn btn-login btn-block" type="submit">
            <i class="icon">
                {{ trans('admin_page.write') }}
            </i>
        </button>
    </form>


@endsection

@section('js')
    <script>
    </script>
@endsection