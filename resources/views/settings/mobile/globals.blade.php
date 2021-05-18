@extends('layouts.settings.default')
@push('css_lib')
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('plugins/iCheck/flat/blue.css') }}">
    <!-- select2 -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.css') }}">
    {{--dropzone--}}
    <link rel="stylesheet" href="{{ asset('plugins/dropzone/bootstrap.min.css') }}">
    {{--Color Picker--}}
    <link rel="stylesheet" href="{{ asset('plugins/colorpicker/bootstrap-colorpicker.min.css') }}">
@endpush
@section('settings_title', trans('lang.app_setting_mobile'))
@section('settings_content')
    @include('flash::message')
    @include('adminlte-templates::common.errors')
    <div class="clearfix"></div>
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                <li class="nav-item">
                    <a class="nav-link active" href="{!!  url()->current() !!}"><i
                            class="fa fa-cog mr-2"></i>{{ trans('lang.app_setting_' . $tab) }}</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            {!! Form::open(['url' => ['settings/update'], 'method' => 'patch']) !!}
            <div class="row">
                <h5 class="col-12 pb-4 custom-field-container"><i class="mr-3 fa fa-map-signs"></i>{!! trans('Mandaditos') !!}</h5>
                    <div class="form-group row col-12">
                        {!! Form::label('enable_mandaditos', trans('Habilitar Mandados'), ['class' => ' control-label
                        text-right']) !!}
                        <div class="checkbox icheck">
                            <label class="w-100 ml-2 form-check-inline">
                                {!! Form::hidden('enable_mandaditos', null) !!}
                                {!! Form::checkbox('enable_mandaditos', 1, setting('enable_mandaditos', true)) !!}
                            </label>
                        </div>
                    </div>
                {{-- Seccion de Zonas de mapas para entregas --}}
                <h5 class="col-12 pb-4 custom-field-container"><i class="mr-3 fa fa-map-marker"></i>{!! trans('Zonas para entregas') !!}</h5>
                    <div class="form-group row col-12">
                        {!! Form::label('enable_zone_maps', trans('Habilitar zonas de entrega'), ['class' => ' control-label
                        text-right']) !!}
                        <div class="checkbox icheck">
                            <label class="w-100 ml-2 form-check-inline">
                                {!! Form::hidden('enable_zone_maps', null) !!}
                                {!! Form::checkbox('enable_zone_maps', 1, setting('enable_zone_maps', true)) !!}
                            </label>
                        </div>
                    </div>

                {{-- Seccion de Ordenes Agendadas --}}
                <h5 class="col-12 pb-4 custom-field-container"><i class="mr-3 fa fa-clock-o"></i>{!!trans('lang.scheduled_order_hours') !!}</h5>
                    <div class="form-group row col-12">
                        {!! Form::label('enable_agendar', trans('Habilitar ordenes Agendadas'), ['class' => ' control-label
                        text-right']) !!}
                        <div class="checkbox icheck">
                            <label class="w-100 ml-2 form-check-inline">
                                {!! Form::hidden('enable_agendar', null) !!}
                                {!! Form::checkbox('enable_agendar', 1, setting('enable_agendar', true)) !!}
                            </label>
                        </div>
                    </div>

                <h5 class="col-12"><i class="mr-1 fa fa-unlock-alt"></i>{!! trans('Hora desde que permite entregar Ordenes Agendadas') !!}</h5>

                <div class="form-group row ">
                    {!! Form::label('hora_apertura_agenda', trans('Hora'), ['class' => 'col-5 control-label text-right'])
                    !!}
                    <div class="col-7">
                        {!! Form::number('hora_apertura_agenda', setting('hora_apertura_agenda', ''), ['class' =>
                        'form-control', 'min=0', 'max=23', 'placeholder' => trans('Ejemplo: 21')]) !!}
                        <div class="form-text text-muted">
                            {{ trans('Hora inicial para entregar Ordenes Agendadas') }}
                        </div>
                    </div>
                </div>

                <h5 class="col-12"><i class="mr-1 fa fa-lock"></i>{!! trans('Hora límite permitida') !!}</h5>

                <div class="form-group row ">
                    {!! Form::label('hora_cierre_agenda', trans('Hora'), ['class' => 'col-5 control-label text-right']) !!}
                    <div class="col-7">
                        {!! Form::number('hora_cierre_agenda', setting('hora_cierre_agenda', ''), ['class' =>
                        'form-control', 'min=0', 'max=23', 'placeholder' => trans('Ejemplo: 21')]) !!}
                        <div class="form-text text-muted">
                            {{ trans('Hora limite para las Ordenes Agendadas') }}
                        </div>
                    </div>
                </div>

                {{-- Seccion de Telefono y mensaje de Saludo --}}
                <h5 class="col-12 pb-4 custom-field-container"><i class="mr-3 fa fa-map"></i>{!!
                    trans('lang.app_setting_number_for_whatsapp') !!}</h5>

                <div class="form-group row col-12">
                    {!! Form::label('number_for_whatsapp', trans('lang.app_setting_number_for_whatsapp'), ['class' => 'col-2 control-label text-right']) !!}
                    <div class="col-10">
                        {!! Form::text('number_for_whatsapp', setting('number_for_whatsapp', ''), ['class' =>
                        'form-control', 'placeholder' => trans('lang.app_setting_number_for_whatsapp_placeholder')]) !!}
                        <div class="form-text text-muted">
                            {!! trans('lang.app_setting_number_for_whatsapp_help') !!}
                        </div>
                    </div>
                </div>
                {{-- initial_message_for_whatsapp --}}
                <div class="form-group row col-12">

                    {!! Form::label('initial_message_for_whatsapp', trans('lang.app_setting_initial_message_for_whatsapp'),
                    ['class' => 'col-2 control-label text-right']) !!}
                    <div class="col-10">
                        {!! Form::text('initial_message_for_whatsapp', setting('initial_message_for_whatsapp', ''), ['class'
                        => 'form-control', 'placeholder' =>
                        trans('lang.app_setting_initial_message_for_whatsapp_placeholder')]) !!}
                        <div class="form-text text-muted">
                            {!! trans('lang.app_setting_initial_message_for_whatsapp_help') !!}
                        </div>
                    </div>
                </div>
                <h5 class="col-12 pb-4 custom-field-container"><i class=""></i></h5>

                {{-- Seccion de Google Api Key --}}
                <h5 class="col-12 pb-4"><i class="mr-3 fa fa-map"></i>{!! trans('lang.app_setting_google_maps_key') !!}</h5>

                <div class="form-group row col-12">
                    {!! Form::label('google_maps_key', trans('lang.app_setting_google_maps_key'), ['class' => 'col-2
                    control-label text-right']) !!}
                    <div class="col-10">
                        {!! Form::text('google_maps_key', setting('google_maps_key',
                        'AIzaSyAT07iMlfZ9bJt1gmGj9KhJDLFY8srI6dA'), ['class' => 'form-control', 'placeholder' =>
                        trans('lang.app_setting_google_maps_key_placeholder')]) !!}
                        <div class="form-text text-muted">
                            {!! trans('lang.app_setting_google_maps_key_help') !!}
                        </div>
                    </div>
                </div>

                <!-- Theme Color Field -->
                <div class="form-group row col-6">
                    {!! Form::label('distance_unit', trans('lang.app_setting_distance_unit'), ['class' => 'col-4 control-label text-right']) !!}
                    <div class="col-8">
                        {!! Form::select(
                        'distance_unit',
                        [
                        'km' => trans('lang.app_setting_km'),
                        'mi' => trans('lang.app_setting_mi'),
                        ],
                        setting('distance_unit', 'km'),
                        ['class' => 'select2 form-control'],
                        ) !!}
                        <div class="form-text text-muted">{{ trans('lang.app_setting_distance_unit_help') }}</div>
                    </div>
                </div>

                <h5 class="col-12 pb-4 custom-field-container"><i class="mr-3 fa fa-globe"></i>{!!
                    trans('lang.app_setting_language') !!}</h5>

                <!-- Lang Field -->
                <div class="form-group row col-6">
                    {!! Form::label('mobile_language', trans('lang.app_setting_mobile_language'), ['class' => 'col-4
                    control-label text-right']) !!}
                    <div class="col-8">
                        {!! Form::select('mobile_language', $mobileLanguages, setting('mobile_language', 'en'), ['class' =>
                        'select2 form-control']) !!}
                        <div class="form-text text-muted">{{ trans('lang.app_setting_mobile_language_help') }}</div>
                    </div>
                </div>

                <h5 class="col-12 pb-4 custom-field-container " style="color: red"><i class="mr-3 fa fa-key" style="color: red"></i>{!!
                    trans('App fuera de servicio temporalmente (No afecta a: Izzy-Store e Izzy - Driver)') !!}</h5>

                <!-- message_app_off_line Field -->
                <div class="form-group row col-12">
                    {!! Form::label('message_app_off_line', trans('Mensaje'), ['class' => 'col-2
                    control-label text-right','style'=>"color: red"]) !!}
                    <div class="col-10">
                        {!! Form::text('message_app_off_line', setting('message_app_off_line',
                        'Estamos actualizando el sistema, por los momentos el nuestros servicios no esta disponible'), ['class' => 'form-control', 'placeholder' =>
                        trans('Estamos actualizando el sistema, por los momentos el nuestros servicios no esta disponible')]) !!}
                        <div class="form-text text-muted">
                            {!! trans('Mensaje explicativo del porque el app no estara disponible.') !!}
                        </div>
                    </div>
                </div>
                <!-- 'Boolean App Off Line' -->
                <div class="form-group row col-8">
                    {!! Form::label('app_off_line', trans('App fuera de servicio'),  ['class' => 'col-4  control-label text-right','style'=>"color: red"  ]) !!}
                    <div class="checkbox icheck">
                        <label class="w-100 ml-2 form-check-inline">
                            {!! Form::hidden('app_off_line', null) !!}
                            {!! Form::checkbox('app_off_line', 1, setting('app_off_line', false)) !!}
                        </label>
                    </div>
                </div>

                <h5 class="col-12 pb-4 custom-field-container"><i class="mr-3 fa fa-mobile-phone"></i>{!!
                    trans('lang.app_setting_version') !!}</h5>

                <!-- app_version Field -->
                <div class="form-group row col-6">
                    {!! Form::label('app_version', trans('lang.app_setting_app_version'), ['class' => 'col-4 control-label
                    text-right']) !!}
                    <div class="col-8">
                        {!! Form::text('app_version', setting('app_version', '1.0.0'), ['class' => 'form-control',
                        'placeholder' => trans('lang.app_setting_app_version_placeholder')]) !!}
                        <div class="form-text text-muted">
                            {!! trans('lang.app_setting_app_version_help') !!}
                        </div>
                    </div>
                </div>
                <!-- 'Boolean enable version' -->
                <div class="form-group row col-6">
                    {!! Form::label('enable_version', trans('lang.app_setting_enable_version'), ['class' => 'col-4  control-label text-right']) !!}
                    <div class="checkbox icheck">
                        <label class="w-100 ml-2 form-check-inline">
                            {!! Form::hidden('enable_version', null) !!}
                            {!! Form::checkbox('enable_version', 1, setting('enable_version', true)) !!}
                        </label>
                    </div>
                </div>

                <!-- Submit Field -->
                <div class="form-group mt-4 col-12 text-right">
                    <button type="submit" class="btn btn-{{ setting('theme_color') }}">
                        <i class="fa fa-save"></i> {{ trans('lang.save') }} {{ trans('lang.app_setting') }}
                    </button>
                    <a href="{!!  route('users.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i>
                        {{ trans('lang.cancel') }}</a>
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
    <script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
    <!-- select2 -->
    <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
    {{--dropzone--}}
    <script src="{{ asset('plugins/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <script type="text/javascript">
        // $("input[name$='color']").colorpicker({
        $(".colorpicker-component, input[name$='color']").colorpicker({
            customClass: 'colorpicker',
            format: 'hex',
            sliders: {
                saturation: {
                    maxLeft: 200,
                    maxTop: 200
                },
                hue: {
                    maxTop: 200
                },
                alpha: {
                    maxTop: 200
                }
            }
        });
        Dropzone.autoDiscover = false;
        var dropzoneFields = [];

    </script>
@endpush
