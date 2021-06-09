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

    a:hover {
        cursor: pointer;
    }

</style>

<div class="loading show">
    <div class="spin"></div>
</div>



<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="marco_decorativo  col-6 py-2">

            {!! Form::label('Fields', trans('Categoria (s) de establecimiento'), ['class' => 'col-12 control-label text-center']) !!}

            <ul class="sort_field list-group">
                @foreach ($fields as $field)
                    <li class="list-group-item  btn btn-success item "
                        onclick="selectfield({{ $field->id }},{{ count($fields) }})" id='{{ $field->id }}'
                        data-id="{{ $field->id }}">

                        @if ($field->active == '0')
                            <span id="{{ $field->id }}_field_name"
                                class="desactivado alinearIzquierda textoNombre col-8 ">
                                {{ $field->name }}
                            </span>
                        @else
                            <span id="{{ $field->id }}_field_name" class="alinearIzquierda textoNombre col-8 ">
                                {{ $field->name }}
                            </span>
                        @endif
                        <span class="handle">
                            <i class="fa fa-sort"></i>
                        </span>

                        @can('categories.destroy')
                            <a
                                onClick="alternarActivacionCategoria({{ $field->id }},'{{ $field->name }}','{{ $field->description }}',{{ $field->active }})">
                                <i class="fa fa-low-vision btn btn-link text-danger"></i>
                            </a>
                        @endcan
                    </li>

                @endforeach
            </ul>
        </div>

    </div>

</div>

<!-- Back Field -->
<div class="form-group col-12 text-right">

    <a href="{!! route('fields.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i>
        {{ trans('Volver') }}</a>
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
            function updateToDatabase(idString) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                $.ajax({
                    url: '{!! url("fields/sort") !!}',
                    method: 'get',
                    data: {
                        ids: idString
                    },
                    success: function() {}

                })
            }



            var target = $('.sort_field');
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
        function alternarActivacionCategoria(idFields, nameF, descriptionFields, activeC) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            var nameFields = nameF;
            var descriptionFie = descriptionFields;
            var idFi = idFields;

            if (activeC == '1') {
                activeC = '0';
            } else {
                activeC = '1';
            }



                $.ajax({
                    url: '{!!  url("fields/updateFields") !!}',
                    method: 'get',
                    data: {
                        name: nameFields,
                        activeFiel: activeC,
                        description: descriptionFie,
                        idF: idFi,
                    },
                    success: function(res) {

                        location.reload();
                    }

                });


        }
    </script>
@endprepend

{{-- <div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
<!-- Name Field -->
<div class="form-group row ">
  {!! Form::label('name', trans("lang.field_name"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {!! Form::text('name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.field_name_placeholder")]) !!}
    <div class="form-text text-muted">
      {{ trans("lang.field_name_help") }}
    </div>
  </div>
</div>

<!-- index_relevance -->
<div class="form-group row ">
  {!! Form::label('index_relevance', trans("Indice de relevancia"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {!! Form::number('index_relevance', null,  ['class' => 'form-control',"min = 0",'step'=>'any','placeholder'=>  trans("lang.distance_per_extra_placeholder")]) !!}
    <div class="form-text text-muted">
      {{ trans("El numero de importancia que tendra la categoria en la pantalla principal. 0 = El más importante, 1 = El segundo más importante") }}
    </div>
  </div>
</div>

<!-- Description Field -->
<div class="form-group row ">
  {!! Form::label('description', trans("lang.field_description"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {!! Form::textarea('description', null, ['class' => 'form-control','placeholder'=>
     trans("lang.field_description_placeholder")  ]) !!}
    <div class="form-text text-muted">{{ trans("lang.field_description_help") }}</div>
  </div>
</div>

<!-- Message Field -->
<div class="form-group row ">
  {!! Form::label('message', trans("Mensaje de saludo"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {!! Form::text('message', null, ['class' => 'form-control','placeholder'=>
     trans("¿Qué deseas comer hoy?")  ]) !!}
    <div class="form-text text-muted">{{ trans("Este mensaje se mostrara en la parte superior cuando se entra la categoria") }}</div>
  </div>
</div>

<!-- 'Boolean active Field' -->
<div class="form-group row ">
  {!! Form::label('active', trans("Activa"),['class' => 'col-3 control-label text-right']) !!}
  <div class="checkbox icheck">
      <label class=" ml-2 form-check-inline">
          {!! Form::hidden('active', 0) !!}
          {!! Form::checkbox('active', 1, null) !!}
          
          {!! Form::label('active_hide', trans("Habilita/deshabilitar la categoria"),['class' => ' text-left pl-3']) !!}
        </label>
  </div>
</div>
<!-- Message Closes Field -->
<div class="form-group row ">
  {!! Form::label('message_closed', trans("Aviso"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {!! Form::text('message_closed', null, ['class' => 'form-control','placeholder'=>
     trans("Actualmente no esta disponible esta categoria")  ]) !!}
    <div class="form-text text-muted">{{ trans("Este mensaje se mostrara cuando la categoria este cerrada") }}</div>
  </div>
</div>

</div>
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

<!-- Image Field -->
<div class="form-group row">
  {!! Form::label('image', trans("lang.field_image"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <div style="width: 100%" class="dropzone image" id="image" data-field="image">
      <input type="hidden" name="image">
    </div>
    <a href="#loadMediaModal" data-dropzone="image" data-toggle="modal" data-target="#mediaModal" class="btn btn-outline-{{setting('theme_color','primary')}} btn-sm float-right mt-1">{{ trans('lang.media_select')}}</a>
    <div class="form-text text-muted w-50">
      {{ trans("lang.field_image_help") }}
    </div>
  </div>
</div>
@prepend('scripts')
<script type="text/javascript">
    var var15866134631720934041ble = '';
    @if (isset($field) && $field->hasMedia('image'))
    var15866134631720934041ble = {
        name: "{!! $field->getFirstMedia('image')->name !!}",
        size: "{!! $field->getFirstMedia('image')->size !!}",
        type: "{!! $field->getFirstMedia('image')->mime_type !!}",
        collection_name: "{!! $field->getFirstMedia('image')->collection_name !!}"};
    @endif
    var dz_var15866134631720934041ble = $(".dropzone.image").dropzone({
        url: "{!!url('uploads/store')!!}",
        addRemoveLinks: true,
        maxFiles: 1,
        init: function () {
        @if (isset($field) && $field->hasMedia('image'))
            dzInit(this,var15866134631720934041ble,'{!! url($field->getFirstMediaUrl('image','thumb')) !!}')
        @endif
        },
        accept: function(file, done) {
            dzAccept(file,done,this.element,"{!!config('medialibrary.icons_folder')!!}");
        },
        sending: function (file, xhr, formData) {
            dzSending(this,file,formData,'{!! csrf_token() !!}');
        },
        maxfilesexceeded: function (file) {
            dz_var15866134631720934041ble[0].mockFile = '';
            dzMaxfile(this,file);
        },
        complete: function (file) {
            dzComplete(this, file, var15866134631720934041ble, dz_var15866134631720934041ble[0].mockFile);
            dz_var15866134631720934041ble[0].mockFile = file;
        },
        removedfile: function (file) {
            dzRemoveFile(
                file, var15866134631720934041ble, '{!! url("fields/remove-media") !!}',
                'image', '{!! isset($field) ? $field->id : 0 !!}', '{!! url("uplaods/clear") !!}', '{!! csrf_token() !!}'
            );
        }
    });
    dz_var15866134631720934041ble[0].mockFile = var15866134631720934041ble;
    dropzoneFields['image'] = dz_var15866134631720934041ble;
</script>
@endprepend

<!-- Markets Field -->
<div class="form-group row ">
  {!! Form::label('markets[]', trans("lang.field_markets"),['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {!! Form::select('markets[]', $market, $marketsSelected, ['class' => 'select2 form-control' , 'multiple'=>'multiple']) !!}
    <div class="form-text text-muted">{{ trans("lang.field_markets_help") }}</div>
  </div>
</div>
</div>
@if ($customFields)
<div class="clearfix"></div>
<div class="col-12 custom-field-container">
  <h5 class="col-12 pb-4">{!! trans('lang.custom_field_plural') !!}</h5>
  {!! $customFields !!}
</div>
@endif
<!-- Submit Field -->
<div class="form-group col-12 text-right">
  <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.field')}}</button>
  <a href="{!! route('fields.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div> --}}
