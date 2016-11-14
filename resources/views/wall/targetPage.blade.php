
@extends('layout.main')

@section('css')
    <link rel="stylesheet" href="{{asset('/css/front.css')}}">
@endsection

@include('layout.header')

@section('content')

    <div class="col-lg-8 col-md-8 col-sm-8" style="float: left">
        <iframe  frameborder="0" src="{{ url($target) }}" scrolling="auto"  width="100%"  height="100%" name="targetFrame" >
        </iframe>
    </div>
    <div  class="col-lg-4 col-md-4 col-sm-4"  style="float: left;align-content: center;background-color: #656568;min-height: 100%">

        <div class="container col-lg-12 col-md-12 col-sm-12" style="margin-top:5px;">
            <form action="{{ url('/write') }}" method="post">
                <div style="align-content: center">
                    <label class="" for="attainment" style="font-size: 25px;color:#2ca02c;">{{trans('admin_page.opinion')}}</label>
                    <div id="divEditor"></div>
                    <textarea class="col-lg-12 col-md-12 col-sm-12" name="attainment" id="attainment" cols="77" rows="10" required>
                        @if(!empty($old))
                            {{$old}}
                        @endif
                    </textarea>
                </div>

                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="target" value="{{ $target }}">
                <button class="btn col-lg-12 col-md-12 col-sm-12" type="submit">
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
    <script src="{{asset('/js/wangEditor-1.3.13')}}" type="text/javascript"></script>

    <script>
        $(function () {
            $('#divEditor').wangEditor({
                codeTargetId: 'txtCode',			  //将源码存储到txtCode
                frameHeight: '300px',				 //默认值为“300px”
                initWords: '欢迎使用！请输入文字...',  //默认值为“请输入...”
                showInfo: true						//是否显示“关于”菜单，默认显示
            });
        });
    </script>
@endsection