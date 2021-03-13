<div class='btn-group btn-group-sm'>
  @can('subdepartments.show')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('subdepartments.show', $id) }}" class='btn btn-link'>
    <i class="fa fa-eye"></i>
  </a>
  @endcan

  @can('subdepartments.edit')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.subdepartment_edit')}}" href="{{ route('subdepartments.edit', $id) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a>
  @endcan

  @can('subdepartments.destroy')
{!! Form::open(['route' => ['subdepartments.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fas fa-trash"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-danger',
  'onclick' => "return confirm('¿Seguro quieres eliminar esté elemento?')"
  ]) !!}
{!! Form::close() !!}
  @endcan
</div>
