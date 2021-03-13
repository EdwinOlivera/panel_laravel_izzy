{{-- <div id="productEditModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-stretch">
                <h5 class="modal-title flex-grow-1">{!! trans('Editar porducto') !!}</h5>
               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
           
            <div class="modal-footer">
                <span>{!! trans('Terminar edición') !!}</span>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{!! trans('Listo') !!}</button>
            </div>
        </div>
    </div>
</div> --}}


<!-- Modal Cierre -->
<div class="modal fade" id="productEditModal_old" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
  
        <div class="modal-header bg-blue">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Editar Producto</h4>
        </div>
  
        <div class="modal-body">
            <form class="form-horizontal">
                <!-- parametros ocultos -->
                <input type="hidden" id="mhdnIdProduct">
                
              <div class="box-body">
                  <div class="form-group">
                      <label class="col-sm-3 control-label">Nombre</label>
                      <div class="col-sm-9"> 
                        <input type="text" name="mtxtNombre" class="form-control" id="mtxtNombre" placeholder="">
                      </div>
                  </div>
  
                  <div class="form-group">
                      <label class="col-sm-3 control-label">Precio</label>
                      <div class="col-sm-9"> 
                        <input type="text" name="mtxtPrice" class="form-control" id="mtxtPrice" value="" >
                      </div>
                  </div>
  
                  <div class="form-group">
                      <label class="col-sm-3 control-label">Disponible</label>
                      <div class="col-sm-9"> 
                        {{-- <input type="text" name="mtxtDisponible" class="form-control" id="mtxtDisponible"> --}}
                        <div class="checkbox icheck">
                            <label class="col-9 ml-2 form-check-inline">
                                {!! Form::hidden('mtxtDisponible', 0) !!}
                                {!! Form::checkbox('mtxtDisponible', 1, null) !!}
                            </label>
                        </div>
                      </div>
                  </div>  
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
  <script>
  $('#mbtnUpdProduct').click(function(){
    $.ajaxSetup({ headers: {'X-CSRF-TOKEN': '{{csrf_token()}}'}});

    var idP = $('#mhdnIdProduct').val();
    var nom = $('#mtxtNombre').val();
    var pri = $('#mtxtPrice').val();
    var dis = $('#mtxtDisponible').val();


    $.ajax({
        url: '{!! url("product/updateModal") !!}',
        method: 'POST',
        data: {
            idP: idP,
            nom: nom,
            pri: pri,
            dis: dis,
        },
        success: function (res) {
            
            alert('Se grabo');
			$('#mbtnCerrarModal').click();
			location.reload();
            
        }
    })

    });
  </script>
      