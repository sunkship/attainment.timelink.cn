@extends('admin.layout.right')

@section('content')
        <!--body wrapper start-->
<div class="wrapper">
    <div class="row">
        <div class="col-sm-8">
            <section class="panel">
                <header class="panel-heading">
                    {{ trans('admin_page.change_password') }}
                </header>
                <div class="panel-body">
                    @include('admin.error.create')
                    <form role="form" action="{{ url('/admin/auth/password') }}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label for="InputName">{{ trans('admin_page.origin_password') }}</label>
                            <input type="password" name="origin_password" class="form-control"  id="InputName" placeholder="{{ trans('admin_page.origin_password') }}" autofocus>
                        </div>

                        <div class="form-group">
                            <label for="InputName">{{ trans('admin_page.new_password') }}</label>
                            <input type="password" name="password" class="form-control"  id="InputName" placeholder="{{ trans('admin_page.new_password') }}">
                        </div>

                        <div class="form-group">
                            <label for="InputName">{{ trans('admin_page.new_password_repeat') }}</label>
                            <input type="password" name="password_confirmation" class="form-control"  id="InputName" placeholder="{{ trans('admin_page.new_password_repeat') }}">
                        </div>

                        <div class="form-group">
                            <label for="InputName">&nbsp;</label>
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="return window.history.back();">{{ trans('admin_page.cancel') }}</button>
                            <input type="submit" class="btn btn-primary" value="{{ trans('admin_page.submit') }}">
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>

@endsection
