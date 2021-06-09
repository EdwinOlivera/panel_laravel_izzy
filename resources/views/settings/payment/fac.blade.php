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
                <li class="nav-item">
                    <a class="nav-link" href="{!! url('settings/payment/pixelpay') !!}"><i class="fa fa-credit-card-alt mr-2"></i>{{trans('lang.app_setting_pixelpay')}}@if(setting('enable_pixelpay',false))<span class="badge ml-2 badge-success">{{trans('lang.active')}}</span>@endif
                    </a>
                </li>
                 {{-- enable_fac --}}
                 <li class="nav-item">
                    <a class="nav-link active" href="{!! url('settings/payment/fac') !!}"><i class="fa fa-university mr-2"></i>{{trans('lang.app_setting_fac')}}@if(setting('enable_fac',false))<span class="badge ml-2 badge-success">{{trans('lang.active')}}</span>@endif
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
                <div style="max-width: 80%;padding: 0 4px;" class="column">
                
                    <div class="form-group row col-12">
                        {!! Form::label('enable_fac', trans('lang.app_setting_enable_fac'),['class' => 'col-3 control-label text-right']) !!}
                        <div class="checkbox icheck">
                            <label class="w-100 ml-2 form-check-inline">
                                {!! Form::hidden('enable_fac', null) !!}
                                {!! Form::checkbox('enable_fac', 1, setting('enable_fac', false)) !!}
                                <span class="ml-2">{!! trans('lang.app_setting_enable_fac_help') !!}</span>
                            </label>
                        </div>
                    </div>
                    {{-- <div class="form-group row col-12">
                        {!! Form::label('enable_fac_3d_secure', trans('lang.app_setting_enable_fac_3d_secure'),['class' => 'col-3 control-label text-right']) !!}
                        <div class="checkbox icheck">
                            <label class="w-100 ml-2 form-check-inline">
                                {!! Form::hidden('enable_fac_3d_secure', null) !!}
                                {!! Form::checkbox('enable_fac_3d_secure', 1, setting('enable_fac_3d_secure', false)) !!}
                                <span class="ml-2">{!! trans('lang.app_setting_enable_fac_3d_secure_help') !!}</span>
                            </label>
                        </div>
                    </div> --}}
                    <div class="form-group row col-12">
                        {!! Form::label('fac_merchant_password', trans('lang.app_setting_fac_password'), ['class' => 'col-3 control-label text-right']) !!}
                        <div class="col-9">
                            {!! Form::text('fac_merchant_password', setting('fac_merchant_password'),  ['class' => 'form-control','placeholder'=>  trans('#&bNzQUtUInx')]) !!}
                            <div class="form-text text-muted">
                                {!! trans('lang.app_setting_fac_password_help') !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group row col-12">
                        {!! Form::label('fac_merchant_id', trans('lang.app_setting_fac_merchant_id'), ['class' => 'col-3 control-label text-right']) !!}
                        <div class="col-9">
                            {!! Form::text('fac_merchant_id', setting('fac_merchant_id'),  ['class' => 'form-control','placeholder'=>  trans('lang.app_setting_fac_merchant_id_placeholder')]) !!}
                            <div class="form-text text-muted">
                                {!! trans('lang.app_setting_fac_merchant_id_help') !!}
                            </div>
                        </div>
                    </div>
{{-- base_url_fac --}}
                    <div class="form-group row col-12">
                        {!! Form::label('base_url_fac', trans('lang.app_setting_fac_base_url'), ['class' => 'col-3 control-label text-right']) !!}
                        <div class="col-9">
                            {!! Form::text('base_url_fac', setting('base_url_fac'),  ['class' => 'form-control','placeholder'=>  trans('lang.app_setting_fac_base_url_placeholder')]) !!}
                            <div class="form-text text-muted">
                                {!! trans('lang.app_setting_fac_base_url_help') !!}
                            </div>
                        </div>
                    </div>
{{-- base_url_fac_3d_secure --}}
                    {{-- <div class="form-group row col-12">
                        {!! Form::label('base_url_fac_3d_secure', trans('lang.app_setting_fac_base_url_3d_secure'), ['class' => 'col-3 control-label text-right']) !!}
                        <div class="col-9">
                            {!! Form::text('base_url_fac_3d_secure', setting('base_url_fac_3d_secure'),  ['class' => 'form-control','placeholder'=>  trans('lang.app_setting_fac_base_url_3d_secure_placeholder')]) !!}
                            <div class="form-text text-muted">
                                {!! trans('lang.app_setting_fac_base_url_3d_secure_help') !!}
                            </div>
                        </div>
                    </div> --}}
                    {{-- url_transaction_modification --}}
                    <div class="form-group row col-12">
                        {!! Form::label('url_transaction_modification', trans('URL: Transaction Modification'), ['class' => 'col-3 control-label text-right']) !!}
                        <div class="col-9">
                            {!! Form::text('url_transaction_modification', setting('url_transaction_modification'),  ['class' => 'form-control','placeholder'=>  trans('ejm: https://ecm.firstatlanticcommerce.com/PGServiceXML/TransactionModification')]) !!}
                            <div class="form-text text-muted">
                                {!! trans('Inserte la URL usada para modificar las transacciones cambiar "marlin" por "ecm" para realizar pruebas') !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div style="flex: 30%;max-width: 30%;padding: 0 4px;" class="column">
                    <!-- TODO explain pixelpay here-->
                </div>
                <!-- Submit Field -->
                <div class="form-group mt-4 col-12 text-right">
                    <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('F.A.C')}}</button>
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
