{{-- @if($customFields)
    <h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif --}}

{{-- Seccion punto A --}}
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">


    {!! Form::label('name', trans("Datos del Punto A"), ['class' => 'col-6 control-label text-right']) !!}

    <div class="form-group row ">
        {!! Form::label('direccion_a', trans("Punto A"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('direccion_a', null, ['class' => 'form-control','readonly','placeholder'=> trans("lang.market_name_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("Punto A de entrega del producto") }}
            </div>
        </div>
    </div>

    <div class="form-group row ">
        {!! Form::label('descripcion_a', trans("Descripción"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('descripcion_a', null, ['class' => 'form-control','readonly','placeholder'=> trans("Descripción del árticulo")]) !!}
            <div class="form-text text-muted">
                {{ trans("Descripcion del lugar a entregar") }}
            </div>
        </div>
    </div>
    <div class="form-group row ">
        {!! Form::label('hacer_repartidor_a', trans("Hacer repartidor en el lugar"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::textarea('hacer_repartidor_a', null, ['class' => 'form-control','readonly','placeholder'=> trans("lang.market_name_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans(" Lo que tiene que hacer el repartidor en el lugar") }}
            </div>
        </div>
    </div>
    <div class="form-group row ">
        {!! Form::label('lat_a', trans("Lat:"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('lat_a', null, ['class' => 'form-control','readonly','placeholder'=> trans("lang.market_name_placeholder")]) !!}
            
        </div>
    </div>

    <div class="form-group row ">
        {!! Form::label('lng_a', trans("long:"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('lng_a', null, ['class' => 'form-control','readonly','placeholder'=> trans("lang.market_name_placeholder")]) !!}
         
        </div>
        <!-- Status Field -->

    </div>
    <div class="form-group row ">
        {!! Form::label('status', trans("lang.payment_status"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('status',
            [
            'Waiting for Client' => trans('lang.encargo_pending'),
            'Not Paid' => trans('lang.encargo_not_paid'),
            'Paid' => trans('lang.encargo_paid'),
            ]
            , isset($encargo->payment) ? $encargo->payment->status : '', ['class' => 'select2 form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.payment_status_help") }}</div>
        </div>
    </div>
    
    <div class="checkbox icheck">
        <label class="col-9 ml-2 form-check-inline">
            {!! Form::hidden('pagada', 0) !!}
        </label>
    </div>
    <!-- encargo Status Id Field -->
    <div class="form-group row ">
        {!! Form::label('encargo_status_id', trans("lang.encargo_status_id"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('encargo_status_id', $encargoStatus, null, ['class' => 'select2 form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.encargo_status_id_help") }}</div>
        </div>
    </div>

    <!-- Driver Id Field -->
    <div class="form-group row ">
        {!! Form::label('driver_id', trans("lang.encargo_driver_id"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('driver_id', $drivers, null, ['data-empty'=>trans("lang.encargo_driver_id_placeholder"),'class' => 'select2 not-required form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.encargo_driver_id_help") }}</div>
        </div>
    </div>
    <!-- 'Boolean active Field' -->
    <div class="form-group row ">
        {!! Form::label('active', trans("lang.encargo_active"),['class' => 'col-3 control-label text-right']) !!}
        <div class="checkbox icheck">
            <label class="col-9 ml-2 form-check-inline">
                {!! Form::hidden('active', 0) !!}
                {!! Form::checkbox('active', 1, null) !!}
            </label>
        </div>


        {!! Form::label('assigned', trans("Asignada"),['class' => 'col-3 control-label text-right']) !!}
        <div class="checkbox icheck">
            <label class="col-9 ml-2 form-check-inline">
                {!! Form::hidden('assigned', 0) !!}
                {!! Form::checkbox('assigned', 1, null, ['disabled']) !!}
            </label>
        </div>
    </div>
    {{-- Datos del usuario --}}
    <div class="form-group row ">
        {!! Form::label('user_name', trans("Cliente"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('user_name', null, ['class' => 'form-control','readonly','placeholder'=> trans("lang.market_name_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("Nombre del cliente que hizo el Encargo ") }}
            </div>
        </div>
    </div>

    <div class="form-group row ">
        {!! Form::label('tel', trans("Tel:"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('tel', null, ['class' => 'form-control','readonly','placeholder'=> trans("lang.market_name_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("Numero Telefonico del cliente") }}
            </div>
        </div>
    </div>

</div>

{{-- Seccion punto B --}}
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
    {!! Form::label('name', trans("Datos del Punto B"), ['class' => 'col-6 control-label text-right']) !!}

    <div class="form-group row ">
        {!! Form::label('direccion_b', trans("Punto B"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('direccion_b', null, ['class' => 'form-control','readonly','placeholder'=> trans("lang.market_name_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("Direccion de entrega del Encargo ") }}
            </div>
        </div>
    </div>



    {{-- <div class="form-group row ">
        {!! Form::label('descripcion_b', trans("Descripción"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('descripcion_b', null, ['class' => 'form-control','readonly','placeholder'=> trans("Descripción del árticulo")]) !!}
            <div class="form-text text-muted">
                {{ trans("Descripcion del lugar de Entrega") }}
            </div>
        </div>
    </div> --}}
    <div class="form-group row ">
        {!! Form::label('hacer_repartidor_b', trans("Hacer repartidor en el lugar"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::textarea('hacer_repartidor_b', null, ['class' => 'form-control','readonly','placeholder'=> trans("lang.market_name_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("Lo que tiene que hacer el repartidor en el lugar") }}
            </div>
        </div>
    </div>
    <div class="form-group row ">
        {!! Form::label('lat_b', trans("Lat:"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('lat_b', null, ['class' => 'form-control','readonly','placeholder'=> trans("lang.market_name_placeholder")]) !!}
            
        </div>
    </div>

    <div class="form-group row ">
        {!! Form::label('lng_b', trans("lng:"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('lng_b', null, ['class' => 'form-control','readonly','placeholder'=> trans("lang.market_name_placeholder")]) !!}
            
        </div>
    </div>

</div>

<div class="form-group col-12 text-right">
    <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.encargo')}}</button>
    <a href="{!! route('encargos.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
