
@extends('layout.main')

@section('css')
    <link rel="stylesheet" href="{{asset('/css/front.css')}}">
@endsection

@include('layout.header')

@section('content')

    <div style="width: 70%;float: left">
        <iframe  frameborder="0" src="{{ url($target) }}" scrolling="auto"  width="100%"  height="100%" name="targetFrame" >
        </iframe>
    </div>
    <div style="width: 30%;float: left;align-content: center;background-color: #656568;min-height: 100%">
        <div class="form" style="padding-left: 3px">
            <form action="{{ url('/write') }}" method="post">
                <div style="align-content: center">
                    <label class="label" for="attainment" style="font-size: 30px;color:#2ca02c;">{{trans('admin_page.opinion')}}</label>
                    <textarea name="attainment" id="attainment" cols="76" rows="30" contenteditable="true" required></textarea>
                </div>

                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="target" value="{{ $target }}">
                <button class="btn btn-login btn-block" type="submit">
                    <i class="icon">
                        {{ trans('admin_page.write') }}
                    </i>
                </button>
            </form>
        </div>
    </div>

@endsection

@section('js')
    <script>
    </script>
@endsection