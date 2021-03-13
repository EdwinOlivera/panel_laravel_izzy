<div class='btn-group btn-group-sm'>
  @can('departments.show')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('departments.show', $id) }}" class='btn btn-link'>
    <i class="fa fa-eye"></i>
  </a>
  @endcan

  @can('departments.edit')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.department_edit')}}" href="{{ route('departments.edit', $id) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a>
  @endcan

  @can('departments.destroy')
{!! Form::open(['route' => ['departments.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fas fa-trash"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-danger',
  'onclick' => "return confirm('¿Seguro quieres eliminar esté elemento?')"
  ]) !!}
{!! Form::close() !!}
  @endcan
</div>
