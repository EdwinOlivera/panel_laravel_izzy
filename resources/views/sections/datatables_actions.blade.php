<div class='btn-group btn-group-sm'>
  @can('sections.show')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('sections.show', $id) }}" class='btn btn-link'>
    <i class="fa fa-eye"></i>
  </a>
  @endcan

  @can('sections.edit')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.section_edit')}}" href="{{ route('sections.edit', $id) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a>
  @endcan

  @can('sections.destroy')
{!! Form::open(['route' => ['sections.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fas fa-trash"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-danger',
  'onclick' => "return confirm('¿Seguro quieres eliminar está Sección?')"
  ]) !!}
{!! Form::close() !!}
  @endcan
</div>
