<header>
    <div class="header">
        <div class="container">
            <div style="margin-top: 10px">
                <a href="" class="pull-right" style="margin-left: 30px;margin-right: 30px">|</a>
                <ul class="pull-right" style="color: #98fdf4;">
                    @if(Auth::user()->id == 1)
                        <li><a href="{{ url('/logout') }}" >{{ trans('front.logout') }}</a></li>
                    @else
                        <li><a href="{{ url('/login') }}" >{{ trans('front.login') }}</a></li>
                    @endif
                </ul>
            </div>

            <a href="" class="pull-right" style="margin-left: 30px;margin-right: 30px">|</a>
            <a href="{{url('/table')}}" class="pull-right" style="color: #8075c4;">
                本月心得
            </a>

            <a href="" class="pull-right" style="margin-left: 30px;margin-right: 30px">|</a>
            <a href="{{url('/wall')}}" class="pull-right" style="color: #2ca02c">
                {{ trans('front.attainment') }}
            </a>
            <a href="" class="pull-right" style="margin-left: 30px;margin-right: 30px">|</a>

        </div>
    </div>
</header>


<script>

</script>