@push('css_lib')
    <!-- iCheck -->
    <!-- select2 -->
    <!-- bootstrap wysihtml5 - text editor -->
    {{--dropzone--}}
    {{--Color Picker--}}
    <link rel="stylesheet" href="{{asset('plugins/colorpicker/bootstrap-colorpicker.min.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    @endpush

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
    .cajaColor{
        background-color: blue;
        border:1px  solid black ;
        height: 20px;
        width: 20px;
        margin-right: 10px;
        border-radius: 20px;
    }
    .colorpicker { z-index: 9999; } 
</style>
@section('settings_title',trans('lang.app_setting_mobile'))

<div class="loading show">
    <div class="spin"></div>
</div>
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
    <!-- Name Field -->
    <div class="form-group row ">
        {!! Form::label('name', trans('Supermercado: '), ['class' => 'col-5 control-label text-right']) !!}
        <div class="col-7">
            {{ $market->name }}
        </div>
    </div>
</div>


{{-- Deptarmentos y subdepartamentos --}}


{{-- Secciones, Categorias, Productos, Grupo de Opciones, Opciones --}}
<div class="container py-2" >

    <div class="row" style="justify-content: space-around">
        <div id="AllSection" class="marco_decorativo col-3 py-2">
            {!! Form::label('sections[]', trans('Secciones'), ['class' => 'col-12 control-label text-center
            textoBlanco']) !!}
            <div class="row">
                <div class="col-11 ml-3">
                    {!! Form::select('sections[]', [], [], ['class' => 'select2 form-control ', 'id' =>
                    'idSectionsToAdd', 'multiple' => 'multiple']) !!}
                </div>
            </div>
            <div class="col-12 " id='btnAddSections'></div>

            <div id="listSections" class=" col-12">
                <ul class="sort_sections list-group" id="sort_sections_sort">

                </ul>
            </div>
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
        {{-- <div id="listaCategorias" class="marco_decorativo_categorias col-3"> --}}
            <div id="listaCategorias" class="">

            <ul class="sort_products_categories list-group" id='listaCategorias_sort'>
        
            </ul>
        </div>      
       
    </div>

</div>

{{-- Lista de promociones --}}
<div class="container py-2" >

    <div class="row" style="justify-content: space-around">
       
        <div id="AllPromo" class="marco_decorativo col-3 py-2">
            {!! Form::label('promos', trans('Promociones'), ['class' => 'col-12 control-label text-center
            textoBlanco']) !!}
            {{-- <div class="row">
                <div class="col-11 ml-3">
                    {!! Form::select('promos', [], [], ['class' => 'select2 form-control ', 'id' =>
                    'IdPromos', 'multiple' => 'multiple']) !!}
                </div>
            </div>
            <div class="col-12 text-center py-2" id='btnAgregarPromos'>
                <a   id="IDProductosParaAgregar" onClick="AgregarPromos()"  data-placement="bottom" title="{{ trans('Añadir productos existente a promociones') }}" style="color: black"  >
                    <i class="fa fa-plus-square cruz_cuadrado"  ></i>  
                </a>
            </div> --}}

            <div id="listaPromociones" class=" col-12">
                <ul class="sort_promos list-group" id="sort_promos_sort">

                </ul>
            </div>
        </div>
        <div  class=" col-3 py-2">
          
        </div>
        <div  class=" col-3 py-2">
            
        </div>
      {{-- <div id="listaCategorias" class="marco_decorativo_categorias col-3"> --}}
        <div id="listaCategorias" class="">

            <ul class="sort_products_categories list-group" >
        
            </ul>
        </div>
    </div>

</div>



<!-- Save Field -->
<div class="form-group col-12 text-right">
    {{-- <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('Cambios')}}</button> --}}
    {{-- <a href="{!!  route('convenience_stores.index') !!}" class="btn btn-{{ setting('theme_color') }}"><i class="fa fa-save"></i>
    {{ trans('lang.save') }}{{ trans('Cambios') }}</a> --}}
    <a href="{!!  route('convenience_stores.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i>
        {{ trans('Volver') }}</a>
</div>

{{-- MODALS --}}

{{-- Secciones --}}
<div class="modal fade" id="ModalCreateSection" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">

            <div class="modal-header bg-blue">
                <h4 class="modal-title" id="myModalLabel">Crear Sección</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <form class="form-horizontal">

                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="mtxtNameSection">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" name="mtxtNameSection" class="form-control" id="mtxtNameSection"
                                    placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="mtxtDecriptionSection">Descripción</label>
                            <div class="col-sm-9">
                                <input type="text" name="mtxtDecriptionSection" class="form-control" id="mtxtDecriptionSection"
                                    placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="mActiveSection">Activa</label>
                            <div class="col-sm-9">
                                <div class="checkbox icheck">
                                    <label class="col-9 ml-2 form-check-inline">
                                        <input type="checkbox" checked name="mActiveSection" id="mActiveSection">
                                    </label>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="mbtnCloseCreateSection"
                    data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="mCreateSection">Crear</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalUpdateSection" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">

            <div class="modal-header bg-blue">
                <h4 class="modal-title" id="myModalLabel">Actualizar Sección</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" id="mIdSectionUpdate">
 
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="mtxtNameSectionUpdate">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" name="mtxtNameSectionUpdate" class="form-control" id="mtxtNameSectionUpdate"
                                    placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-7 control-label" for="mtxtDecriptionSectionUpdate">Descripción</label>
                            <div class="col-sm-9">
                                <input type="text" name="mtxtDecriptionSectionUpdate" class="form-control" id="mtxtDecriptionSectionUpdate"
                                    placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class=" control-label" for="chkActive">Activa</label>
                            <input type="checkbox" name="mActiveSectionUpdate" id="mActiveSectionUpdate">
                            {{-- <label class="col-sm-7 control-label" for="mActiveSectionUpdate">Activa</label>
                            <div class="col-sm-9">
                                <div class="checkbox icheck">
                                    <label class="col-9 ml-2 form-check-inline">
                                        <input type="checkbox" name="mActiveSectionUpdate" id="mActiveSectionUpdate">
                                    </label>
                                </div>
                            </div> --}}
                        </div>
                       
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="mbtnCerrarUpdateSection"
                    data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="mUpdateSection">Actualizar</button>
            </div>
        </div>
    </div>

</div>

{{-- Opciones --}}
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

{{-- grupo de opciones --}}
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
        // Para usarlo se declara e inicia. El número es el tiempo transcurrido para borra el HTML una vez cargado todos los elementos, en esté caso 1 segundo: 1000 milisegundos,
        Loading(1000).init();

        $(document).ready(function() {
            getSections();
            saveSupermarketId();
            searchSection();
            searchProducts();
            searchProductsPromo();
            searchCategory();
            searchOptionGroup();
            searchOption();
            getPromos();
            var check = checkChangeSomeThing();
            var idSection = getIdSection();
            var idProduct = getIdProductLocalStore();
            
            if(check != null){
                if(idSection != null){
                 recargrarSecciones(idSection);

                }
                if(idProduct != null && idSection != null){
                    recargarProductos(idSection,idProduct);
                }
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
        function checkChangeSomeThing() {
            var check;
            if (typeof(Storage) !== "undefined") {
                check = localStorage.getItem("CreatingProduct");
                localStorage.removeItem("CreatingProduct");

            }
            return check;
        }
        function setStatusChangeSomeThing() {
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem("CreatingProduct", 'true');
            }
        }
        function saveSupermarketId() {
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem("supermarket_id", {{$market->id}});
            }
        }
        function getIdMarket() {
            var idMarket = '0';
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem("idMarket", {{$market->id}});
                idMarket = localStorage.getItem("idMarket");
            }
            return idMarket;
        }

        function savedIdMarketLocalStore() {
            setStatusChangeSomeThing();
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem("idMarket", getIdMarket());
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
                setStatusChangeSomeThing();
                 localStorage.setItem("idProduct", idProduct);
            }
        }

        function savedIdSection(idSection) {
         
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem("idSection", idSection);
            }
        }

        function getIdSection() {
         var idSection = '0';
         if (typeof(Storage) !== "undefined") {
            idSection = localStorage.getItem("idSection");
         }
         return idSection;
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

        // Funciones para las Secciones 
        function getSections(idSection){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            document.getElementById('listSections').innerHTML = 'Cargando Secciones';

            $.ajax({
                url: '{!!  url("sections/getSectionsByConvenienceStores") !!}',
                method: 'get',
                data: {
                    idMarket: getIdMarket(),
                },
                success: function(res) {
                    vaciarSelects2();
                    document.getElementById('btnAddSections').innerHTML = `
                        <a  class="CuartoDeCentrado" title="{{ trans('Crear y añadir una nueva sección') }}" data-toggle="modal" data-target="#ModalCreateSection"  style="color: black"  >
                            <i class="fa fa-plus-circle cruz_circulo"   ></i>
                        </a>
                             <a  onClick="AgregarSecciones()" class="CuartoDeCentrado" data-placement="bottom" title="{{ trans('Añadir secciones ya existente') }}" style="color: black"  >
                             <i class="fa fa-plus-square cruz_cuadrado"  ></i>  
                        </a>
                    `;
                    if ((res).length > 0) {
                        document.getElementById('listSections').innerHTML = `
                            <ul class="sort_sections list-group " id='sort_sections_sort'>
                                
                            </ul>
                        `;
                        res.forEach(section => {

                            if (section['active'] == '1') {
                                document.getElementById('sort_sections_sort').innerHTML += `
                                    <li class="list-group-item  btn btn-success item" id='${section['id']}_section'  data-id="${section['id']}">
                                        @can('sections.destroy')    
                                            <a   onClick="removeSectionFromMarket(${section['id']})" title="{{ trans('Remover de la tienda actual') }}">
                                                <i class="fa fa-minus-circle text-danger" ></i>
                                            </a>
                                        @endcan

                                        <div id="${section['id']}_name" class="textoNombre col-8" onclick="selectSection(${section['id']}, ${res.length})">
                                            ${section['name']}
                                        </div>
                                        <span class="handle">
                                            <i class="fa fa-sort"></i>
                                        </span>

                                        @can('sections.edit')
                                            <a onClick="setDataSection(${section['id']},'${section['name']}','${section['description']}', ${section['active']})" data-toggle="modal" data-target="#ModalUpdateSection" title="{{ trans('Editar esta sección') }}">
                                                <i class=" fa fa-edit btnEditar"></i>
                                            </a>
                                        @endcan
                                        @can('sections.destroy')
                                            <a   onClick="alternarDisponibilidadSeccion(${section['id']},'${section['name']}','${section['description']}', ${section['active']})" title="{{ trans('Activar/Desactivar sección') }}">
                                                <i class="fa fa-low-vision btn btn-link text-danger"></i>
                                            </a>
                                        @endcan
                                    </li>
                                `;
                            } else {
                                document.getElementById('sort_sections_sort').innerHTML += `
                                
                                    <li class="list-group-item  btn btn-success item" id='${section['id']}_section'  data-id="${section['id']}">
                                        @can('sections.destroy')    
                                            <a   onClick="removeSectionFromMarket(${section['id']})" title="{{ trans('Remover de la tienda actual') }}">
                                                <i class="fa fa-minus-circle text-danger" ></i>
                                            </a>
                                        @endcan

                                        <div id="${section['id']}_name" class="textoNombre col-8" onclick="selectSection(${section['id']}, ${res.length})">
                                            <div class="desactivado">
                                                    ${section['name']}
                                            </div>
                                        </div>
                                        <span class="handle">
                                                <i class="fa fa-sort"></i>
                                        </span>

                                        @can('sections.edit')
                                            <a onClick="setDataSection(${section['id']}, '${section['name']}', '${section['description']}', ${section['active']})" data-toggle="modal" data-target="#ModalUpdateSection" title="{{ trans('Editar esta sección') }}">
                                                <i class=" fa fa-edit btnEditar"></i>
                                            </a>
                                        @endcan
                                        @can('sections.destroy')
                                            <a   onClick="alternarDisponibilidadSeccion(${section['id']},'${section['name']}','${section['description']}', ${section['active']})" title="{{ trans('Activar/Desactivar sección') }}">
                                                <i class="fa fa-low-vision btn btn-link text-danger"></i>
                                            </a>
                                        @endcan
                                    </li>
                                `;
                            }

                        });

                        function updateToSortSection(idString) {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            $.ajax({
                                url: '{!!  url("sections/updateSort") !!}',
                                method: 'POST',
                                data: {
                                    ids: idString
                                },
                                success: function() {
                                }

                            })
                        }
                        var target = $('.sort_sections');
                        target.sortable({
                            handle: '.handle',
                            placeholder: 'highlight',
                            axis: "y",
                            update: function(e, ui) {
                                var sortData = target.sortable('toArray', {
                                    attribute: 'data-id'
                                })                                
                                updateToSortSection(sortData.join(','))
                            }
                        });

                        if (idSection != null) {
                            document.getElementById(idSection+'_section').className += ' select';
                        }
                    } else {
                        document.getElementById('listSections').innerHTML = `
                            <div class="">
                                {!!  Form::label('', trans('------ Lista Vacía ------ '), ['class' => ' control-label  text-center textoBlanco']) !!}
                            </div>
                        `;
                    }

                }

            });
        }

        function selectSection(idSection, countProduct) {
            var target = $('.sort_sections');
            var sortData = target.sortable('toArray', {
                attribute: 'data-id'
            })

            for (let index = 0; index < countProduct; index++) {
                document.getElementById(sortData[index]+'_section').className = 'list-group-item  btn btn-success item';
            }
            document.getElementById(idSection+'_section').className += ' select';
            vaciarSelects2();
            savedIdSection(idSection);
            vaciarListaOpciones();
            vaciarListaGrupoOpciones();
            vaciarListaProducto();

            document.getElementById('listaOpciones').innerHTML = "";
            document.getElementById('btnAgregarProductos').innerHTML = "";
            document.getElementById('listaGrupoOpciones').innerHTML = "";
            document.getElementById('btnAgregarGrupoOpciones').innerHTML = "";
            document.getElementById('listaProductos').innerHTML = "";
            document.getElementById('btnAgregarOpciones').innerHTML = "";
            getProductBySection(idSection);

        }

        function setDataSection(idSection, nameSection, descriptionSection, activeSection){
            $('#mIdSectionUpdate').val(idSection);
            var stringHTML = '<p>'
            var descriptionFilter = '';
            if (descriptionSection.includes(stringHTML)) {
                descriptionFilter = $(descriptionSection).text();
            } else {
                descriptionFilter = descriptionSection;
            }

            $('#mtxtNameSectionUpdate').val(nameSection);
            $('#mtxtDecriptionSectionUpdate').val(descriptionFilter);
            if (activeSection) {
                $('#mActiveSectionUpdate').prop("checked", true);
            }
        }

        function alternarDisponibilidadSeccion(idSection, nameSection, descriptionSection, activeSection){
            var stringHTML = '<p>'
            var descriptionFilter = '';
            if (descriptionSection.includes(stringHTML)) {
                descriptionFilter = $(descriptionSection).text();
            } else {
                descriptionFilter = descriptionSection;
            }

            if (activeSection) {

            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            chkActive = '1';
            if (activeSection) {
                chkActive = '0';
            } else {
                chkActive = '1';
            }

            $.ajax({
                url: '{!!  url("sections/updateFromMarket") !!}',
                method: 'get',
                data: {
                    idS:         idSection,
                    name:        nameSection,
                    description: descriptionFilter,
                    activeHalf:      chkActive,
                    idMarket: getIdMarket(),
                },
                success: function(res) {
                    $('#mtxtNameSection').val('');
                    $('#mtxtDecriptionSection').val('');
                    // $('#mActiveSection').prop("checked", true);

                    recargrarSecciones();

                }
            })
        }
        
        function removeSectionFromMarket(idSection){
            $.ajax({
                url: '{!!  url("sections/removeFromMarket") !!}',
                method: 'get',
                data: {
                    idS:         idSection,
                    idMarket: getIdMarket(),
                },
                success: function(res) {
                    recargrarSecciones();
                }
            })
        }
        
        $('#mCreateSection').click(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });


            var nameOption = $('#mtxtNameSection').val();
            var descriptionSection = $('#mtxtDecriptionSection').val();
            var chkActive = $('input:checkbox[name=mActiveSection]:checked').val();

            if (nameOption.length <= 0) {
                alert("El nombre es necesario");
            }
           
            if (chkActive == null) {
                chkActive = '0';
            } else {
                chkActive = '1';
            }

            if (nameOption.length > 0) {
                $('#mbtnCloseCreateSection').click();
                $.ajax({
                    url: '{!!  url("sections/createFromMarket") !!}',
                    method: 'get',
                    data: {
                        name: nameOption,
                        description: descriptionSection,
                        active: chkActive,
                        markets: [getIdMarket()],
                    },
                    success: function(res) {
                        $('#mtxtNameSection').val('');
                        $('#mtxtDecriptionSection').val('');
                        // $('#mActiveSection').prop("checked", true);
                        vaciarListaOpciones();
                        vaciarListaGrupoOpciones();
                        vaciarListaProducto();
                        recargrarSecciones();

                    }
                })
            }
        });

        $('#mUpdateSection').click(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            var idSection = $('#mIdSectionUpdate').val();
            var nameOption = $('#mtxtNameSectionUpdate').val();
            var descriptionSection = $('#mtxtDecriptionSectionUpdate').val();
            var chkActive = $('input:checkbox[name=mActiveSectionUpdate]:checked').val();

            if (nameOption.length <= 0) {
                alert("El nombre es necesario");
            }
           
            if (chkActive == null) {
                chkActive = '0';
            } else {
                chkActive = '1';
            }

            if (nameOption.length > 0) {
                $('#mbtnCerrarUpdateSection').click();
                $.ajax({
                    url: '{!!  url("sections/updateFromMarket") !!}',
                    method: 'get',
                    data: {
                        idS:         idSection,
                        name:        nameOption,
                        description: descriptionSection,
                        activeHalf:      chkActive,
                        idMarket: getIdMarket(),
                    },
                    success: function(res) {
                        $('#mtxtNameSection').val('');
                        $('#mtxtDecriptionSection').val('');
                        // $('#mActiveSection').prop("checked", true);

                        recargrarSecciones();

                    }
                })
            }
        });

        function AgregarSecciones() {
            const idsSection = $('#idSectionsToAdd').val();

            if (idsSection != null) {
                vaciarSelects2();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                $.ajax({
                    url: '{!!  url("sections/addFromMarket") !!}',
                    method: 'get',
                    data: {
                        sections: idsSection,
                        idMarket: getIdMarket(),
                    },
                    success: function(res) {
                        recargrarSecciones();
                    }
                })
            }
        }
        function recargrarSecciones(idSection){
            getSections(idSection);
        }

        // Funciones para las categorias
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
                        idMarket: getIdMarket(),
                    },
                    success: function(res) {
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
                        idMarket: getIdMarket(),
                    },
                    success: function(res) {
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
                        // $('#inputSelectCategory').val('').change();
                        location.reload();
                    }

                });
            }


        });

        $('#mSeleccionarCategorias').click(function() {
            var idsCategories = $('#inputSelectCategory').val();
            var idMarket = getIdMarket();
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
                document.getElementById(sortData[index]+'_category').className = 'list-group-item  btn btn-success item';
            }
            document.getElementById(idCategory+'_category').className += ' select ';
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
                    idMarket: getIdMarket(),
                },
                success: function() {
                    location.reload();
                }

            });
        }
        // Funciones para los Productos
        
        function getProductBySection(idSection,reloadList,idProduct) {
            var priceProduct = 0;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            if(reloadList == null){
                document.getElementById('listaProductos').innerHTML = 'Buscando productos . . . ';
            }
            else{
                document.getElementById('listaProductos').innerHTML = '. . . ';
            }
            $.ajax({
                url: '{!!  url("products/getproductbysection") !!}',
                method: 'get',
                data: {
                    id: idSection,
                    idMarket: getIdMarket(),
                },
                
                success: function(res) {
                    vaciarSelects2();
                    document.getElementById('btnAgregarProductos').innerHTML = `
                        <a href="createFromConvenienceStores/${getIdMarket()}" class="CuartoDeCentrado" onClick="setStatusChangeSomeThing()"  title="{{ trans('Crear y añadir un nuevo producto') }}"  style="color: black"  >
                            <i class="fa fa-plus-circle cruz_circulo"   ></i>
                        </a>
                             <a   id="IDProductosParaAgregar" onClick="AgregarProductos(${idSection})" class="CuartoDeCentrado" data-placement="bottom" title="{{ trans('Añadir productos existente') }}" style="color: black"  >
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
                                    <li class="list-group-item  btn btn-success item" id='${product['id']}_product'  data-id="${product['id']}">
                                        @can('products.destroy')    
                                                <a   onClick="removeProductsFromSection(${product['id']},${idSection})" title="{{ trans('Eliminar de la categoria actual') }}">
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
                                            <a href="editFromConvenienceStores/${product['id']}" onClick="savedIdProductLocalStore(${product['id']})">
                                                <i class=" fa fa-edit btnEditar"></i>
                                            </a>
                                        @endcan
                                        @can('products.destroy')

                                                <a   onClick="cambiarDisponibilidad(${product['id']},${product['featured']},${idSection})">
                                                    <i class="fa fa-low-vision btn btn-link text-danger"></i>
                                                </a>
                                        @endcan
                                    </li>
                                `;
                            } else {
                                document.getElementById('sort_products_sort').innerHTML += `
                                
                                    <li class="list-group-item  btn btn-success item" id='${product['id']}_product'  data-id="${product['id']}">
                                    @can('products.destroy')    
                                            <a   onClick="removeProductsFromSection(${product['id']},${idSection})" title="{{ trans('Eliminar de la categoria actual') }}">
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
                                        <a href="editFromMarket/${product['id']}"  onClick="savedIdProductLocalStore(${product['id']})">
                                            <i class=" fa fa-edit btnEditar"></i>
                                        </a>
                                    @endcan
                                    @can('products.destroy')
                                            <a   onClick="cambiarDisponibilidad(${product['id']},${product['featured']},${idSection})">
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

                        if (idProduct != null) {
                            document.getElementById(idProduct+'_product').className += ' select';
                        }
                    } else {
                        document.getElementById('listaProductos').innerHTML = `
                            <div class="">
                                {!!  Form::label('', trans('------ Lista Vacía ------ '), ['class' => ' control-label  text-center textoBlanco']) !!}
                            </div>
                        `;
                    }

                }

            });
        }

        selectIdProduct = function(idProduct, ) {
            $('#mOptionGroupIdProduct').val(idProduct);
        };

        function cambiarDisponibilidad(idProducto, featuredProducto, idSubdepartment) {
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
                url: '{!!  url("product/changeVisibiliFromSubdepartment") !!}',
                method: 'get',
                data: {
                    idP: idProducto,
                    idSd: idSubdepartment,
                    featured: featuredProducto,
                },
                success: function(res) {
                    if (res['success'] == 'true') {

                        vaciarSelects2();

                        recargarProductos(idSubdepartment);

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

        function AgregarProductos(idSection) {
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
                    url: '{!!  url("sections/addProductFromConvenienceStores") !!}',
                    method: 'get',
                    data: {
                        idMarket: getIdMarket(),
                        idSect: idSection,
                        products: IDSP,
                    },
                    success: function(res) {
                        recargarProductos(idSection);
                    }
                })
            }
        }

        function removeProductsFromSection(idProducts, idSection) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $.ajax({
                url: '{!!  url("products/removeProductsFromSection") !!}',
                method: 'get',
                data: {
                    id: idProducts,
                    idSection: idSection,
                    idMarket:getIdMarket(),
                },
                success: function() {
                    vaciarListaOpciones();
                    vaciarListaGrupoOpciones();
                    recargarProductos(idSection);

                }

            });
        }

        function getCategoriAndOptionGroupByProduct(idProduct, countProduct, pName) {
            var target = $('.sort_products');
            var sortData = target.sortable('toArray', {
                attribute: 'data-id'
            })

            for (let index = 0; index < countProduct; index++) {
                document.getElementById(sortData[index]+'_product').className = 'list-group-item  btn btn-success item';
            }
            document.getElementById(idProduct+'_product').className += ' select';
            vaciarSelects2();
            savedIdProductLocalStoreFromSearchOptionGroup(idProduct)
            selectIdProduct(idProduct);
            vaciarListaOpciones();
            document.getElementById('listaOpciones').innerHTML = "";
            // getCategoriesByProduct(idProduct, pName);
            getOptionGroupByProduct(idProduct);

        }

        // Funciones para las categorias (Obsoleto)
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
                    
                    {!!  Form::label('categories', trans('Categoria(s) de: ${pName}'), ['class' => 'col-9 control-label CuartoDeCentrado text-left textoBlanco']) !!}

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
                    {!!  Form::label('categories', trans('Categorias'), ['class' => 'col-9 control-label CuartoDeCentrado text-left textoBlanco']) !!}
                    
                    {!!  Form::label('categories', trans('------ El producto ${pName} no esta asociado a ninguna categoria ------ '), ['class' => ' control-label CuartoDeCentrado text-left textoBlanco']) !!}
                </div>
                    `;
                    }

                }

            });
        }
      
        // Funciones para los Grupos de Opciones.
        function getOptionGroupByProduct(idProduct, reloadList,idGrupoRec) {
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
                success: function(res) {
                    document.getElementById('btnAgregarGrupoOpciones').innerHTML = `
                    <a   style="color: black" onClick="selectIdProduct(${idProduct})" class="CuartoDeCentrado"  title="{{ trans('Crear y añadir un nuevo Grupo de Opciones') }}" data-toggle="modal" data-target="#ModalCreatGrupoOpcion">
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
                        
                        <li  class="list-group-item  btn btn-success item" id="${grupoOpcion['id']}_grupoOpcion"  data-id="${grupoOpcion['id']}">
                            @can('optionGroups.destroy')    
                                <a onClick="removeOptionGroupFromProduct(${idProduct},${grupoOpcion['id']})" title="{{ trans('Remover esté grupo de opciones') }}">
                                        <i class="fa fa-minus-circle text-danger" ></i>
                                </a>
                            @endcan
                                <span class="textoNombre col-8" onClick="selectGrupoOpcionList('${grupoOpcion['id']}',${res.length},${grupoOpcion['id']},${grupoOpcion['id_producto']})">
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
                                
                                <li  class="list-group-item  btn btn-success item" id="${grupoOpcion['id']}_grupoOpcion"  data-id="${grupoOpcion['id']}">
                                    @can('products.destroy')    
                                        <a onClick="removeOptionGroupFromProduct(${idProduct},${grupoOpcion['id']})" title="{{ trans('Remover esté grupo de opciones') }}">
                                                <i class="fa fa-minus-circle text-danger" ></i>
                                        </a>
                                    @endcan
                                        <span class="textoNombre col-8 desactivado" onClick="selectGrupoOpcionList('${grupoOpcion['id']}',${res.length},${grupoOpcion['id']},${grupoOpcion['id_producto']})">
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
                                {!!  Form::label('', trans('------ Lista Vacía ------ '), ['class' => ' control-label  text-center textoBlanco']) !!}
                            
                            </div>
                    `;
                    }
                    if (idGrupoRec != null) {
                        document.getElementById(idGrupoRec + "_grupoOpcion").className += ' select';
                    }
                }

            });
        }

        selectIdProduct = function(idProduct, ) {
            $('#mOptionGroupIdProduct').val(idProduct);
        };

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
            $('input:checkbox[name=chkForceSelectUPDATE]').attr("checked", 'checked');
        };

        function selectGrupoOpcionList(idGrupoOpcion, countCategories, idGrupoOpcionOriginal, idProduct) {
            var target = $('.sort_products_grupo_opciones');
            var sortData = target.sortable('toArray', {
                attribute: 'data-id'
            })

            for (let index = 0; index < countCategories; index++) {
                document.getElementById(sortData[index]+'_grupoOpcion').className = 'list-group-item  btn btn-success item';
            }
            document.getElementById(idGrupoOpcion+'_grupoOpcion').className += ' select';
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
            if (IDGO != null) {
                vaciarSelects2();
                vaciarListaOpciones();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                $.ajax({
                    url: '{!!  url("optionGroups/addOptionGroupsFromMarket") !!}',
                    method: 'get',
                    data: {
                        idMarket: getIdMarket(),
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
                $('#mbtnCerrarGrupoOpcionesModal').click();
                vaciarListaOpciones();

                $.ajax({
                    url: '{!!  url("optionGroups/createFromMarket") !!}',
                    method: 'get',
                    data: {
                        name: nom,
                        active: activeVar,
                        id_producto: idP,
                        name_admin: nomAdmin,
                        multi: multiSe,
                        market_id: getIdMarket(),
                        cant_selectable: cantSelectable,
                        force_select: forceSelect,
                        optionGroupsList: [idP]
                    },
                    success: function(res) {
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
                vaciarListaOpciones();

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

        // Funciones para las opciones
        function getOptions(idGrupoOpcion, idProducto, reloadList, IdOpcionUpdate) {
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
                    market_id: getIdMarket(),
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
                        
                        <li  class="list-group-item" id="${opciones['id']}_opciones"  data-id="${opciones['id']}_opciones">
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
                                    
                                    <a    onClick="selectIdGrupoOpcionUpdate(${idGrupoOpcion},${opciones['product_id']},${opciones['id']},'${opciones['name']}',${opciones['price']},${opciones['active']})" data-toggle="modal" data-target="#ModalUpdateOpcion" >
                                        <i class=" fa fa-edit btnEditar"></i>
                                    </a>
                            @endcan
                            @can('products.destroy')

                                    <a  onClick="alternarActivacionOpcion(${idGrupoOpcion},${opciones['product_id']},${opciones['id']},'${opciones['name']}',${opciones['price']},${opciones['active']})">
                                        <i class="fa fa-low-vision btn btn-link text-danger"></i>
                                    </a>
                            @endcan
                        </li>
                        
                        </div>
                            `;
                        } else {
                                document.getElementById('listaOpciones_sort').innerHTML += `
                            <li  class="list-group-item" id="${opciones['id']}_opciones"  data-id="${opciones['id']}_opciones">
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
                                        
                                        <a    onClick="selectIdGrupoOpcionUpdate(${idGrupoOpcion},${opciones['product_id']},${opciones['id']},'${opciones['name']}',${opciones['price']},${opciones['active']})" data-toggle="modal" data-target="#ModalUpdateOpcion" >
                                            <i class=" fa fa-edit btnEditar"></i>
                                        </a>
                                @endcan
                                @can('products.destroy')

                                        <a  onClick="alternarActivacionOpcion(${idGrupoOpcion},${opciones['product_id']},${opciones['id']},'${opciones['name']}',${opciones['price']},${opciones['active']})">
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
                                <div class="text-center">
                                    {!!  Form::label('', trans('------ Lista Vacía ------ '), ['class' => ' control-label  text-center textoBlanco']) !!}
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


        function alternarActivacionOpcion(idGroup, idProduct, idOpcion, nombre, priceRec, active) {
            if (active == "1") {
                active = "0"
            } else {

                active = "1"
            }

            $.ajax({
                url: '{!!  url("options/updateFroMarkert") !!}',
                method: 'POST',
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
                    // $('#mbtnCerrarUpdateOpcionModal').click();
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

                $.ajax({
                    url: '{!!  url("options/updateFroMarkert") !!}',
                    method: 'POST',
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
                        market_id: getIdMarket(),
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
                        idMarket: getIdMarket(),
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
                    idMarket: getIdMarket(),
                },
                success: function(res) {
                    recargrarOpciones(idGrupo,idProducto);
                }

            });
        }

        // Promociones 
        function getPromos() {
            var priceProduct = 0;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            document.getElementById('listaPromociones').innerHTML = 'Buscando productos en Promoción . . . ';
            $.ajax({
                url: '{!!  url("markets/getPromos") !!}',
                method: 'get',
                data: {
                    idMarket: getIdMarket(),
                },
                success: function(res) {
                    vaciarSelects2();

                    if ((res).length > 0) {
                        document.getElementById('listaPromociones').innerHTML = `
                            <ul class="sort_promos list-group " id='sort_promos_sort'>
                                
                            </ul>
                        `;
                        res.forEach(promo => {
                            priceIdPromo = darFormatoPrecio(promo["price"]);
                            document.getElementById('sort_promos_sort').innerHTML += `
                                <li class="list-group-item  btn btn-success item" id='${promo['id']}_promo'  data-id="${promo['id']}">
                                    @can('products.destroy')    
                                        <a   onClick="removePromo(${promo['id']})" title="{{ trans('Remover el producto actual') }}">
                                            <i class="fa fa-minus-circle text-danger" ></i>
                                        </a>
                                    @endcan
                                    <div id="${promo['id']}_name" class="textoNombre col-8" >
                                        ${promo['name']}
                                        <div  class="textoNombre" >
                                            <b> ${priceIdPromo}</b>
                                        </div>
                                    </div>
                                    <span class="handle">
                                        <i class="fa fa-sort"></i>
                                    </span>
                                </li>
                            `;
                        });
                        function updateToDatabase(idString) {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });

                            $.ajax({
                                url: '{!!  url("market/sortOrderPromos") !!}',
                                method: 'POST',
                                data: {
                                    ids: idString
                                },
                                success: function() {
                                }

                            })
                        }
                        var target = $('.sort_promos');
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
                        document.getElementById('listaPromociones').innerHTML = `
                            <div class="text-center">
                                {!!  Form::label('', trans('------ Lista Vacía ------ '), ['class' => ' control-label  text-center textoBlanco']) !!}     
                            </div>
                        `;
                    }
                }

            });
        }

        function AgregarPromos() {
            const IdP = $('#IdPromos').val();
            if (IdP != null) {
                vaciarSelects2();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                $.ajax({
                    url: '{!!  url("markets/addPromos") !!}',
                    method: 'get',
                    data: {
                        idMarket: getIdMarket(),
                        products: IdP,
                    },
                    success: function(res) {
                        getPromos();
                    }
                })
            }
        }

        function removePromo(IdPromo){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $.ajax({
                url: '{!!  url("markets/removePromo") !!}',
                method: 'get',
                data: {
                    idMarket:getIdMarket(),
                    id: IdPromo,
                },
                success: function() {
                    getPromos();
                }

            });
        }

        // Funciones para Busqueda
        function searchProductsPromo() {

        $('#IdPromos').select2({
            placeholder: 'Buscar productos',
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
                url: '{!!  url("search/promosFormMarket") !!}',
                dataType: 'json',
                data: function(params) {
                    return {
                        idMarket: getIdMarket(),
                        promo: params.term
                    };
                },
            },

            });
        }
        
        function searchSection() {
            $('#idSectionsToAdd').select2({
                placeholder: 'Buscar Secciones',
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
                    url: '{!!  url("search/sectionesFromMarket") !!}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            idMarket: getIdMarket(),
                            section: params.term
                        };
                    },
                },
                success: function(res) {
                }
            });
        }

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
                            idMarket: getIdMarket(),
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
                            idMarket: getIdMarket(),
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
                            market_id: getIdMarket(),
                        };
                    },
                },
            });
        }

        // Funciones de recarga
        function reloadSubdepartment(idDepartment,idSubdepartment){
            var reloadList = true;
            getSubdepartment(idDepartment,reloadList,idSubdepartment);
        }
        function reloadDepartment(idDepartment){
            var reloadList = true;
            getDepartments(idDepartment,reloadList);
        }

        function recargrarOpciones(idGrupo, idProduct, IdOpcionUpdate) {
            var reloadList = true;
            getOptions(idGrupo, idProduct, reloadList, IdOpcionUpdate);
        }
        function recargrarGrupoOpciones(idProducto, idGrupoRec) {
            var reloadList = true;
            getOptionGroupByProduct(idProducto, reloadList,idGrupoRec);
        }
        function recargarProductos(idSection,idProduct) {
            var reloadList = true;
            getProductBySection(idSection,reloadList,idProduct);
        }
        // Funciones mixtas
        function vaciarSelects2() {
            $('#IDOpcionesParaAgregar').val('').change();
            $('#IDProductosParaAgregar').val('').change();
            $('#IDGrupoOpcionesParaAgregar').val('').change();
            // $('#IdPromos').val('').change();
            $('#idSectionsToAdd').val('').change();
            // 
            

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
            if (document.getElementById('btnAgregarProductos') != null) {
                document.getElementById('btnAgregarProductos').innerHTML = ``;
            }
            if (document.getElementById('listaGrupoOpciones') != null) {
                document.getElementById('listaGrupoOpciones').innerHTML = `
                <ul class="sort_products list-group" id="listaGrupoOpciones_sort">

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
    <script src="{{asset('plugins/colorpicker/bootstrap-colorpicker.min.js')}}"></script>

    <script type="text/javascript">
        // $("input[name$='color']").colorpicker({
        $(".colorpicker-component, input[name$='color']").colorpicker({
            customClass: 'colorpicker',
            format: 'hex',
            // sliders: {
            //     saturation: {
            //         maxLeft: 200,
            //         maxTop: 200
            //     },
            //     hue: {
            //         maxTop: 200
            //     },
            //     alpha: {
            //         maxTop: 200
            //     }
            // }
        });
        Dropzone.autoDiscover = false;
        var dropzoneFields = [];
    </script>
@endprepend
