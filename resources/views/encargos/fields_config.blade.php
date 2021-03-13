<div style="flex: 50%;max-width: 70%;padding: 0 4px;" class="column">

    {{-- Seccion Monto Base --}}
    <div class="form-group row ">
        {!! Form::label('monto_base', trans("Monto base"), ['class' => 'col-5 control-label text-right']) !!}
        <div class="col-6">
            {!! Form::number('monto_base', null, ['class' => 'form-control', 'min=20','placeholder'=> trans("20")]) !!}
            <div class="form-text text-muted">
                {{ trans("Monto inicial a cobrar por encargo") }}
            </div>
        </div>
    </div>
    {{-- Seccion Monto Extra por fuera de Rango --}}
    <div class="form-group row ">
        {!! Form::label('monto_extra', trans("Monto Extra por fuera de rango"), ['class' => 'col-5 control-label text-right']) !!}
        <div class="col-6">
            {!! Form::number('monto_extra', null, ['class' => 'form-control', 'min=0','placeholder'=> trans("10")]) !!}
            <div class="form-text text-muted">
                {{ trans("Monto extra que se cobrar si se pasa de un límite  de distancia entre Puntos") }}
            </div>
        </div>
    </div>
    {{-- Seccion Habilitar Monto Extra --}}

    <div class="form-group row ">

        {!! Form::label('habil_rang_extra', trans("Habilitar Monto Extra"),['class' => 'col-5 control-label text-right']) !!}
        <div class="checkbox icheck">
            <label class="col-9 ml-2 form-check-inline">
                {!! Form::hidden('habil_rang_extra', 0) !!}
                {!! Form::checkbox('habil_rang_extra', 1, null) !!}
            </label>

        </div>
    </div>

    
    {{-- Seccion Rango Minimo --}}

    <div class="form-group row ">
        {!! Form::label('rango_minimo', trans("Rango máximo"), ['class' => 'col-5 control-label text-right']) !!}
        <div class="col-6">
            {!! Form::number('rango_minimo', null, ['class' => 'form-control', 'min=1','placeholder'=> trans("5")]) !!}
            <div class="form-text text-muted">
                {{ trans("Distancia máxima (km) entre puntos antes de cobrar el monto extra. ") }}
            </div>
        </div>
    </div>

    {{-- Seccion Comision del Repartidor --}}
    {{-- <div class="form-group row ">
        {!! Form::label('comision_repartidor', trans("Comision del Repartidor"), ['class' => 'col-5 control-label text-right']) !!}
        <div class="col-6">
            {!! Form::number('comision_repartidor', null, ['class' => 'form-control', 'min=5','placeholder'=> trans("lang. market_name_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("Comision que tendra el repartidor por cada encargo. Ejm: 10 = 10%") }}
            </div>
        </div>
    </div>
     --}}
</div>



<div class="form-group col-12 text-right">
    <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.encargo_properties')}}</button>
    <a href="{!! route('encargos.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
