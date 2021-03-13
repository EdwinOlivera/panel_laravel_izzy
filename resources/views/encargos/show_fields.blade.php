<!-- Id Field -->
<div class="form-group row col-md-4 col-sm-12">
    {!! Form::label('id', trans('lang.encargo_id'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>#{!! $encargo->id !!}</p>
  </div>

    {!! Form::label('encargo_client', trans('lang.encargo_client'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>{!! $encargo->user->name !!}</p>
  </div>

    {!! Form::label('encargo_client_phone', trans('lang.encargo_client_phone'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>{!! isset($encargo->user->custom_fields['phone']) ? $encargo->user->custom_fields['phone']['view'] : "" !!}</p>
  </div>

    {!! Form::label('delivery_address', trans('lang.delivery_address'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>{!! $encargo->deliveryAddress ? $encargo->deliveryAddress->address : '' !!}</p>
  </div>

    {!! Form::label('encargo_date', trans('lang.encargo_date'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>{!! $encargo->created_at !!}</p>
  </div>


</div>

<!-- encargo Status Id Field -->
<div class="form-group row col-md-4 col-sm-12">
    {!! Form::label('encargo_status_id', trans('lang.encargo_status_status'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>{!! $encargo->encargoStatus->status  !!}</p>
  </div>

    {!! Form::label('active', trans('lang.encargo_active'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    @if($encargo->active)
      <p><span class='badge badge-success'> {{trans('lang.yes')}}</span></p>
      @else
      <p><span class='badge badge-danger'>{{trans('lang.encargo_canceled')}}</span></p>
      @endif
  </div>

    {!! Form::label('payment_method', trans('lang.payment_method'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>{!! isset($encargo->payment) ? $encargo->payment->method : ''  !!}</p>
  </div>

    {!! Form::label('payment_status', trans('lang.payment_status'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    <p>{!! isset($encargo->payment) ? $encargo->payment->status : trans('lang.encargo_not_paid')  !!}</p>
  </div>
    {!! Form::label('encargo_updated_date', trans('lang.encargo_updated_at'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
        <p>{!! $encargo->updated_at !!}</p>
    </div>

</div>

<!-- Id Field -->
<div class="form-group row col-md-4 col-sm-12">
    {!! Form::label('market', trans('lang.market'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
        @if(isset($encargo->productencargos[0]))
            <p>{!! $encargo->productencargos[0]->product->market->name !!}</p>
        @endif
    </div>

    {!! Form::label('market_address', trans('lang.market_address'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
        @if(isset($encargo->productencargos[0]))
            <p>{!! $encargo->productencargos[0]->product->market->address !!}</p>
        @endif
    </div>

    {!! Form::label('market_phone', trans('lang.market_phone'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
        @if(isset($encargo->productencargos[0]))
            <p>{!! $encargo->productencargos[0]->product->market->phone !!}</p>
        @endif
    </div>

    {!! Form::label('driver', trans('lang.driver'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
        @if(isset($encargo->driver))
            <p>{!! $encargo->driver->name !!}</p>
        @else
            <p>{{trans('lang.encargo_driver_not_assigned')}}</p>
        @endif

    </div>

    {!! Form::label('hint', 'Hint:', ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
        <p>{!! $encargo->hint !!}</p>
    </div>

</div>

{{--<!-- Tax Field -->--}}
{{--<div class="form-group row col-md-6 col-sm-12">--}}
{{--  {!! Form::label('tax', 'Tax:', ['class' => 'col-4 control-label']) !!}--}}
{{--  <div class="col-8">--}}
{{--    <p>{!! $encargo->tax !!}</p>--}}
{{--  </div>--}}
{{--</div>--}}


