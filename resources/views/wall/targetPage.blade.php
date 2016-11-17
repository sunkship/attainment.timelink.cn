
@extends('layout.main')

@section('css')
    <link rel="stylesheet" href="{{asset('/css/front.css')}}">
@endsection

@include('layout.header')

@section('content')

    <iframe class="col-lg-12 col-md-12 col-sm-12" frameborder="0" src="{{ url($target) }}" width="100%"  height="400px" name="targetFrame" >
    </iframe>

    <div  class="col-lg-12 col-md-12 col-sm-12 col-xs-12"  style="align-content: center;background-color: #656568;min-height: 100%">
        <div class="container col-lg-12 col-md-12 col-sm-12  col-xs-12" style="margin-top:5px;">
            <form action="{{ url('/write') }}" method="post">
                <div style="align-content: center">
                    <div>
                        <label class="" for="attainment" style="font-size: 25px;color:#2ca02c;">{{trans('admin_page.opinion')}}</label>
                    </div>
                    <textarea class="col-lg-12 col-md-12 col-sm-12 col-xs-12" name="attainment" id="attainment"  rows="10" required>@if(!empty($old)){{$old}}@endif</textarea>
                </div>

                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="target" value="{{ $target }}">
                <button class="btn col-lg-12 col-md-12 col-sm-12 col-xs-12" type="submit">
                    {{ trans('admin_page.write') }}
                </button>
            </form>
        </div>

        <div class="container col-lg-12 col-md-12 col-sm-12" style="margin-top: 24px;">
        @for($i = 0;$i < sizeof($attainments);$i++)
            <div class="panel panel-primary">
                <div class="panel-heading">
                    {{$users[$i]->username}}
                </div>
                <div class="panel-body">
                    {{$attainments[$i]->content}}
                </div>
            </div>
        @endfor
        </div>
    </div>

@endsection

@section('js')
    <script>
        {{--var share_link='<?php echo $target?>';--}}
        {{--$.ajaxPrefilter( function (options) {--}}
            {{--if (options.crossDomain && jQuery.support.cors) {--}}
                {{--var http = (window.location.protocol === 'http:' ? 'http:' : 'https:');--}}
                {{--options.url = http + '//cors-anywhere.herokuapp.com/' + options.url;--}}
            {{--}--}}
        {{--});--}}

        {{--$.get(--}}
                {{--share_link,--}}
                {{--function (response) {--}}
                    {{--console.log("> ", response);--}}
                    {{--var html = response;--}}
                    {{--html=html.replace(/data-src/g, "src");--}}
                    {{--var html_src = 'data:text/html;charset=utf-8,' + html;--}}
                    {{--$("iframe").attr("src" , html_src);--}}
                {{--}--}}
        {{--);--}}
    </script>
@endsection