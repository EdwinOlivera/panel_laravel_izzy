<div class='btn-group btn-group-sm'>
    @can('convenience_stores.show')
        <a data-toggle="tooltip" data-placement="bottom" title="{{ trans('lang.view_details') }}"
            href="{{ route('convenience_stores.show', $id) }}" class='btn btn-link'>
            <i class="fa fa-eye"></i>
        </a>
    @endcan

    {{-- @can('convenience_stores.editCategory')
        <a data-toggle="tooltip" data-placement="bottom" title="{{ trans('Editar Categorias (Tienda de coveniencia)') }}"
            href="{!! route('convenience_stores.editCategory', $id) !!}" class='btn btn-link'>
            <i class="fa fa-list"></i>
        </a>
    @endcan --}}
    @can('convenience_stores.editSectionsByConvenienceStore')
        <a data-toggle="tooltip" data-placement="bottom" title="{{ trans('Editar Secciones') }}"
            href="{!! route('convenience_stores.editSectionsByConvenienceStore', $id) !!}" class='btn btn-link'>
            <i class="fa fa-grip-lines"></i>
        </a>
    @endcan

    @can('convenience_stores.edit')
        <a data-toggle="tooltip" data-placement="bottom" title="{{ trans('lang.convenience_store_edit') }}"
            href="{{ route('convenience_stores.edit', $id) }}" class='btn btn-link'>
            <i class="fa fa-edit"></i>
        </a>
    @endcan

    @can('convenience_stores.destroy')
        {!! Form::open(['route' => ['convenience_stores.destroy', $id], 'method' => 'delete']) !!}
        {!! Form::button('<i class="fas fa-trash"></i>', [
    'type' => 'submit',
    'class' => 'btn btn-link text-danger',
    'onclick' => "return confirm('¿Seguro quieres eliminar está Tienda?')",
]) !!}
        {!! Form::close() !!}
    @endcan
</div>
