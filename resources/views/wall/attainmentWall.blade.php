
@extends('layout.main')

@section('css')
    <link rel="stylesheet" href="{{asset('/css/front.css')}}">
@endsection

@include('layout.header')

@section('content')
    <div class="container">
        <div class="main-frame">
            <div class="summery">
                <div class="container col-lg-12 col-md-12 col-sm-12">
                    @if(Auth::user()->id == 1)
                        <form action="{{url('/newTarget')}}">
                            <div class="panel panel-primary" style="margin-top: 1%">
                                <div class="panel-heading">
                                    写心得
                                </div>
                                <div class="panel-body">
                                    <div>
                                        <label for="name">输入链接名字：</label>
                                        <input type="text" name="name" id="name" required>
                                        <label for="date">选择链接日期：</label>
                                        <input type="date" name="date" id="date" required style="height: 24px">
                                    </div>
                                    <div>
                                        <label for="input_target">输入目标网页：</label>
                                        <textarea id="input_target" name="target" style="width: 80%" onchange="change_target()" required></textarea>
                                    </div>
                                    <div>
                                        <input type="hidden" name="target" id="target" value="">
                                        <button class="btn btn-success" type="submit">确认提交</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif
                    <hr style="color: #1b2128;width: 100%;">
                    <h4 style="text-align: center">_____最新心得_____</h4>
                    <hr/>
                    @foreach($attainments as $attainment)
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            {{$attainment->created_at}}
                            @if(Auth()->user()->username == 'admin')
                            <a href="{{url('/delete')}}" style="float: right"></a>
                            @endif
                        </div>
                        <div class="panel-body" style="overflow-wrap: break-word;">
                            <a href="{{url('/target?'.$attainment->url)}}">{{urldecode($attainment->url)}}</a>
                        </div>
                    </div>
                    @endforeach

                    {!! $attainments->links() !!}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        var input;
        function change_target() {
            input = document.getElementById('input_target').value;
            var target = document.getElementById('target').value = 'target='+ input;
        }
    </script>
@endsection