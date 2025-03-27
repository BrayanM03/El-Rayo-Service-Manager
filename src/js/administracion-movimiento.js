//Objeto de configuracion de toast
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut" 
  }


let id_movimiento = getQueryParams('id')
let tipo_remision = getQueryParams('tipo_remision')
let modo_edicion = false;
disabled_botton = true;


traerDatosMovimiento(id_movimiento, tipo_remision)

function traerDatosMovimiento(id, tipo_remision){
    $("#input-comprobante-edicion").attr('eliminar', false);
  $.ajax({
    type: "post",
    url: "./modelo/cuentas_pagar/traer-movimiento.php",
    data: {'id_movimiento':id_movimiento},
    dataType: "JSON",
    success: function (response) {
        if(response.estatus){
           extension_archivo = response.datos_movimiento[0].extension_archivo;
           console.log(extension_archivo);
            cargarComprobante(response.datos_movimiento[0].extension_archivo);

            $("#proveedor").empty();
                response.proveedores.forEach(element => {
                $("#proveedor").append(`
                    <option value="${element.id}">${element.nombre}</option>
                `);
            });
            $("#proveedor").val(response.datos_movimiento[0].proveedor_id);

            $("#usuario").empty();
            response.usuarios.forEach(element => {
            $("#usuario").append(`
                <option value="${element.id}">${element.nombre} ${element.apellidos}</option>
                `);
            });
            $("#usuario").val(response.datos_movimiento[0].id_usuario);


            $("#factura").val(response.datos_movimiento[0].folio_factura);
            $("#estado-factura").val(response.datos_movimiento[0].estado_factura);
            $("#estatus").val(response.datos_movimiento[0].estatus);
            $("#importe").val(response.datos_movimiento[0].total);
            $("#pagado-total").val(response.datos_movimiento[0].pagado);
            $("#restante-total").val(response.datos_movimiento[0].restante);
            $("#mercancia").val(response.datos_movimiento[0].mercancia);
            $("#descripcion-remision").val(response.datos_movimiento[0].descripcion);

             sucursales_arreglo = response.sucursales;
             sucursales_arreglo.forEach(element => {
                
                $("#sucursales").append(`
                    <option value="${element.id}">${element.nombre}</option
                `)
            });

            $("#sucursales").val(response.datos_movimiento[0].sucursal);



            if(response.llantas_movimiento.length>0){
                let contador =0;
                response.llantas_movimiento.forEach(element => {
                    contador++;
                    let ubicacionObj = element.id_ubicacion === 0 ? { id: 0, nombre: 'Bodega' } : response.sucursales.find(sucursal => sucursal.id === element.id_ubicacion);
                     let destinoObj = response.sucursales.find(sucursal => sucursal.id === element.id_destino);
                     let id_usuario = $("#emp-title").attr('sesion_id')
                     if(id_usuario == 22 || id_usuario ==15){
                      $("#tbody-llantas-remision").append(`
                      <tr>
                          <td>${contador}</td>
                          <td>${element.cantidad}</td>
                          <td>${element.descripcion}</td>
                          <td>${element.marca}</td>
                          <td>${element.id_ubicacion ==0 ? 'Bodega': ubicacionObj.nombre}</td>
                          <td>${destinoObj.nombre}</td>
                          <td>${element.stock_destino_anterior}</td>
                          <td>${element.stock_destino_actual}</td>
                          <td>${element.costo}</td>
                          <td>${element.importe}</td>
                          <td>
                          <div class="btn btn-success" onclick="editarLlantaRemision(${element.id})"><i class="fas fa-pen"></i></div>
                         
                          </td>
                      </tr>
                  `)}else{
                    $("#tbody-llantas-remision").append(`
                    <tr>
                        <td>${contador}</td>
                        <td>${element.cantidad}</td>
                        <td>${element.descripcion}</td>
                        <td>${element.marca}</td>
                        <td>${element.id_ubicacion ==0 ? 'Bodega': ubicacionObj.nombre}</td>
                        <td>${destinoObj.nombre}</td>
                        <td>${element.stock_destino_anterior}</td>
                        <td>${element.stock_destino_actual}</td>
                        <td>${element.costo}</td>
                        <td>${element.importe}</td>
                        <td>
                        <div class="btn btn-success" onclick="editarLlantaRemision(${element.id})"><i class="fas fa-pen"></i></div>
                        <div class="btn btn-danger" onclick="borrarrLlantaRemision(${element.id})"><i class="fas fa-trash"></i></div>
                        </td>
                    </tr>
                `)
                  }
                     
                });
            }

        }
        $("#contenedor-loader").addClass('d-none')
    }
  });
}

function actualizarDatosGenerales(updating_data_status){
    if(updating_data_status === 0){
        var contenedor = document.getElementById('contenedor-datos-generales');
        var inputs_contenedor = contenedor.querySelectorAll('input');
        var selects_contenedor = contenedor.querySelectorAll('select');
        let contenedor_btnes_edicion_gnl = $("#contenedor-botones-edicion");
        
        if(tipo_remision ==2 && !modo_edicion){
            inputs_contenedor.forEach(function(input) {
                input.removeAttribute('disabled');
            });
            selects_contenedor.forEach(function(input) {
                input.removeAttribute('disabled');
            });
    
            contenedor_btnes_edicion_gnl.empty().append(`
            <div class="row">
                <div class="col-4">
                    <div class="btn btn-success w-100" onclick="actualizarDatosGenerales(1)">Actualizar</div>
                </div>
                <div class="col-4">
                    <div class="btn btn-info w-100" onclick="modalAbonosRemisionIngreso()">Abonos</div>
                </div>
                <div class="col-4">
                    <div class="btn btn-danger w-100" onclick="actualizarDatosGenerales(0)">Cancelar</div>
                </div>
            </div>
            `);
            $("#contenedor-cargar-documento").removeClass().addClass('contenedor-cargar-documento-edicion').attr('onclick','cargarComprobanteEdicion()');
            
            let documento_cargado= $("#area-canvas").attr('documento')
            if(documento_cargado=='true'){
                $("#area-canvas").append(`<span aria-hidden="true" class="btn-x-documento" onclick="deleteThumb()">×</span>`)
            }
            
        }else if(modo_edicion){
            /* $("#area-canvas span").last().remove()
            let archivo_adjuntado = $("#area-canvas").find('canvas');
            console.log(archivo_adjuntado.length);
           if(archivo_adjuntado.length !=1){
                traerDatosMovimiento(id_movimiento, tipo_remision)
             }else{
                traerDatosMovimiento(id_movimiento, tipo_remision)
           } */
           traerDatosMovimiento(id_movimiento, tipo_remision)
            inputs_contenedor.forEach(function(input) { 
                input.setAttribute('disabled', true);
            });
            selects_contenedor.forEach(function(input) {
                input.setAttribute('disabled', true);
            });
            contenedor_btnes_edicion_gnl.empty().append(`
            <div class="row justify-content-end">
                <div class="col-12 col-md-4">
                    <div class="btn btn-primary w-100" onclick="actualizarDatosGenerales(0)">Editar</div>
                </div>
            </div>
                `);
            $("#contenedor-cargar-documento").removeClass().addClass('contenedor-cargar-documento').attr('onclick','');
            

        }
        if(!modo_edicion){
            modo_edicion = true
        }else{
            modo_edicion = false
        }
    }else{
        $("#contenedor-loader").removeClass('d-none')
        let proveedor_actualizado = $('#proveedor').val()
        let factura_actualizado = $('#factura').val()
        let usuario_actualizado = $('#usuario').val()
        let estado_actualizado = $('#estado-factura').val()
        let estatus_actualizado = $('#estatus').val()
        let importe_total_actualizado = $('#importe').val();
        let id_sucursal = $('#sucursales').val();
        let aprobacion_importe = 'unknown';
        let aprobacion_actualizar_stock ='unknown';

        let documento_adjunto = document.getElementById('input-comprobante-edicion');
        var file =  documento_adjunto.files[0];
        if(file != undefined){
            const extension = file.name.split('.').pop();
            extension_archivo=extension;
        };
        let eliminar_documento = $("#input-comprobante-edicion").attr('eliminar')
        console.log(extension_archivo);
        var formData = new FormData();
        /* formData.append('id_usuario', id_user); */
        formData.append('proveedor_actualizado', proveedor_actualizado);
        formData.append('factura_actualizado',factura_actualizado);
        formData.append('usuario_actualizado',usuario_actualizado);
        formData.append('estado_actualizado',estado_actualizado);
        formData.append('estatus_actualizado',estatus_actualizado);
        formData.append('importe_total_actualizado',importe_total_actualizado);
        formData.append('aprobacion_importe', aprobacion_importe);
        formData.append('id_sucursal', id_sucursal);
        formData.append('aprobacion_actualizar_stock', aprobacion_actualizar_stock);
        formData.append('documento_adjunto', file);
        formData.append('id_movimiento', id_movimiento);
        formData.append('eliminar_documento', eliminar_documento);
        formData.append('extension_archivo', extension_archivo);

              
        $.ajax({
            type: "post",
            url: "./modelo/cuentas_pagar/actualizar-remision.php",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (response) {
                $("#contenedor-loader").addClass('d-none')

                if(response.estatus){
                    Swal.fire({
                        icon: response.icon,
                        html: response.mensaje,
                        confirmButtonText: 'Entendido'
                    }).then(()=>{
                        actualizarTabla()
                    })
                }else{
                    if(response.icon == 'warning'){
                        myConfirmButtonText = 'Si',
                        myShowCancelButton = true,
                        myCancelButtonText = 'No'
                    }else{
                        myConfirmButtonText = 'Entendido',
                        myShowCancelButton = true,
                        myCancelButtonText = 'No'
                    }
                    
                    Swal.fire({
                        icon: response.icon,
                        html: response.mensaje,
                        confirmButtonText: myConfirmButtonText,
                        showCancelButton: myShowCancelButton,
                        cancelButtonText: myCancelButtonText
                    }).then(function(r){
                        if(r.isConfirmed && response.icon == 'warning'){
                            if(response.necesita_aprobacion_stock){
                                 aprobacion_actualizar_stock = true;

                            }
                            if(response.necesita_aprobacion_importes){
                                aprobacion_importe = true;
                            }

                            formData.append('aprobacion_actualizar_stock', aprobacion_actualizar_stock);
                            formData.append('aprobacion_importe', aprobacion_importe);

                            $("#contenedor-loader").removeClass('d-none')
                            $.ajax({
                                type: "post",
                                contentType: false,
                                processData: false,
                                url: "./modelo/cuentas_pagar/actualizar-remision.php",
                                data: formData,
                                dataType: "JSON",
                                success: function (response2){
                                    $("#contenedor-loader").addClass('d-none')
                                    if(response2.estatus){
                                        Swal.fire({
                                            icon: response2.icon,
                                            html: response2.mensaje,
                                            confirmButtonText: 'Entendido'
                                        }).then(()=>{
                                            actualizarTabla();
                                        })
                                    }else{
                                        Swal.fire({
                                            icon: response2.icon,
                                            html: response2.mensaje,
                                            confirmButtonText: myConfirmButtonText,
                                            showCancelButton: myShowCancelButton,
                                            cancelButtonText: myCancelButtonText
                                        }).then(function(rr){
                                            if(response2.necesita_aprobacion_stock){
                                                aprobacion_actualizar_stock = true;
                                           }
                                           if(response2.necesita_aprobacion_importes){
                                               aprobacion_importe = true;
                                           }

                                           formData.append('aprobacion_actualizar_stock', aprobacion_actualizar_stock);
                                           formData.append('aprobacion_importe', aprobacion_importe);
                                           if(rr.isConfirmed || rr.isDenied){
                                            $("#contenedor-loader").removeClass('d-none')
                                            $.ajax({
                                                type: "post",
                                                url: "./modelo/cuentas_pagar/actualizar-remision.php",
                                                contentType: false,
                                                processData: false,
                                                data: formData,
                                                dataType: "JSON",
                                                success: function (response3){
                                                    $("#contenedor-loader").addClass('d-none')
                                                    Swal.fire({
                                                        icon: response3.icon,
                                                        html: response3.mensaje,
                                                        confirmButtonText: 'Entendido'
                                                    }).then(()=>{
                                                        actualizarTabla()
                                                    })
                                                }
                                                })
                                           }
                                        });
                                    }
                                   
                                    
                                }
                            })
                        }else if(r.isDenied && response.icon == 'warning'){
                            $("#contenedor-loader").removeClass('d-none')

                            if(response.necesita_aprobacion_stock){
                                aprobacion_actualizar_stock = false;

                           }
                           if(response.necesita_aprobacion_importes){
                               aprobacion_importe = false;
                           }

                           formData.append('aprobacion_actualizar_stock', aprobacion_actualizar_stock);
                           formData.append('aprobacion_importe', aprobacion_importe);
                           $.ajax({
                            type: "post",
                            url: "./modelo/cuentas_pagar/actualizar-remision.php",
                            data: formData,
                            contentType: false,
                            processData: false,
                            dataType: "JSON",
                            success: function (response2){
                                $("#contenedor-loader").addClass('d-none')

                                if(response2.estatus){
                                    Swal.fire({
                                        icon: response2.icon,
                                        html: response2.mensaje,
                                        confirmButtonText: 'Entendido'
                                    }).then(()=>{
                                        actualizarTabla();
                                    })
                                }else{
                                    Swal.fire({
                                        icon: response2.icon,
                                        html: response2.mensaje,
                                        confirmButtonText: myConfirmButtonText,
                                        showCancelButton: myShowCancelButton,
                                        cancelButtonText: myCancelButtonText
                                    }).then(function(rr){
                                        $("#contenedor-loader").removeClass('d-none')

                                        if(response2.necesita_aprobacion_stock){
                                            aprobacion_actualizar_stock = true;
                                       }
                                       if(response2.necesita_aprobacion_importes){
                                           aprobacion_importe = true;
                                       }
                                       formData.append('aprobacion_actualizar_stock', aprobacion_actualizar_stock);
                                       formData.append('aprobacion_importe', aprobacion_importe);
                                       if(rr.isConfirmed || rr.isDenied){
                                        $.ajax({
                                            type: "post",
                                            url: "./modelo/cuentas_pagar/actualizar-remision.php",
                                            data:formData,
                                            contentType: false,
                                            processData: false,
                                            dataType: "JSON",
                                            success: function (response3){
                                                $("#contenedor-loader").addClass('d-none')

                                                Swal.fire({
                                                    icon: response3.icon,
                                                    html: response3.mensaje,
                                                    confirmButtonText: 'Entendido'
                                                }).then(()=>{
                                                    actualizarTabla()
                                                })
                                            }
                                            })
                                       }
                                    });
                                }
                               
                                
                            }
                        })
                        }else{
                            actualizarTabla();
                        }
                        
                    })
                }
               
            }
        });
    }
   
}

//----FUNCIONES PARA MODAL AGREGAR ABONO----

function modalAbonosRemisionIngreso(){
    $.ajax({
        type: "post",
        url: "./modelo/cuentas_pagar/traer-abonos-remision.php",
        data: {id_movimiento},
        dataType: "JSON",
        success: function (response) {
            if(response.estatus){
                Swal.fire({
                    width: '650px',
                    html:`
                        <div class="container" style="">
                            <div class="col-12">
                                <h4 class="text-center">Abonos registrados</h4>
                            </div>
                            <div class="row m-4">
                                <div class="col-4">
                                    <label for="importe-abonos">Total</label>
                                    <input type="number" id="importe-abonos" placeholder="0.00" class="form-control" disabled>
                                </div>
                                <div class="col-4">
                                    <label for="abonado-abonos">Abonado</label>
                                    <input type="number" id="abonado-abonos" placeholder="0.00" class="form-control" disabled>
                                </div>
                                <div class="col-4">
                                    <label for="restante-abonos">Restante</label>
                                    <input type="number" id="restante-abonos" placeholder="0.00" class="form-control" disabled>
                                </div>
                            </div>
                            <hr>
                            <div class="row m-2">
                            <b style="color:gray; font-size:17px">Abonar a remisión</b>
                            </div>
                            <div class="row m-2">
                                <div class="col-5">
                                    <label for="monto-abono-abonos">Abono</label>
                                    <input type="number" id="monto-abono-abonos" placeholder="0.00" class="form-control">
                                </div>
                                <div class="col-5">
                                    <label for="forma-pago-abonos">Forma pago</label>
                                    <select id="forma-pago-abonos" class="form-control">
                                        <option value="Efectivo">Efectivo</option>
                                        <option value="Transferencia" selected>Transferencia</option>
                                        <option value="Deposito">Deposito</option>
                                        <option value="Tarjeta">Tarjeta</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="Deposito">Deposito</option>
                                        <option value="Sin definir">Sin definir</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <div class="btn btn-success" style="margin-top:24px !important;" onclick="abonarCuentaPorPagar(${id_movimiento})">Abonar</div>
                                </div>
                            </div>
                            <div class="row m-4" id="area-folio-pago">
                                <div class="col-12">
                                    <label>Folio transferencia</label>
                                    <input type="text" class="form-control" id="folio-pago" placeholder="Folio">
                                </div>
                            </div>
                            <div class="row m-4" id="area-folio-pago">
                                <div class="col-6">
                                    <label>Fecha y hora</label>
                                    <input type="datetime-local" class="form-control" id="hora-fecha-pago">
                                </div>
                            </div>    
                            <div class="mt-3">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Monto</th>
                                            <th>Fecha</th>
                                            <th>Forma pago</th>
                                            <th>Folio</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead> 
                                    <tbody id="tbody-abonos-remision" style="background-color:whitesmoke !important;">
                                        <tr>
                                            <td colspan="6">Sin abonos registrados</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `,
                    didOpen: function(){
                        if(response.estatus){

                            $("#forma-pago-abonos").on('change',function(){
                                if($(this).val()== 'Transferencia' || $(this).val()== 'Deposito' || $(this).val()== 'Tarjeta' || $(this).val()== 'Cheque'){
                                    if($(this).val()== 'Transferencia'){
                                        var nombre_forma_pago = 'transferencia';
                                    }else if($(this).val()== 'Deposito'){
                                        var nombre_forma_pago = 'deposito';
                                    }else if($(this).val()== 'Tarjeta'){
                                        var nombre_forma_pago = 'tarjeta';
                                    }else if($(this).val()== 'Cheque'){
                                        var nombre_forma_pago = 'cheque';
                                    }else{
                                        var nombre_forma_pago = 'NA';
                                    }
                                    $('#area-folio-pago').empty().append(`
                                        <div class="col-12">
                                            <label>Folio ${nombre_forma_pago}</label>
                                            <input type="text" class="form-control" id="folio-pago" placeholder="Folio">
                                        </div>
                                    `)
                                }else{
                                    $('#area-folio-pago').empty();
                                }
                            })

                            $("#importe-abonos").val(response.importe_total)
                            $("#abonado-abonos").val(response.pagado)
                            $("#restante-abonos").val(response.restante)
                            if(response.data.length > 0){
                                $('#tbody-abonos-remision').empty();
                                response.data.forEach(element => {
                                    $('#tbody-abonos-remision').append(`
                                        <tr>
                                            <td>${element.id}</td>
                                            <td>${element.monto}</td>
                                            <td>${element.fecha} ${element.hora}</td>
                                            <td>${element.forma_pago}</td>
                                            <td>${element.folio_forma_pago}</td>
                                            <td><button type="button" onclick="borrarAbono(${element.id});" class="btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></td>
                                        </tr>
                                    `)
                                });
                            }else{
                                $('#tbody-abonos-remision').empty();
                                $('#tbody-abonos-remision').append(`
                                <tr>
                                    <td colspan="6" class="text-center">Sin abonos registrados</td>
                                </tr>
                            `)
                            }
                        }
                    }
                })
            }
        }
    });
}

function getQueryParams(qs) {
    // Crea un objeto URLSearchParams con los parámetros de la URL
    var parametrosURL = new URLSearchParams(window.location.search);
    // Obtiene el valor de un parámetro específico, por ejemplo, "id"
    var valorParametro = parametrosURL.get(qs);
    return valorParametro;
}

function abonarCuentaPorPagar(id_movimiento){
    let abono = $("#monto-abono-abonos").val();
    let forma_pago = $("#forma-pago-abonos").val();
    let importe_total = $("#importe-abonos").val();
    let hora_fecha_pago = $("#hora-fecha-pago").val();
    
    if($('#forma-pago-abonos').val()== 'Transferencia' || $('#forma-pago-abonos').val()== 'Deposito' || $('#forma-pago-abonos').val()== 'Tarjeta' || $('#forma-pago-abonos').val()== 'Cheque'){
        var folio_forma_pago = $("#folio-pago").val();
        if(folio_forma_pago == null || folio_forma_pago.length == 0 || folio_forma_pago==''){
            var folio_pago_no_val = false;
        }else{
            var folio_pago_no_val = true;
        }
    }else{
        var folio_forma_pago = false;
        var folio_pago_no_val = true;
    }

    if(abono == 0 || abono == null || abono == undefined || abono ==''){
        toastr.error('El monto no puede ir en 0 o vacio', 'Error')
    }else{
    if(folio_pago_no_val == false){
        toastr.error('Ingrese un folio de pago', 'Error')
    }else{
        $.ajax({
            type: "post",
            url: "./modelo/cuentas_pagar/abonar-cuenta-pagar.php",
            data: {id_movimiento, abono, forma_pago, importe_total, folio_forma_pago, hora_fecha_pago},
            dataType: "JSON",
            success: function (response) {
                if(response.estatus){
                        $.ajax({
                            type: "post",
                            url: "./modelo/cuentas_pagar/traer-abonos-remision.php",
                            data: {id_movimiento},
                            dataType: "JSON",
                            success: function (respo) {
                                if(respo.estatus){
                                    $("#importe-abonos").val(respo.importe_total)
                                    $("#abonado-abonos").val(respo.pagado)
                                    $("#restante-abonos").val(respo.restante)
                                    actualizarTabla()
                                    if(respo.data.length > 0){
                                        toastr.success('Abono realizado correctamente', 'Abando')
                                        $('#tbody-abonos-remision').empty();
                                        respo.data.forEach(element => {
                                            $('#tbody-abonos-remision').append(`
                                                <tr>
                                                    <td>${element.id}</td>
                                                    <td>${element.monto}</td>
                                                    <td>${element.fecha} ${element.hora}</td>
                                                    <td>${element.forma_pago}</td>
                                                    <td>${element.folio_forma_pago}</td>
                                                    <td><button type="button" onclick="borrarAbono(${element.id});" class="btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></td>
                                                </tr>
                                            `)
                                        });
                                    }
                                }
                            }})
                    
                }else{
                    toastr.error(response.mensaje, 'Error')
                    
                }
            }
        });
    } 
  }
}

function borrarAbono(id_abono){
    $.ajax({
        type: "POST",
        url: "./modelo/cuentas_pagar/borrar-abono.php",
        data: {id_abono, id_movimiento},
        dataType: "JSON",
        success: function (r) {
            if(r.estatus){
                toastr.success('Abono eliminado correctamente', 'Eliminado')
                
                $.ajax({
                    type: "post",
                    url: "./modelo/cuentas_pagar/traer-abonos-remision.php",
                    data: {id_movimiento},
                    dataType: "JSON",
                    success: function (respo) {
                        if(respo.estatus){
                            $("#importe-abonos").val(respo.importe_total)
                            $("#abonado-abonos").val(respo.pagado)
                            $("#restante-abonos").val(respo.restante)
                            if(respo.data.length > 0){
                                $('#tbody-abonos-remision').empty();
                                respo.data.forEach(element => {
                                    $('#tbody-abonos-remision').append(`
                                        <tr>
                                            <td>${element.id}</td>
                                            <td>${element.monto}</td>
                                            <td>${element.fecha} ${element.hora}</td>
                                            <td>${element.forma_pago}</td>
                                            <td>${element.folio_forma_pago}</td>
                                            <td><button type="button" onclick="borrarAbono(${element.id});" class="btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></td>
                                        </tr>
                                    `)
                                });
                            }else{
                                $('#tbody-abonos-remision').empty();
                                $('#tbody-abonos-remision').append(`
                                <tr>
                                    <td colspan="5" class="text-center">Sin abonos registrados</td>
                                </tr>
                            `)
                            }


                        }

                    }})
            }else{
                toastr.error(r.mensaje, 'Error')
            }
        }
    });
}

//----FUNCIONES PARA MODAL AGREGAR LLANTA----

function modalAgregarLlantas(){
    
    Swal.fire({
        width: '700px',
        title: 'Agregar llanta a remisión',
        html: `
        <div class="row justify-content-center mt-3">
                                    <div class="col-12 col-md-8 text-center">
                                        <label for="buscador">Busca una llanta registrada</label>
                                        <select  class="form-control" id="buscador"></select>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <a href="#" class="btn btn-success mt-4" onclick="agregarLLanta();">
                                            <span class="icon text-white-50">
                                                <i class="fas fa-plus-circle"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>

                                <div class="row justify-content-center mt-3">
                                     <div class="col-12 col-md-3">
                                        <label for="costo-actual">Costo</label>
                                        <input type="number" placeholder="0" class="form-control" id="costo-actual"> 
                                    </div>

                                    <div class="col-12 col-md-4 text-center">
                                        <label for="precio-actual">Precio</label>
                                        <input type="number" placeholder="0" class="form-control" id="precio-actual" valido>
                                        <div class="invalid-feedback" id="label-validator">
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-3 text-center">
                                        <label for="mayoreo-actual">Mayoreo</label>
                                        <input type="number" value="0" placeholder="0" class="form-control" id="mayoreo-actual" valido>
                                    </div>
                                </div>

                                <div class="row justify-content-center mt-3">

                                     <div class="col-12 col-md-3 text-center">
                                        <label for="stock">Stock actual</label>
                                        <select type="number" placeholder="0" class="form-control" id="stock_actual">

                                        </select>     
                                    </div>

                                    <div class="col-12 col-md-4 text-center">
                                        <label for="cantidad-agregar">¿Cuantas llantas vas a ingresar?</label>
                                        <input type="number" placeholder="0" class="form-control" id="cantidad-agregar" valido disabled>
                                        <div class="invalid-feedback" id="label-validator">
                                            
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-3 text-center">
                                        <label for="stock">Cantidad en partidas</label>
                                        <input type="number" value="0" placeholder="0" class="form-control" id="cantidad_piezas" valido disabled>
                                    </div>
                                </div>
                                <div class="row mt-3 justify-content-center">
                                    <div class="col-12 col-md-6 text-center">
                                        <label for="sucursal-remision">Sucursal</label>
                                        <select class="form-control" id="sucursal-remision"></select>
                                    </div>
                                    <div class="col-12 col-md-4 text-center">
                                        <label for="permiso-act-inv">Actualizar inventario</label>
                                        <select class="form-control" id="permiso-act-inv">
                                            <option value="1">Si</option>
                                            <option value="2">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-5 justify-content-center">
                                    <div class="col-12 col-md-3 text-center">
                                       <div class="btn btn-success disabled" id="btn-agregar" onclick="validarAgregarLlantasARemision()" disabled>Agregar llanta</div>
                                    </div>
                                    
                                </div>
        `,
        showCloseButton: true,
        didOpen: () => {

            $('#buscador').select2({
                placeholder: "Selecciona una llanta",
                theme: "bootstrap",
                minimumInputLength: 1,
                ajax: {
                    url: "./modelo/cambios/buscar-llanta-existencia.php",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                    
                     if(params.term == undefined){
                      params.term = "";
                    }
                  
                     return {
                       searchTerm: params.term, // search term
                       id_sucursal: $("#sucursales").val(),
                       page: params.page || 1,
                       
                     };
                    },
                    cache: true,
        
                }, processResults: function (data, params) {
                  params.page = params.page || 1;
                    return {
                       results: data.results,
                       pagination: {
                        more: (params.page * 10) < data.total_count // Verificar si hay más resultados para cargar
                      }
                    };
                  },
                language:  {
        
                    inputTooShort: function () {
                        return "Busca la llanta...";
                      },
                      
                    noResults: function() {
                
                      return "Sin resultados";        
                    },
                    searching: function() {
                
                      return "Buscando..";
                    }
                  },
        
                  templateResult: formatRepo,
                  templateSelection: formatRepoSelection
            })

            sucursales_arreglo.forEach(element => {
                
                $("#sucursal-remision").append(`
                    <option value="${element.id}">${element.nombre}</option
                `)
            });
        },
        showConfirmButton: false,
    })
}

function formatRepo (repo) {
          
    if (repo.loading) {
      return repo.text;
    }
      var $container = $(
          "<div class='select2-result-repository clearfix' desc='"+repo.Descripcion+" marca='"+repo.Marca +
          " id='"+repo.Marca+" costo='"+repo.precio_Inicial +" id='tyre' precio='"+repo.precio_Venta+" idcode='"+repo.id+"'>" +
          "<div class='select2-contenedor-principal row' syle='display:flex;'>" +
          "<div class='col-md-2 justify-content-center'><img class='' style='width: 50px; border-radius: 6px;' src='./src/img/logos/" + repo.Marca + ".jpg' /></div>" +
            "<div class='col-md-10 select2-contenedor'>" +
            "<div class='select2_modelo'>Modelo "+ repo.Modelo +"</div>" +
            "<div class='select2_description'>" + repo.Descripcion + "</div>" +
            "</div>" +
            "</div>" +
            "<span>Cod: "+repo.id+"</span>" +
            "<div class='select2_statistics' style='display:flex; border-top: 1px solid whitesmoke; padding-top:8px; justify-content:space-around; margin-top:5px;'>" +
            "<div class='select2_marca'><i class='fa fa-star'></i> "+ repo.Marca+"</div>" +
              "<div class='select2_costo'><i class='fa fa-dollar-sign'></i> "+repo.precio_Inicial+" (Costo) </div>" +
              "<div class='select2_precio_venta'><i class='fa fa-tag'></i> "+ repo.precio_Venta +" (precio)</div>" + 
            "</div>" +
          "</div>" +
        "</div>"
      );

      return $container;
      
    }

function formatRepoSelection (repo) {
    if(repo.id !== ""){
          $("#btn-agregar").attr("id_item", repo.id);
        $("#stock").prop("disabled", false);
        $("#btn-agregar").removeClass('disabled');
        $("#cantidad-agregar").prop("disabled", false);
        disabled_botton = true;
      }else{
        $("#stock").prop("disabled", true)
        $("#btn-agregar").addClass('disabled');
        disabled_botton = false;

      }

      /* validador();*/
      traerStockSucursales(repo.id); 

      $("#costo-actual").val(repo.precio_Inicial);
      $("#precio-actual").val(repo.precio_Venta);
      $("#mayoreo-actual").val(repo.precio_Mayoreo);
      $("#costo-actual").attr('costo',repo.precio_Inicial);
      $("#precio-actual").attr('precio',repo.precio_Venta);
      $("#mayoreo-actual").attr('mayoreo',repo.precio_Mayoreo);

      return repo.text || repo.Descripcion;
    }

function traerStockSucursales(id_llanta){
        $.ajax({
          type: "post",
          url: "./modelo/inventarios/traer-stock-sucursales.php",
          data: {id_llanta},
          dataType: "JSON",
          success: function (response) {
            $("#stock_actual").empty();
            if(response.data.length >0){
              response.data.forEach(element => {
                $("#stock_actual").append(`
                      <option>${element.nombre}: ${element.stock}</option>
                  `);
              });
            }else{
    
            }
          }
        });
        
      }


function validarAgregarLlantasARemision(){
    if(!disabled_botton){
        return false;
    }
    let id_llanta =  $("#btn-agregar").attr("id_item");
    let id_sucursal = $("#sucursales").val();
    let cantidad = $("#cantidad-agregar").val()
    let costo_actual = $("#costo-actual").val();
    let costo_antes = $("#costo-actual").attr('costo');
    let precio_actual = $("#precio-actual").val();
    let precio_antes = $("#precio-actual").attr('precio');
    let mayoreo_actual = $("#mayoreo-actual").val();
    let mayoreo_antes = $("#mayoreo-actual").attr('mayoreo');
    let sucursal_remision = $("#sucursal-remision").val();
    permiso_act_inv = $("#permiso-act-inv").val();
    if(cantidad.length ==0 || cantidad == 0 || cantidad == '' || cantidad == null || cantidad ==undefined){
        Swal.showValidationMessage('Escribe una cantidad');
    }else if(cantidad < 0){
        Swal.showValidationMessage('La cantidad no puede ser menor a 0');
    }else
    if(costo_actual != costo_antes || precio_actual != precio_antes || mayoreo_actual != mayoreo_antes){
        Swal.fire({
            icon: 'question',
            html: `¿Quieres actualizar los precios de esta llanta en el catalogo?`,
            showCancelButton: true,
            confirmButtonText: 'Actualizar',
            cancelButtonText: 'No',
          }).then((r)=>{
            if(r.isConfirmed){
                let actualizar_precio = true;
                agregarLlantasARemision(id_llanta, sucursal_remision, cantidad, costo_actual, costo_antes, precio_actual, precio_antes, mayoreo_actual, mayoreo_antes, tipo_remision, actualizar_precio)
            }else{
                let actualizar_precio = false;

                agregarLlantasARemision(id_llanta, sucursal_remision, cantidad, costo_actual, costo_antes, precio_actual, precio_antes, mayoreo_actual, mayoreo_antes, tipo_remision, actualizar_precio)

            }
          })
    }else{
        let actualizar_precio = false;
        agregarLlantasARemision(id_llanta, sucursal_remision, cantidad, costo_actual, costo_antes, precio_actual, precio_antes, mayoreo_actual, mayoreo_antes, tipo_remision, actualizar_precio)
    }
      }


function agregarLlantasARemision(id_llanta, id_sucursal, cantidad, costo_actual, costo_antes, precio_actual, precio_antes, mayoreo_actual, mayoreo_antes, tipo_remision, actualizar_precio){
        
        $.ajax({
            type: "post",
            url: "./modelo/movimientos/ingresar-nuevo-a-remision.php",
            data: {id_llanta, id_sucursal, cantidad, costo_actual, costo_antes, precio_actual, precio_antes, mayoreo_actual, mayoreo_antes, tipo_remision, actualizar_precio, id_movimiento, permiso_act_inv},
            dataType: "JSON",
            success: function (response) {
                if(response.estatus){
                    Swal.fire({
                        icon: 'success',
                        html: response.mensaje
                    }).then(()=>{
                        actualizarTabla()
                    })
                }else{
                    Swal.fire({
                        icon: 'error',
                        html: response.mensaje
                    }).then(()=>{
                        actualizarTabla()
                    })
                }
            }
        });
      }

 //___EDITAR LLANTAS
 
 function editarLlantaRemision(id_historial){
    $.ajax({
        type: "post",
        url: "./modelo/movimientos/traer-detalle-movimiento.php",
        data: {id_historial},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                Swal.fire({ 
                    width:"600px",
                    title: 'Editar llanta',
                    html:`
                    <div class="container">
                        <div class="row">
                        <!--<div class="col-12">
                                <label for="buscador">Selecciona la llanta por la que actualizaras</label>
                                <select  class="form-control" id="buscador"></select>
                            </div>--->
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <label for="buscador">Llanta registrada</label>
                                <input type="text" class="form-control" placeholder="Llanta actual" id="llanta-actual"></input>
                            </div>
                        </div>
                        <div class="row mt-2">
                            
                        </div>
                        <div class="row mt-2 mb-3">
                            <div class="col-4">
                                <label for="buscador">Cantidad</label>
                                <input type="number" class="form-control" placeholder="0" id="cantidad-historial" onchange="obtenerNuevoImporte()"></input>
                            </div>
                            <div class="col-4">
                                <label for="buscador">Costo</label>
                                <input type="number" class="form-control" placeholder="0.00" id="costo-historial"></input>
                            </div>
                            <div class="col-4">
                                <label for="buscador">Importe</label>
                                <input type="number" class="form-control" placeholder="0.00" id="importe-historial"></input>
                            </div>
                        </div>
                        <small><b>Nota:</b> No se actualizará el inventario.</small>
                    </div>
                    `,
                    confirmButtonText: 'Actualizar',
                    showLoaderOnConfirm: true,
                    preConfirm: ()=>{
                        let cant_act = $("#cantidad-historial").val();
                        console.log(cant_act);
                        if(cant_act == '' || cant_act == null || cant_act.length == 0 || cant_act == undefined || cant_act == ' ' || cant_act ==0){
                            Swal.showValidationMessage('Escribe una cantidad');
                        }else if(cant_act < 0){
                            Swal.showValidationMessage('La cantidad no puede ser menor a 0');
                        }
                    },
                    didOpen:()=>{
                        $('#buscador').select2({
                            placeholder: "Selecciona una llanta",
                            theme: "bootstrap",
                            minimumInputLength: 1,
                            ajax: {
                                url: "./modelo/cambios/buscar-llanta-existencia.php",
                                type: "post",
                                dataType: 'json',
                                delay: 250,
                                data: function (params) {
                                
                                 if(params.term == undefined){
                                  params.term = "";
                                }
                              
                                 return {
                                   searchTerm: params.term, // search term
                                   id_sucursal: $("#sucursales").val(),
                                   page: params.page || 1,
                                   
                                 };
                                },
                                cache: true,
                    
                            }, processResults: function (data, params) {
                              params.page = params.page || 1;
                                return {
                                   results: data.results,
                                   pagination: {
                                    more: (params.page * 10) < data.total_count // Verificar si hay más resultados para cargar
                                  }
                                };
                              },
                            language:  {
                    
                                inputTooShort: function () {
                                    return "Busca la llanta...";
                                  },
                                  
                                noResults: function() {
                            
                                  return "Sin resultados";        
                                },
                                searching: function() {
                            
                                  return "Buscando..";
                                }
                              },
                    
                              templateResult: formatRepo,
                              templateSelection: formatRepoSelection
                        })

                        let data = response.data[0];
                        $("#llanta-actual").val(data.descripcion);
                        $("#cantidad-historial").val(data.cantidad);
                        $("#costo-historial").val(data.costo);
                        $("#importe-historial").val(data.importe);

                        $("#cantidad-historial").on('keyup', ()=>{
                            let valor = $("#costo-historial").val();
                            let cant = $("#cantidad-historial").val();
                            let nuevo_imp = valor * cant;
                            $("#importe-historial").val(nuevo_imp);
                        })
                    }
                    }).then((re)=>{  
                        if(re.isConfirmed){
                            let cant_historial = $("#cantidad-historial").val();
                            let costo_historial = $("#costo-historial").val();
                            let importe_historial = $("#importe-historial").val();
                            $.ajax({
                                type: "post",
                                url: "./modelo/movimientos/actualizar-partidas-remision.php",
                                data: {id_historial, cant_historial, costo_historial, importe_historial},
                                dataType: "json",
                                success: function (response) {
                                    if(response.estatus){
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.mensaje
                                        }).then(()=>{
                                            actualizarTabla()
                                        })
                                    }else{
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.mensaje
                                        }).then(()=>{
                                            actualizarTabla()
                                        })
                                    }
                                }
                            });
                        }
                     })
            }else{
                Swal.fire({
                    title: response.mensaje
                })
            }
        }
    });
 }

 function obtenerNuevoImporte(){
    let valor = $("#costo-historial").val();
    let cant = $("#cantidad-historial").val();
    let nuevo_imp = valor * cant;
    $("#importe-historial").val(nuevo_imp);
}

function borrarrLlantaRemision(id_historial){
    
    $.ajax({
        type: "post",
        url: "./modelo/movimientos/traer-detalle-movimiento.php",
        data: {id_historial},
        dataType: "json",
        success: function (response) {
            switch (response.data[0].id_destino) {
                case 1:
                    index_sucursales = 0;
                    break;
                case 2:
                    index_sucursales = 1;
                    break;    
                case 5:
                    index_sucursales = 2;
                    break;
                case 6:
                    index_sucursales = 3;
                    break;
                default:
                    break;
            }
            Swal.fire({
                icon:'question',
                html:`
                <h3>¿Quieres borrar este registro de la remisión?</h3>
                <label>Borrar la llanta del inventario</label>
                <label>Stock actual ${sucursales_arreglo[index_sucursales].nombre}: ${response.data[0].stock}</label>
                <select id="permiso-borrar-inv" class="form-control">
                    <option value="1" selected>Si</option>
                    <option value="2">No</option>
                </select>
                `,
                confirmButtonText: 'Si',
                showCancelButton: true,
                cancelButtonText: 'No',
            }).then(function(r){
                if(r.isConfirmed){
                    let permiso_borrar = $("#permiso-borrar-inv").val();
                    
                    $.ajax({
                        type: "post",
                        url: "./modelo/movimientos/borrar-llanta-remision.php",
                        data: {id_historial, permiso_borrar},
                        dataType: "JSON",
                        success: function (response) {
                            if(response.estatus){
                                Swal.fire({
                                    icon: 'success',
                                    title: response.mensaje
                                }).then(function(){
                                    actualizarTabla();
                                })
                            }
                        }
                    });
                }
            })
        }
    });
    
}

function actualizarTabla(){
    $("#tbody-llantas-remision").empty();
    $("#tbody-llantas-remision").append(`
    <tr>
        <td colspan="11" class="text-center">
            <img src="src/img/preload.gif" style="width:90px !important;"></img>
        </td>
    </tr>
`)
setTimeout(function(){
    $.ajax({
        type: "post",
        url: "./modelo/cuentas_pagar/traer-movimiento.php",
        data: {'id_movimiento':id_movimiento},
        dataType: "JSON",
        success: function (response) {
            if(response.estatus){
                $("#proveedor").empty();
                    response.proveedores.forEach(element => {
                    $("#proveedor").append(`
                        <option value="${element.id}">${element.nombre}</option>
                    `);
                });
                $("#proveedor").val(response.datos_movimiento[0].proveedor_id);
    
                $("#usuario").empty();
                response.usuarios.forEach(element => {
                $("#usuario").append(`
                    <option value="${element.id}">${element.nombre} ${element.apellidos}</option>
                    `);
                });
                $("#usuario").val(response.datos_movimiento[0].id_usuario);
    
    
                $("#factura").val(response.datos_movimiento[0].folio_factura);
                $("#estado-factura").val(response.datos_movimiento[0].estado_factura);
                $("#estatus").val(response.datos_movimiento[0].estatus);
                $("#importe").val(response.datos_movimiento[0].total);
                $("#pagado-total").val(response.datos_movimiento[0].pagado);
                $("#restante-total").val(response.datos_movimiento[0].restante);
                $("#descripcion-remision").val(response.datos_movimiento[0].descripcion);
                $("#mercancia").val(response.datos_movimiento[0].mercancia);
    
                 sucursales_arreglo = response.sucursales;
                 $("#sucursales").empty();
                 sucursales_arreglo.forEach(element => {
                    
                    $("#sucursales").append(`
                        <option value="${element.id}">${element.nombre}</option
                    `)
                });
    
                $("#sucursales").val(response.datos_movimiento[0].sucursal);
    
    
                $("#tbody-llantas-remision").empty();
                if(response.llantas_movimiento.length>0){

                    let contador =0;
                    response.llantas_movimiento.forEach(element => {
                        contador++;
                        let ubicacionObj = element.id_ubicacion === 0 ? { id: 0, nombre: 'Bodega' } : response.sucursales.find(sucursal => sucursal.id === element.id_ubicacion);
                         let destinoObj = response.sucursales.find(sucursal => sucursal.id === element.id_destino);
                       let id_usuario = $("#emp-title").attr('sesion_id')
                       if(id_usuario == 22 || id_usuario ==15){
                        $("#tbody-llantas-remision").append(`
                        <tr>
                            <td>${contador}</td>
                            <td>${element.cantidad}</td>
                            <td>${element.descripcion}</td>
                            <td>${element.marca}</td>
                            <td>${element.id_ubicacion ==0 ? 'Bodega': ubicacionObj.nombre}</td>
                            <td>${destinoObj.nombre}</td>
                            <td>${element.stock_destino_anterior}</td>
                            <td>${element.stock_destino_actual}</td>
                            <td>${element.costo}</td>
                            <td>${element.importe}</td>
                            <td>
                            <div class="btn btn-success" onclick="editarLlantaRemision(${element.id})"><i class="fas fa-pen"></i></div>
                           
                            </td>
                        </tr>
                    `)
                       }else{
                        $("#tbody-llantas-remision").append(`
                        <tr>
                            <td>${contador}</td>
                            <td>${element.cantidad}</td>
                            <td>${element.descripcion}</td>
                            <td>${element.marca}</td>
                            <td>${element.id_ubicacion ==0 ? 'Bodega': ubicacionObj.nombre}</td>
                            <td>${destinoObj.nombre}</td>
                            <td>${element.stock_destino_anterior}</td>
                            <td>${element.stock_destino_actual}</td>
                            <td>${element.costo}</td>
                            <td>${element.importe}</td>
                            <td>
                            <div class="btn btn-success" onclick="editarLlantaRemision(${element.id})"><i class="fas fa-pen"></i></div>
                            <div class="btn btn-danger" onclick="borrarrLlantaRemision(${element.id})"><i class="fas fa-trash"></i></div>
                            </td>
                        </tr>
                    `)
                       }
                        
                    });
                }else{
                    $("#tbody-llantas-remision").append(
                        `
                        <tr>
                            <td colspan="11" class="text-center">
                                <span>No existen partidas en este movimiento</span>
                            </td>
                        </tr>`
                    );
                }
    
            }
        }
      });
},1000)
   
   
}

function buscarFoliosCuentasPorPagar(){
    let folio = $("#buscar-folio-factura").val();
    if(folio.trim() == ""){
        
        toastr.error("Escribe un folio de factura o movimiento", 'Error');
      }else{
        $("#contenedor-loader").removeClass('d-none')
        setTimeout(function () {
            
                  $.ajax({
                      type: "post",
                      url: "./modelo/movimientos/buscar-folio-factura.php",
                      data: {folio},
                      dataType: "json",
                      success: function (response) {
                          if(response.estatus){
                            $("#contenedor-loader").addClass('d-none')
                              if(response.total_facturas >1){
                                  Swal.fire({
                                      icon: 'info',
                                      width: "1400px",
                                      title: 'Se encontrarón mas de 1 factura',
                                      html: `
                                      <span>Seleccione la factura que quiera cargar: </span>
                                      <div class="container-fluid mt-2">
                                          <div class="row">
                                              <div class="col-md-12">
                                                  <ul class="list-group">
                                                      <li class="list-group-item active">
                                                          <div class="row">
                                                              <div class="col-md-1">#</div>
                                                              <div class="col-md-4">Proveedor</div>
                                                              <div class="col-md-1">Folio</div>
                                                              <div class="col-md-1">Fecha</div>
                                                              <div class="col-md-4">Mercancia</div>
                                                              <div class="col-md-1">Usuario</div>
                                                          </div>
                                                      </li>
                                                      <div id="facturas-encontradas">
                                                          
                                                          </div>
                                                  </ul>
                                              </div>
                                          </div>
                                      </div>`,
                                      didOpen: function(){
                                          $("#facturas-encontradas").empty();
                                          response.data.forEach(element => {
                                              $("#facturas-encontradas").append(`
                                              <li class="list-group-item list-group-item-action" style="cursor:pointer" onclick="cargarNuevoMovimiento(${element.id})">
                                              <div class="row">
                                                              <div class="col-md-1">${element.id}</div>
                                                              <div class="col-md-4">${element.proveedor}</div>
                                                              <div class="col-md-1">${element.folio_factura}</div>
                                                              <div class="col-md-1">${element.fecha}</div>
                                                              <div class="col-md-4">${element.mercancia}</div>
                                                              <div class="col-md-1">${element.usuario}</div>
                                                          </div>
                                              </li>
                                              `)
                                          });
                                      }
                                  })
          
                              }else if(response.total_facturas ==1){
                                response.data.forEach(element => {
                                    cargarNuevoMovimiento(element.id)
                                })
                              }
                          }
                      }
                  });
              
        },1000)
        
      }
  
  }


function cargarNuevoMovimiento(id_movimiento){
    $("#contenedor-loader").removeClass('d-none')
    setTimeout(function () {
        const dominio = window.location.hostname;   
        location.href =  'administracion-movimiento.php?id=' + id_movimiento + '&tipo_remision=2';
    },1500)
} 

function cargarComprobante(extension_archivo){
    let area_canvas = $("#area-canvas");
    $.ajax({
        url: "./src/docs/facturas_compras/FAC-"+id_movimiento+"."+extension_archivo,
        method: "GET",
        xhr: function() {
          var xhr = new window.XMLHttpRequest();
          xhr.responseType = "arraybuffer";
          return xhr;
        },
        success: function(data) {
         if(extension_archivo =='pdf'){
            var pdfData = new Uint8Array(data);
            var pdfURL = URL.createObjectURL(new Blob([pdfData], { type: "application/pdf" }));
        
            // Continuar con el código original usando `pdfURL`
            var pdfjsLib = window['pdfjs-dist/build/pdf'];
              // Obtiene el elemento canvas
              area_canvas.empty().append(`
              
              <canvas id="thumbnailCanvas" onclick="verDocumento('pdf', ${id_movimiento})" width="100" height="150"></canvas>`)
              var canvas = document.getElementById('thumbnailCanvas');
  
              // Carga el PDF
              pdfjsLib.getDocument(pdfURL).promise.then(function(pdfDoc) {
              // Obtiene la primera página del PDF (página 1)
              var pageNumber = 1;
  
              // Renderiza la página en el canvas
              pdfDoc.getPage(pageNumber).then(function(page) {
                  var viewport = page.getViewport({ scale: 0.2 });
                  var context = canvas.getContext('2d');
                  canvas.width = viewport.width;
                  canvas.height = viewport.height;
  
                  var renderContext = {
                  canvasContext: context,
                  viewport: viewport,
                  };
                  page.render(renderContext).promise.then(function() {

                  });
              });
              });
         }else{
            let random_v = Math.floor(Math.random() * 900000) + 100000;
            area_canvas.empty().append(`
              <img height="200" onclick="verDocumento('${extension_archivo}', ${id_movimiento})" src="./src/docs/facturas_compras/FAC-${id_movimiento}.${extension_archivo}?v=${random_v}">`)
         }

         $("#area-canvas").attr('documento', true)
          
        },
        error: function(message){
            area_canvas.empty().append(`
            <div class="contenedor-cargar-documento" id="contenedor-cargar-documento">Sin factura cargada</div>
            
            `)
            $("#area-canvas").attr('documento', false)
        }

      });
}


function cargarComprobanteEdicion(){
    document.getElementById("input-comprobante-edicion").click(); 
}

function cargarComprobanteRegistro(){
    let input_comprobante = document.getElementById('input-comprobante-edicion');
    let file = input_comprobante.files[0];
    let area_canvas = $("#area-canvas");

    if (file && file.type === 'application/pdf') {
    // Ruta del PDF desde donde se obtendrá la miniatura
    var pdfURL = URL.createObjectURL(file);//'./src/docs/gastos/Folio RAY440.pdf'; // Reemplaza con la ruta correcta a tu archivo PDF
    var pdfjsLib = window['pdfjs-dist/build/pdf'];
    // Obtiene el elemento canvas
    area_canvas.empty().append(`
    <span aria-hidden="true" class="btn-x-documento" onclick="deleteThumb()">×</span>
    <canvas id="thumbnailCanvas" onclick="verDocumento('pdf', ${id_movimiento})" width="100" height="150"></canvas>`)
    var canvas = document.getElementById('thumbnailCanvas');

    // Carga el PDF
    pdfjsLib.getDocument(pdfURL).promise.then(function(pdfDoc) {
      // Obtiene la primera página del PDF (página 1)
      var pageNumber = 1;

      // Renderiza la página en el canvas
      pdfDoc.getPage(pageNumber).then(function(page) {
        var viewport = page.getViewport({ scale: 0.2 });
        var context = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;

        var renderContext = {
          canvasContext: context,
          viewport: viewport,
        };
        
        
        page.render(renderContext).promise.then(function() {
     
        });
      });
    });
    $("#input-comprobante-edicion").attr('eliminar', false);
   // reader.readAsDataURL(file);
  }else if (file.type.startsWith('image/')){
    // Si es una imagen (cualquier tipo de imagen)
    var reader = new FileReader();
    console.log(file.type);
    area_canvas.empty().append(`
    <span aria-hidden="true" class="btn-x-documento" onclick="deleteThumb()">×</span>
    <img src="" height="200" id="gasto-imagen" onclick="verDocumento('', ${id_movimiento})">`)
    let img_preview = document.getElementById('gasto-imagen')
  
      reader.onloadend = function () {
        img_preview.src = reader.result;
      }
    
      if (file) {
        reader.readAsDataURL(file);
       // clearCanvas()
      } else {
        img_previewpreview.src = "";
      }

      Swal.resetValidationMessage()
      toastr.success('Documento adjunto agregado con exito' ); 
      $("#input-comprobante-edicion").attr('eliminar', false);
  }else{
    area_canvas.empty()
    Swal.showValidationMessage(
      `Tipo de archivo no admitido`
    );
  }
  }

  function clearCanvas() {
    var canvas = document.getElementById('thumbnailCanvas');
    var context = canvas.getContext('2d');
    context.clearRect(0, 0, canvas.width, canvas.height);
  }

  function deleteThumb(flag =0){
      let area_canvas = $("#area-canvas");
      area_canvas.empty().append(`<div class="contenedor-cargar-documento-edicion" onclick="cargarComprobanteEdicion()" id="contenedor-cargar-documento">Sin factura cargada</div>
     `)
      flag !== 1 ? toastr.success('Documento adjunto eliminado con exito' ) : false; 
      $("#input-comprobante-edicion").val('').attr('eliminar', true);
    eliminar_comprobante = true;
  }

 function verDocumento(ext, id_movimiento){
    console.log(ext);
    console.log(id_movimiento);
    Swal.fire({
        width:  '100%',
        title: 'Documento cargado',
        showCloseButton: true,
        html: `
        <div class="swal-pdf-container text-center">
            <embed type="application/pdf"  width="1500" height="600" src="./src/docs/facturas_compras/FAC-${id_movimiento}.${ext}"/>
        </div>
        `
    })
 }