
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
                    <div class="panel panel-primary" style="margin-top: 1%">
                        <div class="panel-heading">
                            写心得
                        </div>
                        <div class="panel-body">
                            <label for="input_target">输入目标网页：</label>
                            <textarea id="input_target" name="target" style="width: 60%" onchange="change_target()" required></textarea>
                            <div>
                                <a id="target" href="{{url('/newTarget')}}">
                                    <button class="btn btn-success">去写心得</button>
                                </a>
                            </div>
                        </div>
                    </div>
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
                        <div class="panel-body">
                            <a href="{{url('/target?target='.$attainment->url)}}">{{$attainment->url}}</a>
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
            var target = document.getElementById('target').href = '/newTarget?target='+ input;
        }
    </script>
@endsection