<style>
    ::-webkit-scrollbar {
        -webkit-appearance: none;
        width: 7px;
    }

    ::-webkit-scrollbar-thumb {
        border-radius: 4px;
        background-color: rgba(0, 0, 0, .5);
        -webkit-box-shadow: 0 0 1px rgba(255, 255, 255, .5);
    }

    .marco_decorativo {
        border-radius: 4px 4px 4px 4px;
        -moz-border-radius: 4px 4px 4px 4px;
        -webkit-border-radius: 4px 4px 4px 4px;
        border: 2px solid #ebebeb;
        float: left;
        overflow-y: scroll;
        height: 500px;
    }

    .marco_decorativo_categorias {
        border-radius: 4px 4px 4px 4px;
        -moz-border-radius: 4px 4px 4px 4px;
        -webkit-border-radius: 4px 4px 4px 4px;
        border: 2px solid #ebebeb;
        float: left;
        overflow-y: scroll;
        height: 200px;
    }

    .marco_decorativo_Categorias {
        border-radius: 4px 4px 4px 4px;
        -moz-border-radius: 4px 4px 4px 4px;
        -webkit-border-radius: 4px 4px 4px 4px;
        border: 2px solid #ebebeb;
        overflow-y: scroll;
        height: 500px;
        margin-left: 10px;
        margin-bottom: 10px;

    }

    .marco_decorativo_grupoCategoria {
        border-radius: 4px 4px 4px 4px;
        -moz-border-radius: 4px 4px 4px 4px;
        -webkit-border-radius: 4px 4px 4px 4px;
        border: 2px solid #ebebeb;
        margin-left: 10px;
        margin-bottom: 10px;
        overflow-y: scroll;
        height: 500px;
    }

    .textoNombre {
        font-size: 14px;
        color: black;
        text-align: left;
    }

    .list-group-item {
        display: flex;
        align-items: center;
        white-space: initial
    }

    .highlight {
        /* Color de Fondo */
        /* background: #0fa13b; */
        background: #d38080;
        min-height: 50px;
        list-style-type: none;
    }

    .handle {
        min-width: 18px;
        min-height: 23px;
        background: #607D8B;
        height: 15px;
        display: inline-block;
        cursor: move;
        margin-right: 10px;
    }

    .desactivado {
        font-size: 1rem;
        color: red;
        text-align: left;
        text-decoration: line-through;
    }

    .select {
        background-color: #9b4444;
        font-weight: bold;
        color: white !important;
    }

    .textoBlanco {
        font-size: 1rem;
        color: black;
        text-align: left;
    }

    .CuartoDeCentrado {
        background-color: ;
        margin-left: 25%;
    }

    .Centrar {
        background-color: ;
        margin-left: 50%;
    }

    .alinearIzquierda {
        padding-left: 0px;
        margin-left: 0px;
    }

    .alinearVerticalmente {
        display: flex;
        justify-content: center;
        align-content: center;
        flex-direction: column;
    }

    .Limpiar {
        font-size: 1rem;
        color: black;
        text-align: left;
        text-decoration: none;
    }

    /* Clases para la carga */
    .loading {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        transition: 1s all;
        opacity: 0;
    }

    .loading.show {
        opacity: 1;
    }

    .loading .spin {
        border: 3px solid hsla(185, 100%, 62%, 0.2);
        border-top-color: #3cefff;
        border-radius: 50%;
        width: 3em;
        height: 3em;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .cruz_circulo {
        font-size: 25px;
        color: rgb(9, 94, 9);
    }

    .cruz_cuadrado {
        font-size: 25px;
        color: rgb(33, 15, 189);
    }
    .btnEditar {
        color: #007dff;
    }
    .btItem:hover {
        color: #1717FF
    }

    li.item:hover {

        background-color: #d38080;

    }
    
    a:hover{
        cursor: pointer;
    }

</style>

<div class="loading show">
    <div class="spin"></div>
</div>

<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
    <!-- Name Field -->
    <div class="form-group row ">
        {!! Form::label('name', trans('Establecimiento: '), ['class' => 'col-5 control-label text-right']) !!}
        <div class="col-7">
            {{ $market->name }}
        </div>
    </div>
</div>

<div class="container-fluid">

    <div class="row">
        <div class="marco_decorativo justify-content-center col-3 py-2">

            {!! Form::label('Categories', trans('Categoria (s)'), ['class' => 'col-12 control-label text-center']) !!}

            <div class=" ">
                <div class="row">
                    <div class="col-11 ml-3">
                        {!! Form::select('categoriesProducts[]', [], [], ['class' => 'select2 form-control', 'id' =>
                        'inputSelectCategory', 'multiple' => 'multiple']) !!}
                    </div>
                </div>

                <a   data-placement="bottom" title="{{ trans('Crear y añadir una Categoria nueva') }}"
                    style="color: black" onClick="setMarketIdSelectCategories({{ $market->id }})"
                    class="CuartoDeCentrado" data-toggle="modal" data-target="#createCategoryModal">
                    <i class="fa fa-plus-circle cruz_circulo"></i>
                </a>
                <a   id="mSeleccionarCategorias" data-placement="bottom"
                    title="{{ trans('Añadir una Categoria ya existente') }}" style="color: black"
                    class="CuartoDeCentrado">
                    <i class="fa fa-plus-square cruz_cuadrado"></i>
                </a>

            </div>
            <ul class="sort_category list-group">
                @foreach ($categoriesProducts as $category)
                    <li class="list-group-item  btn btn-success item "
                        onclick="selectCategory({{ $category->id }},{{ count($categoriesProducts) }})"
                        id='{{ $category->id }}' data-id="{{ $category->id }}">
                        @can('categories.destroy')
                            <a   class="pr-2" onClick="removeCategoryFromMarket({{ $category->id }})"
                                title="{{ trans('Remover categoria de este establecimiento') }}">
                                <i class="fa fa-minus-circle text-danger"></i>
                            </a>
                        @endcan
                        @if ($category->active == '0')
                            <span id="{{ $category->id }}_category_name"
                                class="desactivado alinearIzquierda textoNombre col-8 ">
                                {{ $category->name }}
                            </span>
                        @else
                            <span id="{{ $category->id }}_category_name" class="alinearIzquierda textoNombre col-8 ">
                                {{ $category->name }}
                            </span>
                        @endif
                        <span class="handle">
                            <i class="fa fa-sort"></i>
                        </span>
                        @can('categories.edit')
                            <a  data-toggle="modal" title="{{ trans('Editar esta categoria') }}"
                                onClick="setDateCategoryUpdate({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}',{{ $category->active }})"
                                data-target="#UpdateCategoryModal">
                                <i class=" fa fa-edit btnEditar" ></i>
                            </a>
                        @endcan
                        @can('categories.destroy')

                            <a  
                                onClick="alternarActivacionCategoria({{ $category->id }},'{{ $category->name }}','{{ $category->description }}',{{ $category->active }})">
                                <i class="fa fa-low-vision btn btn-link text-danger"></i>

                            </a>
                        @endcan
                    </li>

                @endforeach
            </ul>
        </div>
        <div id="AllProducts" class="marco_decorativo col-3 py-2">
            {!! Form::label('product_id', trans('Productos'), ['class' => 'col-12 control-label text-center
            textoBlanco']) !!}
            <div class="row">
                <div class="col-11 ml-3">
                    {!! Form::select('product_id', [], [], ['class' => 'select2 form-control ', 'id' =>
                    'IDProductosParaAgregar', 'multiple' => 'multiple']) !!}
                </div>
            </div>
            <div class="col-12 " id='btnAgregarProductos'></div>

            <div id="listaProductos" class=" col-12">
                <ul class="sort_products list-group" id="sort_products_sort">

                </ul>
            </div>
        </div>
        <div id="AllOptionsGroups" class="marco_decorativo col-3 py-2">
            {!! Form::label('categorias', trans('Grupos de Opciones'), ['class' => 'col-12 control-label text-center
            textoBlanco']) !!}
            <div class="row">
                <div class="col-11 ml-3">
                    {!! Form::select('categorias', [], [], ['class' => 'select2 form-control ', 'id' =>
                    'IDGrupoOpcionesParaAgregar', 'multiple' => 'multiple']) !!}
                </div>
            </div>
            <div class="col-12 " id='btnAgregarGrupoOpciones'></div>

            <div id="listaGrupoOpciones" class="col-12">
                <ul class="sort_products_grupo_opciones list-group" id='listaGrupoOpciones_sort'>

                </ul>
            </div>
        </div>
        <div id="AllOptions" class="marco_decorativo col-3 py-2">
            {!! Form::label('options', trans('Opciones'), ['class' => 'col-12 control-label text-center textoBlanco'])
            !!}
            <div class="row">
                <div class="col-11 ml-3">
                    {!! Form::select('options', [], [], ['class' => 'select2 form-control ', 'id' =>
                    'IDOpcionesParaAgregar', 'multiple' => 'multiple']) !!}
                </div>
            </div>
            <div class="col-12 " id='btnAgregarOpciones'></div>

            <div id="listaOpciones" class=" col-12">
                <ul class="sort_products_options list-group" id='listaOpciones_sort'>

                </ul>
            </div>
        </div>
        <div class=" col-3">

        </div>
        <div id="listaCategorias" class="marco_decorativo_categorias col-3">

            <ul class="sort_products_categories list-group" id='listaCategorias_sort'>

            </ul>
        </div>
    </div>

</div>


<!-- Save Field -->
<div class="form-group col-12 text-right">
    {{-- <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('Cambios')}}</button> --}}
    {{-- <a href="{!!  route('markets.index') !!}" class="btn btn-{{ setting('theme_color') }}"><i class="fa fa-save"></i>
    {{ trans('lang.save') }}{{ trans('Cambios') }}</a> --}}
    <a href="{!!  route('markets.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i>
        {{ trans('Volver') }}</a>
</div>

{{-- Modals --}}
<div class="modal fade" id="productEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">

            <div class="modal-header bg-blue">
                <h4 class="modal-title" id="myModalLabel">Editar Producto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" id="mhdnIdProduct">
                    <input type="hidden" id="mtxtMarketId">
                    <input type="hidden" id="mtxtCategoryId">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-7 control-label">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" name="mtxtNombre" class="form-control" id="mtxtNombre"
                                    placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-7 control-label">Precio</label>
                            <div class="col-sm-9">
                                <input type="text" name="mtxtPrice" class="form-control" id="mtxtPrice" value="">
                            </div>
                        </div>

                        {{-- <div class="form-group">
                  <label class="col-sm-7 control-label">Disponible</label>
                  <div class="col-sm-9"> --}}
                        {{-- <input type="text" name="mtxtDisponible" class="form-control" id="mtxtDisponible"> --}}
                        {{-- <div class="checkbox icheck">
                        <label class="col-9 ml-2 form-check-inline">
                            {!!  Form::hidden('#mtxtDisponible', 0) !!}
                            {!!  Form::checkbox('#mtxtDisponible', 1, null) !!}
                        </label>
                    </div> --}}
                        {{-- </div> --}}
                        {{-- </div> --}}
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="mbtnCerrarModal" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="mbtnUpdProduct">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="selectCategoryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">

            <div class="modal-header bg-blue">
                <h4 class="modal-title" id="myModalLabel">Agregar categorias</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="idMarketModal">
                <!-- Categories Field -->
                <div class="form-group row ">
                    {!! Form::label('categoriesProducts[]', trans('lang.product_categories_id'), ['class' => 'col-3
                    control-label text-right']) !!}
                    <div class="col-9">

                        {{-- {!!  Form::select('categoriesProducts[]', [], [], ['class' => 'select2 form-control', 'id' => 'inputSelectCategory', 'multiple' => 'multiple']) !!} --}}
                        <div class="form-text text-muted">{{ trans('Buscar entre las categorias existentes') }}</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="mCerrarModalSelectCategoria"
                    data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-info" id="mSeleccionarCategorias_old">Seleccionar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addProductsFromCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">

            <div class="modal-header bg-blue">
                <h4 class="modal-title" id="myModalLabel">Añadir o remover Categoria</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="idCategorySelectsProducts">

                {{-- <div class="form-group row ">
                {!!  Form::label('product_id', trans('lang.option_product_id'), ['class' => 'col-3 control-label text-right']) !!}
                <div class="col-9">
                  {!!  Form::select('product_id', $products, [], ['class' => 'select2 form-control', 'id' => 'products_select2', 'multiple' => 'multiple']) !!}
                  <div class="form-text text-muted">{{ trans("lang.option_product_id_help") }}</div>
                </div>
            </div> --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="mCerraAddProducts"
                    data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-info" id="mSelectsProducts">Seleccionar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createCategoryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">

            <div class="modal-header bg-blue">
                <h4 class="modal-title" id="myModalLabel">Crear Categoria</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="idMarketCreateCategoryModal">
                <!-- Name Field -->
                <div class="form-group row ">
                    {!! Form::label('name', trans('lang.category_name'), ['class' => 'col-3 control-label text-right'])
                    !!}
                    <div class="col-9">
                        {!! Form::text('', null, ['id' => 'nameCategoryCreateModal', 'class' => 'form-control',
                        'placeholder' => trans('lang.category_name_placeholder')]) !!}
                        <div class="form-text text-muted">
                            {{ trans('lang.category_name_help') }}
                        </div>
                    </div>
                </div>
                <div class="form-group row ">
                    {!! Form::label('chkActiveCreateCategory', trans('Activa'), ['class' => 'col-3 control-label
                    text-right']) !!}
                    <div class="col-9">
                        <div class="col-sm-9">
                            <input type="checkbox" checked name="chkActiveCreateCategory" style="transform: scale(1.5)"
                                id="chkActiveCreateCategory">
                        </div>
                    </div>
                </div>
                <!-- Description Field -->
                <div class="form-group row ">
                    {!! Form::label('description', trans('lang.category_description'), ['class' => 'col-3 control-label
                    text-right']) !!}
                    <div class="col-9">
                        {!! Form::text('', null, ['id' => 'descripcionCategoryCreateModal', 'class' => 'form-control',
                        'placeholder' => trans('lang.category_description_placeholder')]) !!}
                        <div class="form-text text-muted">{{ trans('lang.category_description_help') }}</div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="mCerrarCreateCategoryModal"
                    data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="mCrearCategoriaModal">Crear</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="UpdateCategoryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">

            <div class="modal-header bg-blue">
                <h4 class="modal-title" id="myModalLabel">Actualizar Categoria</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="idMarketUpdateCategoryModal">
                <input type="hidden" id="idCategoryUpdate">
                <!-- Name Field -->
                <div class="form-group row ">
                    {!! Form::label('name', trans('lang.category_name'), ['class' => 'col-3 control-label text-right'])
                    !!}
                    <div class="col-9">
                        {!! Form::text('', null, ['id' => 'nameCategoryUpdateModal', 'class' => 'form-control',
                        'placeholder' => trans('lang.category_name_placeholder')]) !!}
                        <div class="form-text text-muted">
                            {{ trans('lang.category_name_help') }}
                        </div>
                    </div>
                </div>
                <div class="form-group row ">
                    {!! Form::label('chkActiveUpdateCategory', trans('Activa'), ['class' => 'col-3 control-label
                    text-right']) !!}
                    <div class="col-9">
                        <div class="col-sm-9">
                            <input type="checkbox" name="chkActiveUpdateCategory" style="transform: scale(1.5)"
                                id="chkActiveUpdateCategory">
                        </div>
                    </div>
                </div>

                <!-- Description Field -->
                <div class="form-group row ">
                    {!! Form::label('description', trans('lang.category_description'), ['class' => 'col-3 control-label
                    text-right']) !!}
                    <div class="col-9">
                        {!! Form::text('', null, ['id' => 'descripcionCategoryUpdateModal', 'class' => 'form-control',
                        'placeholder' => trans('lang.category_description_placeholder')]) !!}
                        <div class="form-text text-muted">{{ trans('lang.category_description_help') }}</div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="mCerrarUpdateCategoryModal"
                    data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="mUpdateCategoriaModal">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="selectProductReady" tabindex="-1" role="dialog" aria-labelledby="myModalLabelProductReady">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">

            <div class="modal-header bg-blue">
                <h4 class="modal-title" id="myModalLabelProductReady">Seleccionar producto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">


            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="mCerrarModalSeleccionarProducto"
                    data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="mSeleccionarProducto">Crear</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalCreateOpcion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">

            <div class="modal-header bg-blue">
                <h4 class="modal-title" id="myModalLabel">Crear Opción</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" id="mGroupID">
                    <input type="hidden" id="mProductID">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="mtxtNombreOption">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" name="mtxtNombreOption" class="form-control" id="mtxtNombreOption"
                                    placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="mActiveOption">Activo</label>
                            <div class="col-sm-9">
                                <div class="checkbox icheck">
                                    <label class="col-9 ml-2 form-check-inline">
                                        <input type="checkbox" checked name="mActiveOption" id="mActiveOption">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="mtxtPriceOption">Precio</label>
                            <div class="col-sm-9">
                                <input class="form-control" min="0" value="0" placeholder="Precio del producto, ejem: 3"
                                    id="mtxtPriceOption" name="mtxtPriceOption" type="number">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="mbtnCerrarCreateOpcionModal"
                    data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="mCreateOpciones">Crear</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalUpdateOpcion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">

            <div class="modal-header bg-blue">
                <h4 class="modal-title" id="myModalLabel">Actualizar Opción</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" id="mGroupIDUPDATE">
                    <input type="hidden" id="mProductIDUPDATE">
                    <input type="hidden" id="mIdOpcionUpdate">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="mtxtNombreOptionUPDATE">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" name="mtxtNombreOptionUPDATE" class="form-control"
                                    id="mtxtNombreOptionUPDATE" placeholder="Nombre de la opcion">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class=" control-label" for="mActiveOptionUPD">Activo</label>
                            <input type="checkbox" name="mActiveOptionUPDATE" id="mActiveOptionUPDATE">

                        </div>
                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="mtxtPriceOptionUPDATE">Precio</label>
                            <div class="col-sm-9">
                                <input class="form-control" min="0" value="0" placeholder="Precio del producto, ejem: 3"
                                    id="mtxtPriceOptionUPDATE" name="mtxtPriceOptionUPDATE" type="number">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="mbtnCerrarUpdateOpcionModal"
                    data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="mUpdateOpciones">Actualizar</button>
            </div>
        </div>


    </div>

</div>

<div class="modal fade" id="ModalCreatGrupoOpcion" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabelCreateGrupoOption">

    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">

            <div class="modal-header bg-blue">
                <h4 class="modal-title" id="myModalLabelCreateGrupoOption">Crear Grupo de opciones</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" id="mOptionGroupIdProduct">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="mtxtNombreGroup">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" name="mtxtNombreGroup" class="form-control" id="mtxtNombreGroup"
                                    placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="chkForceSelect">Activo</label>
                            <div class="col-sm-9">
                                <div class="checkbox icheck">
                                    <label class="col-9 ml-2 form-check-inline">
                                        <input type="checkbox" checked name="mActive" id="mActive"
                                            style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="mtxtNombreAdmin">Nombre Administrativo</label>
                            <div class="col-sm-9">
                                <input type="text" name="mtxtNombreAdmin" class="form-control" id="mtxtNombreAdmin"
                                    placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="chkMultiSelect">Multi seleccionable</label>
                            <div class="col-sm-9">
                                <div class="checkbox icheck">
                                    <label class="col-9 ml-2 form-check-inline">
                                        <input type="checkbox" name="chkMultiSelect" id="multi"
                                            style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-12 control-label" for="cant_selectable">Cantidad seleccionable. 1 =
                                TODOS</label>
                            <div class="col-sm-9">
                                <input class="form-control" min="1" value="1" placeholder="3" id="cant_selectable"
                                    name="cant_selectable" type="number">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="chkForceSelect">Forzar Selección</label>
                            <div class="col-sm-9">
                                <div class="checkbox icheck">
                                    <label class="col-9 ml-2 form-check-inline">
                                        <input type="checkbox" class="iCheck" name="chkForceSelect" id="forceSelect"
                                            style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;">
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="mbtnCerrarGrupoOpcionesModal"
                    data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="mCreateGrupoOpciones">Crear</button>
            </div>
        </div>

    </div>

</div>

<div class="modal fade" id="ModalUpdateGrupoOpcion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">

            <div class="modal-header bg-blue">
                <h4 class="modal-title" id="myModalLabel">Actualizar Grupo de opciones</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" id="mOptionGroupIdProductUPDATE">
                    <input type="hidden" id="mIdGrupo">

                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="mtxtNombreGroupUPDATE">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" name="mtxtNombreGroupUPDATE" class="form-control"
                                    id="mtxtNombreGroupUPDATE" placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class=" control-label" for="chkActive">Activo</label>
                            <input type="checkbox" name="mActiveUpdate" id="mActiveUpdate">

                            <div class="form-group">
                                <label class="col-sm-7 control-label" for="mtxtNombreAdminUPDATE">Nombre
                                    Administrativo</label>
                                <div class="col-sm-9">
                                    <input type="text" name="mtxtNombreAdminUPDATE" class="form-control"
                                        id="mtxtNombreAdminUPDATE" placeholder="">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class=" control-label" for="chkMultiSelectUPDATE">Multi seleccionable</label>
                                <input type="checkbox" name="chkMultiSelectUPDATE" id="multiUPDATE">

                            </div>
                            <div class="form-group">
                                <label class="col-12 control-label" for="cant_selectableUPDATE">Cantidad seleccionable.
                                    1 = TODOS</label>
                                <div class="col-sm-9">
                                    <input class="form-control" min="1" value="1" placeholder="3"
                                        id="cant_selectableUPDATE" name="cant_selectable" type="number">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="chkForceSelectUPDATE">Forzar Selección</label>
                                <input type="checkbox" name="chkForceSelectUPDATE" id="forceSelectUPDATE">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="mbtnCerrarGrupoOpcionesModalUPDATE"
                    data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="mUpdateGrupoOpciones">Actualizar</button>
            </div>
        </div>
    </div>
</div>


<script src="https://unpkg.com/jquery@2.2.4/dist/jquery.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<link href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css" />
@prepend('scripts')
    <script type="text/javascript">
        //======================================================================
        // LOADING
        //======================================================================
        var Loading = (loadingDelayHidden = 0) => {

            //-----------------------------------------------------
            // Variables
            //-----------------------------------------------------
            // HTML
            let loading = null;
            // Retardo para borrar
            const myLoadingDelayHidden = loadingDelayHidden;
            // Imágenes
            let imgs = [];
            let lenImgs = 0;
            let counterImgsLoading = 0;

            //-----------------------------------------------------
            // Funciones
            //-----------------------------------------------------

            /**
             * Método que aumenta el contador de las imágenes cargadas
             */
            function incrementCounterImgs() {
                counterImgsLoading += 1;
                // Comprueba si todas las imágenes están cargadas
                if (counterImgsLoading === lenImgs) hideLoading();
            }

            /**
             * Ocultar HTML
             */
            function hideLoading() {

                // Comprueba que exista el HTML
                if (loading !== null) {
                    // Oculta el HTML de "cargando..." quitando la clase .show
                    loading.classList.remove('show');

                    // Borra el HTML
                    setTimeout(function() {
                        loading.remove();
                    }, myLoadingDelayHidden);
                }

            }

            /**
             * Método que inicia la lógica
             */
            function init() {
                /* Comprobar que el HTML esté cargadas */
                document.addEventListener('DOMContentLoaded', function() {
                    loading = document.querySelector('.loading');
                    imgs = Array.from(document.images);
                    lenImgs = imgs.length;

                    hideLoading();

                    /* Comprobar que todas las imágenes estén cargadas */
                    if (imgs.length === 0) {
                        // No hay ninguna
                    } else {
                        // Una o más
                        imgs.forEach(function(img) {
                            // A cada una le añade un evento que cuando se carge la imagen llame a la funcion incrementCounterImgs
                            img.addEventListener('load', incrementCounterImgs, false);
                        });
                    }
                });
            }

            return {
                'init': init
            }
        }
        // Para usarlo se declara e inicia. El número es el tiempo transcurrido para borra el HTML una vez cargado todos los elementos, en este caso 1 segundo: 1000 milisegundos,
        Loading(1000).init();

        $(document).ready(function() {
            searchProducts();
            searchCategory();
            searchOptionGroup();
            porbarLocalStore();
            searchOption();
            var idCategory = getIdCategoryLocalStore();
            var idProduct = getIdProductLocalStore();
            if (idCategory != null && idProduct == null) {
                document.getElementById(idCategory).className += ' select';
                getProduct(idCategory);
            }
            if (idCategory != null && idProduct != null) {
                document.getElementById(idCategory).className += ' select';
                getProduct(idCategory, idProduct);
            }

            function updateToDatabase(idString) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                $.ajax({
                    url: '{!!  url("categories/sort") !!}',
                    method: 'get',
                    data: {
                        ids: idString
                    },
                    success: function() {}

                })
            }



            var target = $('.sort_category');
            target.sortable({
                handle: '.handle',
                placeholder: 'highlight',
                axis: "y",
                update: function(e, ui) {
                    var sortData = target.sortable('toArray', {
                        attribute: 'data-id'
                    })
                    updateToDatabase(sortData.join(','))
                }
            });

        });

        // Funciones Para LocalStore
        function porbarLocalStore() {
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem("idMarket", {{ $market->id }});
                localStorage.setItem("titulo", "Curso de Angular avanzado - Víctor Robles");
                var idMarket = localStorage.getItem("idMarket");
                var idCategory = localStorage.getItem("idCategory");
                localStorage.removeItem("titulo");

            } else {

            }

        }

        function savedIdCategoryLocalStore(idCategory) {
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem("idCategory", idCategory);
            }
        }

        function getIdCategoryLocalStore() {
            var idCategory;
            if (typeof(Storage) !== "undefined") {
                var idCategory = localStorage.getItem("idCategory");
                localStorage.removeItem("idCategory");
            }
            return idCategory;
        }

        function savedIdProductLocalStore(idProduct) {
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem("idProduct", idProduct);
            }


        }

        function savedIdProductLocalStoreFromSearchOptionGroup(idProduct) {
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem("idProductOptionGroup", idProduct);
            }
        }

        function getIdProductFromSearchOptionGroupLocalStore() {
            var idProduct;
            if (typeof(Storage) !== "undefined") {
                idProduct = localStorage.getItem("idProductOptionGroup");
            }
            return idProduct;
        }

        function getIdProductLocalStore() {
            var idProduct;
            if (typeof(Storage) !== "undefined") {
                idProduct = localStorage.getItem("idProduct");
                localStorage.removeItem("idProduct");
            }
            return idProduct;
        }

        function savedIdProductAndIdCategoryLocalStore(idProduct, idCategory) {
            if (typeof(Storage) !== "undefined") {
                savedIdProductLocalStore(idProduct);
                savedIdCategoryLocalStore(idCategory);
            }
        }
function guardarIdProduct(idProduct){

}

function conseguirIdProduct(){

}
        function darFormatoPrecio(precio) {
            const formatter = new Intl.NumberFormat('es-HN', {
                style: 'currency',
                currency: 'HNL',
                minimumFractionDigits: 2
            });

            return formatter.format(precio);
        }

        $('#mbtnUpdProduct').click(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            var idP = $('#mhdnIdProduct').val();
            var nom = $('#mtxtNombre').val();
            var pri = $('#mtxtPrice').val();
            var dis = $('#mtxtDisponible').val();


            $('#mbtnCerrarModal').click();
            $.ajax({
                url: '{!!  url("product/updateModal") !!}',
                method: 'GET',
                data: {
                    id: idP,
                    name: nom,
                    price: pri,
                    featured: dis,

                },
                success: function(res) {
                    $('#mbtnCerrarModal').click();
                    location.reload();

                }
            })

        });



        // Funciones de elminaciones
        function deleteProduct(idRecibida) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $.ajax({
                url: '{!!  url("product/destroyAlt") !!}',
                method: 'get',
                data: {
                    id: idRecibida
                },
                success: function() {
                    location.reload();
                }

            });


        }

        function deleteGrupoOptions(idOptionGroup, idProduct) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            $.ajax({
                url: '{!!  url("optionGroups/destroyAlt") !!}',
                method: 'get',
                data: {
                    id: idOptionGroup
                },
                success: function() {
                    document.getElementById('listaOpciones').innerHTML = '';
                    recargrarGrupoOpciones(idProduct);
                }

            });
        }

        function deleteOption(IdOption, idOptionGroup, IdProduct) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $.ajax({
                url: '{!!  url("options/destroyAlt") !!}',
                method: 'get',
                data: {
                    id: IdOption
                },
                success: function() {
                    recargrarOpciones(idOptionGroup, IdProduct);
                }

            });
        }

        // Funciones para las categorias
        setMarketIdSelectCategories = function(idMarketModal, idCategory) {
            $('#idMarketModal').val(idMarketModal);
            $('#idMarketCreateCategoryModal').val(idMarketModal);
            $('#idCategoryUpdate').val(idCategory);

        }

        setDateCategoryUpdate = function(idCategory, name, description, active) {
            var stringHTML = '<p>'
            var descriptionFilter = '';
            if (description.includes(stringHTML)) {
                descriptionFilter = $(description).text();
            } else {
                descriptionFilter = description;
            }
            if (active == '1') {
                $('#chkActiveUpdateCategory').prop("checked", true);

            } else {

                $('#chkActiveUpdateCategory').prop("checked", false);
            }

            $('#idCategoryUpdate').val(idCategory);
            $('#nameCategoryUpdateModal').val(name);

            $('#descripcionCategoryUpdateModal').val(descriptionFilter);
        }

        function removeCategory(idCategory, idMarket) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            $.ajax({
                url: '{!!  url("categories/remove") !!}',
                method: 'get',
                data: {
                    id: idCategory,
                    idM: idMarket,
                },
                success: function() {
                    location.reload();
                }

            });
        }

        function alternarActivacionCategoria(idCategory, nameCategory, descriptionCat, activeC) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            var nameC = nameCategory;
            var descriptionC = descriptionCat;
            var idC = idCategory;

            if (activeC == '1') {
                activeC = '0';
            } else {
                activeC = '1';
            }

            if (nameC.length <= 0) {
                alert("Ingrese un nombre");
            }
            if (descriptionC.length <= 0) {
                descriptionC = '--';                
                // alert("Ingrese una descripción valida");
            }
            if (nameC.length > 0 && descriptionC.length > 0) {
                $.ajax({
                    url: '{!!  url("categories/updateFromMarket") !!}',
                    method: 'get',
                    data: {
                        name: nameC,
                        activeCM: activeC,
                        description: descriptionC,
                        idCa: idC,
                        idMarket: {{ $market->id }},
                    },
                    success: function(res) {
                        // alert(res);
                        // $('#inputSelectCategory').val('').change();
                        location.reload();
                    }

                });
            }


        }

        $('#mUpdateCategoriaModal').click(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            var activeC = $('input:checkbox[name=chkActiveUpdateCategory]:checked').val();
            var nameC = $('#nameCategoryUpdateModal').val();
            var descriptionC = $('#descripcionCategoryUpdateModal').val();
            var idC = $('#idCategoryUpdate').val();

            if (activeC == null) {
                activeC = '0';
            } else {
                activeC = '1';
            }
            if (nameC.length <= 0) {
                alert("Ingrese un nombre");
            }
            if (descriptionC.length <= 0) {
                alert("Ingrese una descripción valida");
            }
            if (nameC.length > 0 && descriptionC.length > 0) {
                $.ajax({
                    url: '{!!  url("categories/updateFromMarket") !!}',
                    method: 'get',
                    data: {
                        name: nameC,
                        activeCM: activeC,
                        description: descriptionC,
                        idCa: idC,
                        idMarket: {{ $market->id }},
                    },
                    success: function(res) {
                        // alert(res);
                        // $('#inputSelectCategory').val('').change();
                        location.reload();
                    }

                });
            }


        });

        $('#mCrearCategoriaModal').click(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            var activeC = $('input:checkbox[name=chkActiveCreateCategory]:checked').val();

            if (activeC == null) {
                activeC = '0';
            } else {
                activeC = '1';
            }
            var nameC = $('#nameCategoryCreateModal').val();
            var descriptionC = $('#descripcionCategoryCreateModal').val();
            var idMarket = $('#idMarketCreateCategoryModal').val();
            if (nameC.length <= 0) {
                alert("Ingrese un nombre");
            }
            if (descriptionC.length <= 0) {
                alert("Ingrese una descripción valida");
            }
            if (nameC.length > 0 && descriptionC.length > 0) {
                $.ajax({
                    url: '{!!  url("categories/storeFromMarket") !!}',
                    method: 'get',
                    data: {
                        name: nameC,
                        activeCM: activeC,
                        description: descriptionC,
                        categoriesProducts: [idMarket]
                    },
                    success: function(res) {
                        // alert(res);
                        // $('#inputSelectCategory').val('').change();
                        location.reload();
                    }

                });
            }


        });

        $('#mSeleccionarCategorias').click(function() {
            var idsCategories = $('#inputSelectCategory').val();
            var idMarket = {{ $market->id }};
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            if (idsCategories != null) {
                $.ajax({
                    url: '{!!  url("categories/selects") !!}',
                    method: 'get',
                    data: {
                        categoriesProducts: idsCategories,
                        id: idMarket,
                    },
                    success: function(res) {
                        // alert(res);
                        // $('#inputSelectCategory').val('').change();
                        location.reload();
                    }

                });
            }

        });

        function selectCategory(idCategory, countCategory) {
            var target = $('.sort_category');
            var sortData = target.sortable('toArray', {
                attribute: 'data-id',
            })

            for (let index = 0; index < countCategory; index++) {
                document.getElementById(sortData[index]).className = 'list-group-item  btn btn-success item';
            }
            document.getElementById(idCategory).className += ' select ';
            document.getElementById('listaCategorias').innerHTML = ``;
            document.getElementById('listaGrupoOpciones').innerHTML = ``;
            document.getElementById('listaOpciones').innerHTML = ``;
            vaciarSelects2();
            vaciarListaOpciones();
            vaciarListaGrupoOpciones();
            getProduct(idCategory)
        }

        function removeCategoryFromMarket(idCategory) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            $.ajax({
                url: '{!!  url("categories/removeCategoryFromMarket") !!}',
                method: 'get',
                data: {
                    idC: idCategory,
                    idMarket: {{ $market->id }},
                },
                success: function() {
                    location.reload();
                }

            });
        }
        // Funciones para los Productos
        function getProduct(idCategory, idProduct) {
            var priceProduct = 0;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            document.getElementById('listaProductos').innerHTML = 'Buscando productos . . . ';
            $.ajax({
                url: '{!!  url("products/getproductbycategory") !!}',
                method: 'get',
                data: {
                    id: idCategory,
                    idMarket: {{ $market->id }},
                },
                success: function(res) {
                    vaciarSelects2();
                    document.getElementById('btnAgregarProductos').innerHTML = `
                    <a href="createFromMarket/{{ $market->id }}" class="CuartoDeCentrado" onClick="savedIdCategoryLocalStore(${idCategory})" title="{{ trans('Crear y añadir un nuevo producto') }}"  style="color: black"  >
                            <i class="fa fa-plus-circle cruz_circulo"   ></i>
                    </a>
                    <a   id="IDProductosParaAgregar" onClick="AgregarProductos(${idCategory})" class="CuartoDeCentrado" data-placement="bottom" title="{{ trans('Añadir productos existente') }}" style="color: black"  >
                        <i class="fa fa-plus-square cruz_cuadrado"  ></i>  
                    </a>
            `;
                    if ((res).length > 0) {



                        document.getElementById('listaProductos').innerHTML = `
                    
                    <ul class="sort_products list-group " id='sort_products_sort'>
                        
                    </ul>
                    `;

                        res.forEach(product => {
                            
                            priceProduct = darFormatoPrecio(product["price"]);

                            if (product['featured'] == '1') {
                                document.getElementById('sort_products_sort').innerHTML += `
                                    <li class="list-group-item  btn btn-success item" id='${product['id']}'  data-id="${product['id']}">
                                        @can('products.destroy')    
                                                <a   onClick="removeProductsFromCategory(${product['id']},${idCategory})" title="{{ trans('Eliminar de la categoria actual') }}">
                                                    <i class="fa fa-minus-circle text-danger" ></i>
                                                </a>
                                        @endcan

                                        <div id="${product['id']}_name" class="textoNombre col-8" onclick="getCategoriAndOptionGroupByProduct(${product['id']}, ${res.length},'${product['name']}')">
                                            ${product['name']}
                                            <div  class="textoNombre" >
                                                <b> ${priceProduct}</b>
                                            </div>
                                            
                                        </div>
                                        <span class="handle">
                                            <i class="fa fa-sort"></i>
                                        </span>

                                        @can('products.edit')    

                                                <a href="editFromMarket/${product['id']}"  onClick="savedIdProductAndIdCategoryLocalStore(${product['id']},${idCategory})">
                                                    <i class=" fa fa-edit btnEditar"></i>
                                                </a>
                                        @endcan
                                        @can('products.destroy')

                                                <a   onClick="cambiarDisponibilidad(${product['id']},${product['featured']},${idCategory})">
                                                    <i class="fa fa-low-vision btn btn-link text-danger"></i>
                                                </a>
                                        @endcan
                                    </li>

                                `;

                            } else {
                                document.getElementById('sort_products_sort').innerHTML += `
                        
                            <li class="list-group-item  btn btn-success item" id='${product['id']}'  data-id="${product['id']}">
                            @can('products.destroy')    
                                    <a   onClick="removeProductsFromCategory(${product['id']},${idCategory})" title="{{ trans('Eliminar de la categoria actual') }}">
                                        <i class="fa fa-minus-circle text-danger"></i>
                                    </a>
                            @endcan

                            <div id="${product['id']}_name" class="textoNombre col-8" onclick="getCategoriAndOptionGroupByProduct(${product['id']}, ${res.length}, '${product['name']}')">
                                <div class="desactivado">
                                        ${product['name']}
                                </div>
                                <div  class=" Limpiar" >
                                        <b> ${priceProduct}</b>
                                </div>
                            </div>
                            <span class="handle">
                                <i class="fa fa-sort"></i>
                            </span>
                            
                            @can('products.edit')    
                                    <a href="editFromMarket/${product['id']}"  onClick="savedIdProductAndIdCategoryLocalStore(${product['id']},${idCategory})">
                                        <i class=" fa fa-edit btnEditar"></i>
                                    </a>
                            @endcan
                            @can('products.destroy')

                                    <a   onClick="cambiarDisponibilidad(${product['id']},${product['featured']},${idCategory})">
                                        <i class="fa fa-low-vision btn btn-link text-danger"></i>
                                    </a>
                            @endcan
                        </li>
                    `;
                            }

                        });

                        function updateToDatabase(idString) {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });

                            $.ajax({
                                url: '{!!  url("menu/update-order") !!}',
                                method: 'POST',
                                data: {
                                    ids: idString
                                },
                                success: function() {
                                    // alert(idString);
                                }

                            })
                        }



                        var target = $('.sort_products');
                        target.sortable({
                            handle: '.handle',
                            placeholder: 'highlight',
                            axis: "y",
                            update: function(e, ui) {
                                var sortData = target.sortable('toArray', {
                                    attribute: 'data-id'
                                })
                                
                                updateToDatabase(sortData.join(','))

                            }
                        });

                    } else {

                        document.getElementById('listaProductos').innerHTML = `
                    <div class="">
                 
                    {!!  Form::label('categories', trans('------ Lista Vacía ------ '), ['class' => ' control-label text-left textoBlanco']) !!}
                    
                </div>
                    `;
                    }
                    if (idProduct != null) {
                        document.getElementById(idProduct).className += ' select';
                    }
                }

            });
        }

        selectIdProduct = function(idProduct ) {
            $('#mOptionGroupIdProduct').val(idProduct);
        };

        function cambiarDisponibilidad(idProducto, featuredProducto, idCategory) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            if (!featuredProducto) {
                featuredProducto = "1";
            } else {
                featuredProducto = "0";
            }

            $.ajax({
                url: '{!!  url("product/cambiarDisponibilidad") !!}',
                method: 'get',
                data: {
                    id: idProducto,
                    idC: idCategory,
                    featured: featuredProducto,
                },
                success: function(res) {
                    if (res['success'] == 'true') {

                        vaciarSelects2();
                        // getProduct(idCategory);
                        recargarProductos(idCategory);

                    } else {
                    }
                }

            });

        }

        selectProduct = function(idProduct, nombre, price, disponible) {
            $('#mhdnIdProduct').val(idProduct);
            $('#mtxtNombre').val(nombre);
            $('#mtxtPrice').val(price);
            $('#mtxtDisponible').val(disponible);
            vaciarSelects2();

        };

        function AgregarProductos(idCategory) {


            const IDSP = $('#IDProductosParaAgregar').val();

            if (IDSP != null) {
            vaciarSelects2();
                vaciarListaOpciones();
                vaciarListaGrupoOpciones();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                $.ajax({
                    url: '{!!  url("categories/addProductFormMarket") !!}',
                    method: 'get',
                    data: {
                        idMarket: {{ $market->id }},
                        idC: idCategory,
                        category_id:idCategory,
                        products: IDSP,
                    },
                    success: function(res) {
                        // alert(res);
                        recargarProductos(idCategory);
                    }
                })
            }
        }

        function removeProductsFromCategory(idProducts, idCategory) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $.ajax({
                url: '{!!  url("product/removeProductsFromCategory") !!}',
                method: 'get',
                data: {
                    id: idProducts,
                    idC: idCategory,
                },
                success: function() {
                    vaciarListaOpciones();
                    vaciarListaGrupoOpciones();
                    recargarProductos(idCategory);

                }

            });
        }

        function recargarProductos(idCategory) {
            getProduct(idCategory);
        }
        // Funciones para las categorias (Obsoleto)
        function getCategoriAndOptionGroupByProduct(idProduct, countProduct, pName) {
            var target = $('.sort_products');
            var sortData = target.sortable('toArray', {
                attribute: 'data-id'
            })

            for (let index = 0; index < countProduct; index++) {
                document.getElementById(sortData[index]).className = 'list-group-item  btn btn-success item';
            }
            vaciarSelects2();
            document.getElementById(idProduct).className += ' select';
            savedIdProductLocalStoreFromSearchOptionGroup(idProduct)
            selectIdProduct(idProduct);
            vaciarListaOpciones();
            document.getElementById('listaOpciones').innerHTML = "";
            getCategoriesByProduct(idProduct, pName);
            getOptionGroupByProduct(idProduct);

        }

        function getCategoriesByProduct(idProduct, pName) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            document.getElementById('listaCategorias').innerHTML = 'Buscando Categorias . . . ';

            $.ajax({
                url: '{!!  url("categories/categoriesProduct") !!}',
                method: 'get',
                data: {
                    id: idProduct
                },
                success: function(res) {
                    if ((res).length > 0) {

                        document.getElementById('listaCategorias').innerHTML = `
                    
                    {!!  Form::label('categories', trans('Categoria(s) de: ${pName}'), ['class' => 'col-9 control-label text-left textoBlanco']) !!}

                    <ul class="sort_products_categories list-group" id='listaCategorias_sort'>
                      
                    </ul>
                    `;

                        res.forEach(category => {
                            if (category != null) {
                                document.getElementById('listaCategorias_sort').innerHTML += `

                                <li  class="list-group-item "id="${category['id']}_category" data-id="${category['id']}_category">
                                    

                                        <span class="textoNombre col-10" >
                                            ${category['name']}
                                        </span>
                                    
                                    
                                </li> 

                                </div>
                            `;
                            }
                        });



                    } else {
                        document.getElementById('listaCategorias').innerHTML = `
                    <div class="">
                    {!!  Form::label('categories', trans('Categorias'), ['class' => 'col-9 control-label text-left textoBlanco']) !!}
                    
                    {!!  Form::label('categories', trans('------ El producto ${pName} no esta asociado a ninguna categoria ------ '), ['class' => ' control-label text-left textoBlanco']) !!}
                </div>
                    `;
                    }

                }

            });
        }


        // Funciones para los Grupos de Opciones.
        function getOptionGroupByProduct(idProduct, idGrupoRec) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            document.getElementById('listaGrupoOpciones').innerHTML = 'Buscando Grupo de Opciones . . . ';

            $.ajax({
                url: '{!!  url("optiongroup/groupbyproduct") !!}',
                method: 'get',
                data: {
                    id: idProduct
                },
                //   data:'json',
                success: function(res) {
                    document.getElementById('btnAgregarGrupoOpciones').innerHTML = `
                    <a style="color: black" onClick="selectIdProduct(${idProduct})" class="CuartoDeCentrado"  title="{{ trans('Crear y añadir un nuevo Grupo de Opciones') }}" data-toggle="modal" data-target="#ModalCreatGrupoOpcion">
                        <i class="fa fa-plus-circle cruz_circulo" ></i>
                    </a>
                    <a   id="mSeleccionarCategorias" onClick="AgregarGrupoOpciones(${idProduct})" class="CuartoDeCentrado" data-placement="bottom" title="{{ trans('Añadir Grupos existente') }}" style="color: black"  >
                        <i class="fa fa-plus-square cruz_cuadrado"  ></i>  
                    </a>
                    `;
                    if ((res).length > 0) {

                        document.getElementById('listaGrupoOpciones').innerHTML = `
                    <ul class="sort_products_grupo_opciones list-group" id='listaGrupoOpciones_sort'>
                    
                        </ul>
                    `;


                        res.forEach(grupoOpcion => {

                            if (grupoOpcion['active'] == '1') {
                                document.getElementById('listaGrupoOpciones_sort').innerHTML += `
                        
                        <li  class="list-group-item  btn btn-success item"id="${grupoOpcion['id']}_grupoOpcion"  data-id="${grupoOpcion['id']}_grupoOpcion">
                            @can('optionGroups.destroy')    
                                <a onClick="removeOptionGroupFromProduct(${idProduct},${grupoOpcion['id']})" title="{{ trans('Remover este grupo de opciones') }}">
                                        <i class="fa fa-minus-circle text-danger" ></i>
                                </a>
                            @endcan
                                <span class="textoNombre col-8" onClick="selectGrupoOpcionList('${grupoOpcion['id']}_grupoOpcion',${res.length},${grupoOpcion['id']},${idProduct})">
                                    ${grupoOpcion['name']}
                                </span>
                            <span class="handle ">
                                <i class="fa fa-sort"></i>
                            </span> 
                            @can('products.edit')
                                    
                                    <a    onClick="selectIdProductUpdate(${idProduct},${grupoOpcion['id']},'${grupoOpcion['name']}', '${grupoOpcion['name_admin']}',${grupoOpcion['cant_selectable']},${grupoOpcion['active']}, ${grupoOpcion['multi']},${grupoOpcion['force_select']})" data-toggle="modal" data-target="#ModalUpdateGrupoOpcion" >
                                        <i class=" fa fa-edit btnEditar"></i>
                                    </a>
                            @endcan
                            @can('optionGroups.destroy')

                                    <a   onClick="alternarActivacionGrupoOpciones(${idProduct},${grupoOpcion['id']},'${grupoOpcion['name']}', '${grupoOpcion['name_admin']}',${grupoOpcion['cant_selectable']},${grupoOpcion['active']}, ${grupoOpcion['multi']},${grupoOpcion['force_select']})">
                                        <i class="fa fa-low-vision btn btn-link text-danger"></i>
                                    </a>
                            @endcan
                        </li> 
                        
                </div>
                    `;
                            } else {
                                document.getElementById('listaGrupoOpciones_sort').innerHTML += `
                        
                        <li  class="list-group-item  btn btn-success item  "id="${grupoOpcion['id']}_grupoOpcion"  data-id="${grupoOpcion['id']}_grupoOpcion">
                            @can('products.destroy')    
                                <a onClick="removeOptionGroupFromProduct(${idProduct},${grupoOpcion['id']})" title="{{ trans('Remover este grupo de opciones') }}">
                                        <i class="fa fa-minus-circle text-danger" ></i>
                                </a>
                            @endcan
                                <span class="textoNombre col-8 desactivado" onClick="selectGrupoOpcionList('${grupoOpcion['id']}_grupoOpcion',${res.length},${grupoOpcion['id']},${idProduct})">
                                    ${grupoOpcion['name']}
                                </span>
                            <span class="handle ">
                                <i class="fa fa-sort"></i>
                            </span> 
                            @can('products.edit')
                                    
                                    <a    onClick="selectIdProductUpdate(${idProduct},${grupoOpcion['id']},'${grupoOpcion['name']}', '${grupoOpcion['name_admin']}',${grupoOpcion['cant_selectable']},${grupoOpcion['active']}, ${grupoOpcion['multi']},${grupoOpcion['force_select']})" data-toggle="modal" data-target="#ModalUpdateGrupoOpcion" >
                                        <i class=" fa fa-edit btnEditar"></i>
                                    </a>
                            @endcan
                            @can('products.destroy')

                                    <a   onClick="alternarActivacionGrupoOpciones(${idProduct},${grupoOpcion['id']},'${grupoOpcion['name']}', '${grupoOpcion['name_admin']}',${grupoOpcion['cant_selectable']},${grupoOpcion['active']}, ${grupoOpcion['multi']},${grupoOpcion['force_select']})">
                                        <i class="fa fa-low-vision btn btn-link text-danger"></i>
                                    </a>
                            @endcan
                        </li> 
                        
                </div>
                    `;
                            }

                        });

                        function updateSortOrdenGroupOption(idString) {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            $.ajax({
                                url: '{!!  url("option_group/sort_orden") !!}',
                                method: 'POST',
                                data: {
                                    ids: idString
                                },
                                success: function() {

                                }

                            })

                        }
                        var targetCategories = $('.sort_products_grupo_opciones');
                        targetCategories.sortable({
                            handle: '.handle',
                            placeholder: 'highlight',
                            axis: "y",
                            update: function(e, ui) {
                                var sortData = targetCategories.sortable('toArray', {
                                    attribute: 'data-id'
                                });
                                updateSortOrdenGroupOption(sortData.join(','));

                            }
                        });

                    } else {
                        document.getElementById('listaGrupoOpciones').innerHTML = `
                    <div class="">
                    {!!  Form::label('categories', trans('------ Lista Vacía ------ '), ['class' => ' control-label text-left textoBlanco']) !!}
                    
                </div>
                    `;
                    }
                    if (idGrupoRec != null) {
                        document.getElementById(idGrupoRec + "_grupoOpcion").className += ' select';
                    }
                }

            });
        }

        selectIdProductUpdate = function(idProduct, idGrupo, nombre, nombreAdmin, cantSelect, active, multi, force_select) {
           
            $('#mIdGrupo').val(idGrupo);
            $('#mOptionGroupIdProductUPDATE').val(idProduct);
            $('#mtxtNombreGroupUPDATE').val(nombre);
            if (nombreAdmin == 'null') nombreAdmin = '';
            $('#mtxtNombreAdminUPDATE').val(nombreAdmin);
            if (active == "1") {
                $('#mActiveUpdate').prop("checked", true);
            }
            if (multi == "1") {
                $('#multiUPDATE').prop("checked", true);
            }

            if (force_select == "1") {
                $('#forceSelectUPDATE').prop("checked", true);
            }
            $('#cant_selectableUPDATE').val(cantSelect);

        };

        function selectGrupoOpcionList(idGrupoOpcion, countCategories, idGrupoOpcionOriginal, idProduct) {
            var target = $('.sort_products_grupo_opciones');
            var sortData = target.sortable('toArray', {
                attribute: 'data-id'
            })

            for (let index = 0; index < countCategories; index++) {
                document.getElementById(sortData[index]).className = 'list-group-item  btn btn-success item';
            }
            document.getElementById('listaOpciones').innerHTML = ' ';

            document.getElementById(idGrupoOpcion).className += ' select';
            getOptions(idGrupoOpcionOriginal, idProduct);

        }

        function alternarActivacionGrupoOpciones(idProduct, idGrupoRec, nombre, nombreAdmin, cantSelect, activeRec, multiSe,
            force_select) {
            if (activeRec == "1") {
                activeRec = "0"
            } else {

                activeRec = "1"
            }

            $.ajax({
                url: '{!!  url("optionGroups/updateFromMarket") !!}',
                method: 'get',
                data: {
                    active: activeRec,
                    idGrupo: idGrupoRec,
                    name: nombre,
                    id_producto: idProduct,
                    name_admin: nombreAdmin,
                    multi: multiSe,
                    cant_selectable: cantSelect,
                    force_select: force_select,
                },
                success: function(res) {
                    recargrarGrupoOpciones(idProduct, idGrupoRec);
                }
            })

        }

        function AgregarGrupoOpciones(idProduct) {


            const IDGO = $('#IDGrupoOpcionesParaAgregar').val();
            vaciarSelects2();
            if (IDGO != null) {
                vaciarListaOpciones();

    
                document.getElementById('listaOpciones').innerHTML = ' ';

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                $.ajax({
                    url: '{!!  url("optionGroups/addOptionGroupsFromMarket") !!}',
                    method: 'get',
                    data: {
                        idMarket: {{ $market->id }},
                        idP: idProduct,
                        optionGroupsList: IDGO,
                    },
                    success: function(res) {

                        recargrarGrupoOpciones(idProduct);
                    }
                })
            }
        }


        function removeOptionGroupFromProduct(idProduct, idGrupo) {
            document.getElementById('listaOpciones').innerHTML = ' ';
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $.ajax({
                url: '{!!  url("optionGroups/removeOptionGroupFromProduct") !!}',
                method: 'get',
                data: {
                    id: idProduct,
                    idG: idGrupo,
                },
                success: function(res) {
                    vaciarListaOpciones();

                    recargrarGrupoOpciones(idProduct);
                }

            });
        }

        $('#mCreateGrupoOpciones').click(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            var idP = $('#mOptionGroupIdProduct').val();
            var nom = $('#mtxtNombreGroup').val();
            var nomAdmin = $('#mtxtNombreAdmin').val();
            var activeVar = $('input:checkbox[name=mActive]:checked').val();
            var multiSe = $('input:checkbox[name=chkMultiSelect]:checked').val();
            var cantSelectable = $('#cant_selectable').val();
            var forceSelect = $('input:checkbox[name=chkForceSelect]:checked').val();


            if (multiSe == null) {
                multiSe = '0';
            } else {
                multiSe = '1';
            }
            if (activeVar == null) {
                activeVar = '0';
            } else {
                activeVar = '1';
            }
            if (forceSelect == null) {
                forceSelect = '0';
            } else {
                forceSelect = '1';
            }

            if (nom.length == 0) {

                alert('El nombre es obligatorio');
            }
            if (nomAdmin.length == 0) {

                alert('El nombre administrativo es obligatorio');
            }
            if (cantSelectable <= 0) {

                alert('La cantidad seleccionable no es valida');
            }

            if (cantSelectable > 0 && nomAdmin.length > 0 && nom.length > 0) {
                document.getElementById('listaOpciones').innerHTML = ' ';

                $('#mbtnCerrarGrupoOpcionesModal').click();

                $.ajax({
                    url: '{!!  url("optionGroups/createFromMarket") !!}',
                    method: 'get',
                    data: {
                        name: nom,
                        active: activeVar,
                        id_producto: idP,
                        name_admin: nomAdmin,
                        multi: multiSe,
                        market_id: {{ $market->id }},
                        cant_selectable: cantSelectable,
                        force_select: forceSelect,
                        optionGroupsList: [idP]
                    },
                    success: function(res) {
                        console.log(res);
                        $('#mtxtNombreGroup').val('');
                        $('#mtxtNombreAdmin').val('');
                        $('input:checkbox[name=chkMultiSelect]:checked').val(null);
                        $('#cant_selectable').val(1);
                        $('input:checkbox[name=chkForceSelect]:checked').val(null);
                        $('#mbtnCerrarGrupoOpcionesModal').click();

                        recargrarGrupoOpciones(idP);

                    }
                })
            }
        });

        $('#mUpdateGrupoOpciones').click(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            var idGrupoOpcion = $('#mIdGrupo').val();
            var activeVar = $('input:checkbox[name=mActiveUpdate]:checked').val();
            var idP = $('#mOptionGroupIdProductUPDATE').val();
            var nom = $('#mtxtNombreGroupUPDATE').val();
            var nomAdmin = $('#mtxtNombreAdminUPDATE').val();
            var multiSe = $('input:checkbox[name=chkMultiSelectUPDATE]:checked').val();
            var cantSelectable = $('#cant_selectableUPDATE').val();
            var forceSelect = $('input:checkbox[name=chkForceSelectUPDATE]:checked').val();

            if (activeVar == null) {
                activeVar = '0';
            } else {
                activeVar = '1';
            }

            if (multiSe == null) {
                multiSe = '0';
            } else {
                multiSe = '1';
            }


            if (forceSelect == null) {
                forceSelect = '0';
            } else {
                forceSelect = '1';
            }

            if (nom.length == 0) {

                alert('El nombre es obligatorio');
            }
            if (nomAdmin.length == 0) {

                alert('El nombre administrativo es obligatorio');
            }
            if (cantSelectable <= 0) {

                alert('La cantidad seleccionable no es valida');
            }
            if (activeVar == null) {
                activeVar = "0";
            }
            if (cantSelectable > 0 && nomAdmin.length > 0 && nom.length > 0) {
                $('#mbtnCerrarGrupoOpcionesModalUPDATE').click();

                $.ajax({
                    url: '{!!  url("optionGroups/updateFromMarket") !!}',
                    method: 'get',
                    data: {
                        active: activeVar,
                        idGrupo: idGrupoOpcion,
                        name: nom,
                        id_producto: idP,
                        name_admin: nomAdmin,
                        multi: multiSe,
                        cant_selectable: cantSelectable,
                        force_select: forceSelect,
                    },
                    success: function(res) {

                        $('#mtxtNombreGroupUPDATE').val('');
                        $('#mtxtNombreAdminUPDATE').val('');
                        $('input:checkbox[name=chkMultiSelectUPDATE]:checked').val(null);
                        $('#cant_selectableUPDATE').val(1);
                        $('input:checkbox[name=chkForceSelectUPDATE]:checked').val(null);
                        $('#mbtnCerrarGrupoOpcionesModalUPDATE').click();

                        recargrarGrupoOpciones(idP);

                    }
                })
            }

        });

        function recargrarGrupoOpciones(idProducto, idGrupoRec) {
            getOptionGroupByProduct(idProducto, idGrupoRec);
        }
        // Funciones para las opciones
        function getOptions(idGrupoOpcion, idProducto, IdOpcionUpdate) {
            var priceOption = 0;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            document.getElementById('listaOpciones').innerHTML = 'Buscando Opciones . . . ';

            $.ajax({
                url: '{!!  url("options/optionbygroup") !!}',
                method: 'get',
                data: {
                    id: idGrupoOpcion,
                    market_id: {{ $market->id }},
                },
                success: function(res) {
                    vaciarSelects2();
                    document.getElementById('btnAgregarOpciones').innerHTML = `
                    <a   style="color: black" onClick="selectIdGrupoOpcion(${idGrupoOpcion}, ${idProducto})" title="{{ trans('Crear y añadir un nueva Opción') }}" class="CuartoDeCentrado" data-toggle="modal" data-target="#ModalCreateOpcion">
                        <i class="fa fa-plus-circle cruz_circulo"  ></i>
                    </a>
                    <a   id="mSeleccionarCategorias" onClick="AgregarOpciones(${idGrupoOpcion},${idProducto})" class="CuartoDeCentrado" data-placement="bottom" title="{{ trans('Añadir productos existente') }}" style="color: black"  >
                        <i class="fa fa-plus-square cruz_cuadrado"  ></i>  
                    </a>
                    `;
                    if ((res).length > 0) {
                        document.getElementById('listaOpciones').innerHTML = `
                    <ul class="sort_products_options list-group" id='listaOpciones_sort'>
                      
                    </ul>
                    `;

                        res.forEach(opciones => {
                            priceOption = darFormatoPrecio(opciones["price"]);

                            if (opciones['active'] == '1') {
                                document.getElementById('listaOpciones_sort').innerHTML += `
                        
                        <li  class="list-group-item   "id="${opciones['id']}_opciones" onClick="selectOpcion('${opciones['id']}_opciones',${res.length})" data-id="${opciones['id']}_opciones">
                            @can('options.destroy')    
                                    <a   onClick="removeFromOptiongGroup(${opciones['id']},${idProducto},${idGrupoOpcion})">
                                        <i class="fa fa-minus-circle text-danger"></i>
                                    </a>
                            @endcan
                            <div id="${opciones['id']}_option_name" class="textoNombre col-8">
                                ${opciones['name']}
                                <div  class="textoNombre" >
                                    <b> ${priceOption}</b>
                                </div>
                            </div>

                            <span class="handle ">
                                <i class="fa fa-sort" style="color: white; padding-left:4px"></i>
                            </span> 
                                @can('products.edit')
                                    
                                    <a    onClick="selectIdGrupoOpcionUpdate(${idGrupoOpcion},${idProducto},${opciones['id']},'${opciones['name']}',${opciones['price']},${opciones['active']})" data-toggle="modal" data-target="#ModalUpdateOpcion" >
                                        <i class=" fa fa-edit btnEditar"></i>
                                    </a>
                            @endcan
                            @can('products.destroy')

                                    <a   onClick="alternarActivacionOpcion(${idGrupoOpcion},${idProducto},${opciones['id']},'${opciones['name']}',${opciones['price']},${opciones['active']})">
                                        <i class="fa fa-low-vision btn btn-link text-danger"></i>
                                    </a>
                            @endcan
                        </li> 
                        
                </div>
                    `;
                            } else {
                                document.getElementById('listaOpciones_sort').innerHTML += `
                        
                        <li  class="list-group-item   "id="${opciones['id']}_opciones" onClick="selectOpcion('${opciones['id']}_opciones',${res.length})" data-id="${opciones['id']}_opciones">
                            @can('options.destroy')    
                                    <a   onClick="removeFromOptiongGroup(${opciones['id']},${idProducto},${idGrupoOpcion})">
                                        <i class="fa fa-minus-circle text-danger"></i>
                                    </a>
                            @endcan
                            <div id="${opciones['id']}_option_name" class="textoNombre col-8">
                                <div class="desactivado">
                                        ${opciones['name']}
                                </div>
                                <div  class="Limpiar" >
                                        <b> ${priceOption}</b>
                                </div>
                            </div>
                            <span class="handle ">
                                <i class="fa fa-sort" style="color: white; padding-left:4px"></i>
                            </span>
                            @can('products.edit')
                                    
                                    <a    onClick="selectIdGrupoOpcionUpdate(${idGrupoOpcion},${idProducto},${opciones['id']},'${opciones['name']}',${opciones['price']},${opciones['active']})" data-toggle="modal" data-target="#ModalUpdateOpcion" >
                                        <i class=" fa fa-edit btnEditar"></i>
                                    </a>
                            @endcan
                            @can('products.destroy')

                                    <a   onClick="alternarActivacionOpcion(${idGrupoOpcion},${idProducto},${opciones['id']},'${opciones['name']}',${opciones['price']},${opciones['active']})">
                                        <i class="fa fa-low-vision btn btn-link text-danger"></i>
                                    </a>
                            @endcan
                        </li> 
                        
                </div>
                    `;
                            }

                        });

                        function updateSortOrdenOption(idString) {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            $.ajax({
                                url: '{!!  url("options/sort_orden") !!}',
                                method: 'POST',
                                data: {
                                    ids: idString
                                },
                                success: function() {

                                }

                            })

                        }
                        var targetCategories = $('.sort_products_options');
                        targetCategories.sortable({
                            handle: '.handle',
                            placeholder: 'highlight',
                            axis: "y",
                            update: function(e, ui) {
                                var sortData = targetCategories.sortable('toArray', {
                                    attribute: 'data-id'
                                });
                                updateSortOrdenOption(sortData.join(','));

                            }
                        });

                    } else {
                        document.getElementById('listaOpciones').innerHTML = `
                    <div class="">
                    {!!  Form::label('Ninguna', trans('------ Lista Vacía ------ '), ['class' => ' control-label text-left textoBlanco']) !!}
                </div>
                    `;
                    }
                    if (IdOpcionUpdate != null) {
                        // document.getElementById(IdOpcionUpdate+'_opciones').className += ' select';
                    }
                }

            });
        }

        selectIdGrupoOpcion = function(idGroup, idProduct) {
            $('#mGroupID').val(idGroup);
            $('#mProductID').val(idProduct);
        };

        selectIdGrupoOpcionUpdate = function(idGroup, idProduct, idOpcion, nombre, price, active) {
            $('#mGroupIDUPDATE').val(idGroup);
            $('#mProductIDUPDATE').val(idProduct);
            $('#mIdOpcionUpdate').val(idOpcion);
            $('#mtxtNombreOptionUPDATE').val(nombre);
            $('#mtxtPriceOptionUPDATE').val(price);

            if (active == "1") {
                $('#mActiveOptionUPDATE').prop("checked", true);
            }
        };

        function selectOpcion(idOpcion, countOpcion) {
            // var target = $('.sort_products_options');
            // var sortData = target.sortable('toArray', {
            //     attribute: 'data-id'
            // })

            // for (let index = 0; index < countOpcion; index++) {
            //     document.getElementById(sortData[index]).className = 'list-group-item  btn btn-success item';
            // }
            // document.getElementById(idOpcion).className += ' select';

        }

        function alternarActivacionOpcion(idGroup, idProduct, idOpcion, nombre, priceRec, active) {
            if (active == "1") {
                active = "0"
            } else {

                active = "1"
            }

            $.ajax({
                url: '{!!  url("options/updateFroMarkert") !!}',
                method: 'get',
                data: {
                    id: idOpcion,
                    name: nombre,
                    price: priceRec,
                    active: active,
                    option_group_id: idGroup,
                    product_id: idProduct,
                },
                success: function(res) {
                    $('#mtxtNombreOption').val('');
                    $('#mtxtPriceOption').val(1);
                    $('#mbtnCerrarUpdateOpcionModal').click();
                    recargrarOpciones(idGroup, idProduct);

                }
            });

        }

        $('#mUpdateOpciones').click(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            var idGrupo = $('#mGroupIDUPDATE').val();
            var idProduct = $('#mProductIDUPDATE').val();
            var IdOpcionUpdate = $('#mIdOpcionUpdate').val();
            var nameOption = $('#mtxtNombreOptionUPDATE').val();
            var chkActive = $('input:checkbox[name=mActiveOptionUPDATE]:checked').val();
            var txtPrice = $('#mtxtPriceOptionUPDATE').val();


            if (nameOption.length <= 0) {
                alert("El nombre es necesario");
            }
            if (txtPrice < 0) {
                txtPrice = 0;
            }

            if (chkActive == null) {
                chkActive = '0';
            } else {
                chkActive = '1';
            }

            if (nameOption.length > 0 && txtPrice >= 0) {
                $('#mbtnCerrarUpdateOpcionModal').click();
                console.log(IdOpcionUpdate);

                $.ajax({
                    url: '{!!  url("options/updateFroMarkert") !!}',
                    method: 'get',
                    data: {
                        id: IdOpcionUpdate,
                        name: nameOption,
                        price: txtPrice,
                        active: chkActive,
                        option_group_id: idGrupo,
                        product_id: idProduct,
                    },
                    success: function(res) {

                        $('#mtxtNombreOption').val('');
                        $('#mtxtPriceOption').val('1');
                        $('#mbtnCerrarUpdateOpcionModal').click();

                        recargrarOpciones(idGrupo, idProduct, IdOpcionUpdate);

                    }
                })
            }

        });

        $('#mCreateOpciones').click(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            var idGrupo = $('#mGroupID').val();
            var idProduct = $('#mProductID').val();
            var nameOption = $('#mtxtNombreOption').val();
            var chkActive = $('input:checkbox[name=mActiveOption]:checked').val();
            var txtPrice = $('#mtxtPriceOption').val();


            if (nameOption.length <= 0) {
                alert("El nombre es necesario");
            }
            if (txtPrice < 0) {
                alert("Precio negativo no es permitido");
            }

            if (chkActive == null) {
                chkActive = '0';
            } else {
                chkActive = '1';
            }

            if (nameOption.length > 0 && txtPrice >= 0) {
                $('#mbtnCerrarCreateOpcionModal').click();
                $.ajax({
                    url: '{!!  url("options/createFromMarket") !!}',
                    method: 'get',
                    data: {
                        name: nameOption,
                        price: txtPrice,
                        active: chkActive,
                        option_group_id: idGrupo,
                        product_id: idProduct,
                        optionGroupList: [idGrupo],
                        market_id: {{ $market->id }},
                    },
                    success: function(res) {
                        $('#mtxtNombreOption').val('');
                        $('#mtxtPriceOption').val(1);
                        $('#mbtnCerrarCreateOpcionModal').click();

                        recargrarOpciones(idGrupo, idProduct);

                    }
                })
            }
        });

        function AgregarOpciones(idGrupoOpcion, idProduct) {
            const IDO = $('#IDOpcionesParaAgregar').val();
            if (IDO != null) {

                vaciarSelects2();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                $.ajax({
                    url: '{!!  url("options/addOptionsFromMarket") !!}',
                    method: 'get',
                    data: {

                        idGO: idGrupoOpcion,
                        optionsList: IDO,
                        idMarket: {{ $market->id }},
                        market_id: {{ $market->id }},
                    },
                    success: function(res) {

                        recargrarOpciones(idGrupoOpcion, idProduct);
                    }
                })
            }
        }

        function removeFromOptiongGroup(idOpcion, idProducto, idGrupo) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $.ajax({
                url: '{!!  url('options/removeOptionFromOptionGroup') !!}',
                method: 'get',
                data: {
                    id: idOpcion,
                    idG: idGrupo,
                    idP:idProducto,
                    idMarket: {{ $market->id }},
                },
                success: function(res) {
                    recargrarOpciones(idGrupo,idProducto);
                }

            });
        }

        function recargrarOpciones(idGrupo, idProduct, IdOpcionUpdate) {
            getOptions(idGrupo, idProduct, IdOpcionUpdate);
        }
        // Funciones para Busqueda
        function searchProducts() {

            $('#IDProductosParaAgregar').select2({
                placeholder: 'Buscar Productos',
                minimumInputLength: 2,
                language: {

                    noResults: function() {

                        return "No hay resultado.";
                    },
                    searching: function() {

                        return "Buscando..";
                    },

                    inputTooShort: function() {
                        return "Escribe por lo menos 2 letras para buscar";
                    },
                },

                ajax: {
                    url: '{!!  url("search/productsFromMarket") !!}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            idMarket: {{ $market->id }},
                            palabra: params.term
                        };
                    },
                },
                success: function(res) {
                }
            });
        }

        function searchCategory() {
            $('#inputSelectCategory').select2({
                placeholder: 'Buscar Categorías',
                allowClear: true,
                minimumInputLength: 2,
                language: {

                    noResults: function() {

                        return "No hay resultado.";
                    },
                    searching: function() {

                        return "Buscando..";
                    },

                    inputTooShort: function() {
                        return "Escribe por lo menos 2 letras para buscar";
                    },
                },

                ajax: {
                    url: '{!!  url("search/categories") !!}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            categoria: params.term
                        };
                    },
                },
                success: function(res) {
                }
            });
        }

        function searchOptionGroup() {
            $('#IDGrupoOpcionesParaAgregar').select2({
                placeholder: 'Buscar Grupo de Opciones',
                minimumInputLength: 2,

                language: {

                    noResults: function() {

                        return "No hay resultado.";
                    },
                    searching: function() {

                        return "Buscando..";
                    },

                    inputTooShort: function() {
                        return "Escribe por lo menos 2 letras para buscar";
                    },
                },

                ajax: {
                    url: '{!!  url("search/optiongroupsFromMarket") !!}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            idMarket: {{ $market->id }},
                            optiongroup: params.term,
                            idProduct: getIdProductFromSearchOptionGroupLocalStore(),
                        };
                    },
                },
            });
        }

        function searchOption() {
            $('#IDOpcionesParaAgregar').select2({
                placeholder: 'Buscar Opción',
                minimumInputLength: 2,

                language: {

                    noResults: function() {

                        return "No hay resultado.";
                    },
                    searching: function() {

                        return "Buscando..";
                    },

                    inputTooShort: function() {
                        return "Escribe por lo menos 2 letras para buscar";
                    },
                },

                ajax: {
                    url: '{!!  url("search/options") !!}',
                    dataType: 'json',
                    // processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'

                    // return {
                    //     results: data.items
                    // };
                    // },
                    data: function(params) {

                        return {
                            option: params.term,
                            market_id: {{ $market->id }},
                        };
                    },
                },
            });
        }
        // Funciones mixtas
        function vaciarSelects2() {
            $('#IDOpcionesParaAgregar').val('').change();
            $('#IDProductosParaAgregar').val('').change();
            $('#IDGrupoOpcionesParaAgregar').val('').change();

        }

        function vaciarListaProducto() {
            if (document.getElementById('btnAgregarProductos') != null) {
                document.getElementById('btnAgregarProductos').innerHTML = ``;
            }
            if (document.getElementById('listaProductos') != null) {
                document.getElementById('listaProductos').innerHTML = `
                   
                <ul class="sort_products list-group" id="sort_products_sort">

                </ul>
            
            `;
            }

        }

        function vaciarListaGrupoOpciones() {
            if (document.getElementById('btnAgregarGrupoOpciones') != null) {
                document.getElementById('btnAgregarGrupoOpciones').innerHTML = ``;
            }
            if (document.getElementById('listaGrupoOpciones') != null) {
                document.getElementById('listaGrupoOpciones').innerHTML = `
                <ul class="sort_products list-group" id="listaGrupoOpciones_sort">

                </ul>
            `;
            }
        }

        function vaciarListaOpciones() {
            if (document.getElementById('btnAgregarOpciones') != null) {
                document.getElementById('btnAgregarOpciones').innerHTML = ``;
            }
            if (document.getElementById('listaOpciones') != null) {
                document.getElementById('listaOpciones').innerHTML = `
                <ul class="sort_products list-group" id="listaOpciones_sort">

                </ul>
            `;
            }

        }

    </script>
@endprepend
