<div class='btn-group btn-group-sm'>
  @can('supermarkets.show')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('supermarkets.show', $id) }}" class='btn btn-link'>
    <i class="fa fa-eye"></i>
  </a>
  @endcan

  {{-- @can('supermarkets.editMarketComplete')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('Editar Categorias (supermercado)')}}" href="{!! route('supermarkets.editMarketComplete', $id) !!}" class='btn btn-link'>
    <i class="fa fa-list"></i>
  </a>
  @endcan --}}
  @can('supermarkets.editDepartmentsByMarket')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('Editar departamentos')}}" href="{!! route('supermarkets.editDepartmentsByMarket', $id) !!}" class='btn btn-link'>
    <i class="fa fa-th-large"></i>
  </a>
  @endcan

  @can('supermarkets.edit')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.supermarket_edit')}}" href="{{ route('supermarkets.edit', $id) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a>
  @endcan


  @can('supermarkets.destroy')
{!! Form::open(['route' => ['supermarkets.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fas fa-trash"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-danger',
  'onclick' => "return confirm('¿Seguro quieres eliminar esté elemento?')"
  ]) !!}
{!! Form::close() !!}
  @endcan
</div>
