<div class='btn-group btn-group-sm'>
  @can('encargos.show')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('encargos.show', $id) }}" class='btn btn-link'>
    <i class="fa fa-eye"></i>
  </a>
  @endcan

  @can('encargos.edit')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.order_edit')}}" href="{{ route('encargos.edit', $id) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a>
  @endcan

  @can('encargos.destroy')
{!! Form::open(['route' => ['encargos.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fas fa-trash"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-danger',
  'onclick' => "return confirm('¿Seguro quieres eliminar esté elemento?')"
  ]) !!}
{!! Form::close() !!}
  @endcan
</div>
