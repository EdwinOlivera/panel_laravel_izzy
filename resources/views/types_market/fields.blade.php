
<div style="flex: 80%;max-width: 80%;padding: 0 4px;" class="column">
    <!-- Name Field -->
    <div class="form-group row ">

        {!! Form::label('name', trans('lang.type_market_name'), ['class' => 'col-3 control-label text-right']) !!}

        <div class="col-7">

            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' =>
            trans('lang.type_market_name_placeholder')]) !!}
            <div class="form-text text-muted">
                {{ trans('lang.type_market_name_help') }}
            </div>
        </div>
    </div>

    {{-- Description Field --}}
    <div class="form-group row ">
        {!! Form::label('description', trans('lang.type_market_description'), [
        'class' => 'col-3 mt-3 pb-3 control-label
        text-right',
        ]) !!}
        <div class="col-7 mt-3 pb-3">
            {!! Form::text('description', null, ['class' => 'form-control', 'placeholder' =>
            trans('lang.type_market_description_placeholder')]) !!}
            <div class="form-text text-muted">
                {{ trans('lang.type_market_description_help') }}
            </div>
        </div>

    </div>

    <!-- Boolean Enable selectable' -->
    <div class="form-group row ">
        {!! Form::label('enable', trans('lang.type_market_enable'), ['class' => 'col-3 control-label text-right'])
        !!}
        <div class="checkbox icheck">

            <label class="col-8 ml-2 form-check-inline">
                {!! Form::hidden('enable', 0) !!}
                {{-- {!! Form::checkbox('enable', 1, null, ['checked' => 'checked']) !!} --}}
                {!! Form::checkbox('enable', 1, $typeMarket->enable, ) !!}
            </label>
        </div>
        <div class="text-muted">
            {{ trans('lang.type_market_checbox_enable_help') }}
        </div>
    </div>


</div>


<!-- Submit Field -->
<div class="form-group col-12 text-right">
    <button type="submit" class="btn btn-{{ setting('theme_color') }}"><i class="fa fa-save"></i>
        {{ trans('lang.save') }} {{ trans('lang.type_market') }}</button>
    <a href="{!!  route('optionGroups.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i>
        {{ trans('lang.cancel') }}</a>
</div>
