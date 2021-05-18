@extends('layouts.settings.default')
@push('css_lib')
    <!-- iCheck -->
    <link rel="stylesheet" href="{{asset('plugins/iCheck/flat/blue.css')}}">
    <!-- select2 -->
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="{{asset('plugins/summernote/summernote-bs4.css')}}">
    {{--dropzone--}}
    <link rel="stylesheet" href="{{asset('plugins/dropzone/bootstrap.min.css')}}">
@endpush
@section('settings_title',trans('lang.user_table'))
@section('settings_content')
    @include('flash::message')
    @include('adminlte-templates::common.errors')
    <div class="clearfix"></div>
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                <li class="nav-item">
                    <a class="nav-link" href="{!! url('settings/payment/payment') !!}"><i class="fa fa-money mr-2"></i>{{trans('lang.app_setting_payment')}}</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link " href="{!! url('settings/payment/paypal') !!}"><i class="fa fa-paypal mr-2"></i>{{trans('lang.app_setting_paypal')}}@if(setting('enable_paypal', false))<span class="badge ml-2 badge-success">{{trans('lang.active')}}</span>@endif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link "  href="{!! url('settings/payment/stripe') !!}"><i class="fa fa-cc-stripe mr-2"></i>{{trans('lang.app_setting_stripe')}}@if(setting('enable_stripe',false))<span class="badge ml-2 badge-success">{{trans('lang.active')}}</span>@endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{!! url('settings/payment/razorpay') !!}"><i class="fa fa-envelope-o mr-2"></i>{{trans('lang.app_setting_razorpay')}}@if(setting('enable_razorpay',false))<span class="badge ml-2 badge-success">{{trans('lang.active')}}</span>@endif
                    </a>
                </li> --}}
                {{-- enable_pixelpay --}}
                {{-- <li class="nav-item">
                    <a class="nav-link active" href="{!! url('settings/payment/pixelpay') !!}"><i class="fa fa-credit-card-alt mr-2"></i>{{trans('lang.app_setting_pixelpay')}}@if(setting('enable_pixelpay',false))<span class="badge ml-2 badge-success">{{trans('lang.active')}}</span>@endif
                    </a>
                </li> --}}
                 {{-- enable_fac --}}
                 <li class="nav-item">
                    <a class="nav-link" href="{!! url('settings/payment/fac') !!}"><i class="fa fa-university mr-2"></i>{{trans('lang.app_setting_fac')}}@if(setting('enable_fac',false))<span class="badge ml-2 badge-success">{{trans('lang.active')}}</span>@endif
                    </a>
                </li>
                <div class="ml-auto d-inline-flex">
                    @can('currencies.index')
                    <li class="nav-item">
                        <a class="nav-link" href="{!! route('currencies.index') !!}"><i class="fa fa-dollar mr-2"></i>{{trans('lang.currency_table')}}</a>
                    </li>
                    @endcan
                </div>
            </ul>
        </div>
        <div class="card-body">
            {!! Form::open(['url' => ['settings/update'], 'method' => 'patch']) !!}
            <div class="row">
                <div style="flex: 70%;max-width: 70%;padding: 0 4px;" class="column">
                
                    <div class="form-group row col-12">
                        {!! Form::label('enable_pixelpay', trans('lang.app_setting_enable_pixelpay'),['class' => 'col-3 control-label text-right']) !!}
                        <div class="checkbox icheck">
                            <label class="w-100 ml-2 form-check-inline">
                                {!! Form::hidden('enable_pixelpay', null) !!}
                                {!! Form::checkbox('enable_pixelpay', 1, setting('enable_pixelpay', false)) !!}
                                <span class="ml-2">{!! trans('lang.app_setting_enable_pixelpay_help') !!}</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row col-12">
                        {!! Form::label('pixelpay_key', trans('lang.app_setting_pixelpay_key'), ['class' => 'col-3 control-label text-right']) !!}
                        <div class="col-9">
                            {!! Form::text('pixelpay_key', setting('pixelpay_key'),  ['class' => 'form-control','placeholder'=>  trans('lang.app_setting_pixelpay_key_placeholder')]) !!}
                            <div class="form-text text-muted">
                                {!! trans('lang.app_setting_pixelpay_key_help') !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group row col-12">
                        {!! Form::label('pixelpay_secret', trans('lang.app_setting_pixelpay_secret'), ['class' => 'col-3 control-label text-right']) !!}
                        <div class="col-9">
                            {!! Form::text('pixelpay_secret', setting('pixelpay_secret'),  ['class' => 'form-control','placeholder'=>  trans('lang.app_setting_pixelpay_secret_placeholder')]) !!}
                            <div class="form-text text-muted">
                                {!! trans('lang.app_setting_pixelpay_secret_help') !!}
                            </div>
                        </div>
                    </div>
                     <div class="form-group row col-12">
                        {!! Form::label('pixelpay_dominio', trans('lang.app_setting_pixelpay_dominio'), ['class' => 'col-3 control-label text-right']) !!}
                        <div class="col-9">
                            {!! Form::text('pixelpay_dominio', setting('pixelpay_dominio'),  ['class' => 'form-control','placeholder'=>  trans('lang.app_setting_pixelpay_dominio_placeholder')]) !!}
                            <div class="form-text text-muted">
                                {!! trans('lang.app_setting_pixelpay_dominio_help') !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div style="flex: 30%;max-width: 30%;padding: 0 4px;" class="column">
                    <!-- TODO explain pixelpay here-->
                </div>
                <!-- Submit Field -->
                <div class="form-group mt-4 col-12 text-right">
                    <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.app_setting_payment')}}</button>
                    <a href="{!! route('users.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
                </div>

            </div>
            {!! Form::close() !!}
            <div class="clearfix"></div>
        </div>
    </div>
    </div>
    @include('layouts.media_modal',['collection'=>null])
@endsection
@push('scripts_lib')
    <!-- iCheck -->
    <script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
    <!-- select2 -->
    <script src="{{asset('plugins/select2/select2.min.js')}}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
    {{--dropzone--}}
    <script src="{{asset('plugins/dropzone/dropzone.js')}}"></script>
    <script type="text/javascript">
        Dropzone.autoDiscover = false;
        var dropzoneFields = [];
    </script>
@endpush
