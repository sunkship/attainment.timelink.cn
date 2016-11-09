
@extends('layout.main')

@section('css')
    <link rel="stylesheet" href="{{asset('/css/front.css')}}">
@endsection

@include('layout.header')

@section('content')
    <div class="container" style="height: 800px;;overflow:scroll;">
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>用户</th>
                @foreach($users as $user)
                    <th>{{$user->username}}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @for($i = 0;$i < 30;$i++)
                <tr>
                    <td>{{$recentDays[$i]}}</td>
                    @for($j = 0;$j < count($users);$j++)
                        <td>{{$attainmentCount[$j][$i]}}</td>
                    @endfor
                </tr>
            @endfor
            </tbody>
        </table>
    </div>
@endsection

@section('js')
    <script>

    </script>
@endsection