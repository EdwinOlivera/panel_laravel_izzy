<div class='btn-group btn-group-sm'>
    @can('typesMarket.show')
        <a data-toggle="tooltip" data-placement="bottom" title="{{ trans('lang.view_details') }}"
            href="{{ route('typesMarket.show', $id) }}" class='btn btn-link'>
            <i class="fa fa-eye"></i>
        </a>
    @endcan

    @can('typesMarket.edit')
        <a data-toggle="tooltip" data-placement="bottom" title="{{ trans('lang.option_group_edit') }}"
            href="{{ route('typesMarket.edit', $id) }}" class='btn btn-link'>
            <i class="fa fa-edit"></i>
        </a>
    @endcan

    @can('typesMarket.destroy')
        {{-- {!! Form::open(['route' => ['typesMarket.destroy', $id], 'method' => 'delete']) !!}
        {!! Form::button('<i class="fas fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-link text-danger',
        'onclick' => "return confirm('¿Seguro quieres eliminar esté elemento?')",
        ]) !!}
        {!! Form::close() !!} --}}
    @endcan
</div>
