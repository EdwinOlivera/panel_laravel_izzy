<div class='btn-group btn-group-sm'>
  @can('favorites.show')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('favorites.show', $id) }}" class='btn btn-link'>
    <i class="fa fa-eye"></i>
  </a>
  @endcan

  @can('favorites.edit')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.favorite_edit')}}" href="{{ route('favorites.edit', $id) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a>
  @endcan

  @can('favorites.destroy')
{!! Form::open(['route' => ['favorites.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fas fa-trash"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-danger',
  'onclick' => "return confirm('¿Seguro quieres eliminar esté elemento?')"
  ]) !!}
{!! Form::close() !!}
  @endcan
</div>
