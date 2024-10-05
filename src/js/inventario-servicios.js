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


$(document).ready(function () {

    reloadTabla();


});


function agregarServicio(){
    Swal.fire({
        title: 'Agregar servicio',
        html: `
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-12">
                    <label>Descripcion</label>
                    <textarea class="form-control" id="descripcion" placeholder="Agrega una descripcion del servicio"></textarea>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12 col-md-12">
                    <label>Precio</label>
                    <input class="form-control" type="numer" id="precio" placeholder="0">
                </div>
            </div>
            <div class="row m-2">
            <div class="col-12 col-md-12">
                <label>Estatus</label>
                <select class="form-control" id="estatus">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>
        <div class="row">
        <div class="col-12 col-md-12">
            <label>Tipo</label>
            <select class="form-control" id="tipo">
            <option value="balanceo">Servicio al auto</option>
                <option value="reparacion">Servicio de Reparación</option>
                <option value="Cambio2">Servicio a neumatico</option>
            </select>
        </div>
    </div>

        </div>
        `,
        showConfirmButton: true,
        confirmButtonText: 'Agregar',
        showCancelButton: true,
        cancelButtonText: 'Mejor no',
        preConfirm: function () {
            if(descripcion ==""){

                toastr.error("Especifica una dirección", 'Error' );
                return false;
            }else if(precio ==""){

                toastr.error("Especifica una precio", 'Error' );
                return false;
            }else{
                return true;
            }
        }
    }).then(function(response) {

        if (response.isConfirmed){
            let descripcion = $("#descripcion").val();
            let precio = $("#precio").val();
            let estatus = $("#estatus").val();
            let tipo = $("#tipo").val();

            if(descripcion ==""){

                toastr.error("Especifica una dirección", 'Error' );
            }else if(precio ==""){

                toastr.error("Especifica una precio", 'Error' );
            }else{
                $.ajax({
                    type: "POST",
                    url: "./modelo/servicios/agregar-servicios.php",
                    data: {"descripcion": descripcion,
                           "precio": precio,
                           "estatus": estatus,
                           "tipo": tipo},
                    success: function (response) {
                        if(response==1){
                            Swal.fire({
                                icon: 'success',
                                html: "<b>Servicio registrado correctamente</b>"
                            });
                            reloadTabla();

                        }
                    }
                });
            }
        }
    })
}

function reloadTabla() {
    $.ajax({
        type: "POST",
        url: "./modelo/servicios/traer-servicios.php",
        data: "data",
        dataType: "JSON",
        success: function (response) {
         let contador = 0;
         $("#cuerpo-services").empty();
            response.forEach(element => {
                contador++;
                $("#cuerpo-services").append(`
                <button type="button" class="list-group-item list-group-item-action">         
                     <div class="row">
                         <div class="col-12 col-md-1">${contador}</div>
                         <div class="col-12 col-md-2">${element.codigo}</div>
                         <div class="col-12 col-md-3">${element.descripcion}</div>
                         <div class="col-12 col-md-1">${element.precio}</div>
                         <div class="col-12 col-md-2">${element.estatus}</div>
                         <div class="col-12 col-md-2">
                         <img src="./src/img/services/${element.img}.png" style="width:53px;"></div>
                         <div class="col-12 col-md-1" style="display: flex;">
                             <div class="btn btn-warning mr-1 d-flex align-items-center" onclick="editar(${element.id})"><i class="fas fa-pen"></i></div>
                             <div class="btn btn-danger d-flex align-items-center" onclick="eliminar(${element.id})"><i class="fas fa-trash"></i></div>
                         </div>
                     </div>
                </buttom>
                `)
            });
        }
    }); 
  }


  function editar(id){

    $.ajax({
        type: "POST",
        url: "./modelo/servicios/traer-servicio-ind.php",
        data: {"id_servicio": id},
        dataType: "JSON",
        success: function (response) {
            
            Swal.fire({
                title: 'Agregar servicio',
                html: `
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <label>Descripcion</label>
                            <textarea class="form-control" id="descripcion" placeholder="Agrega una descripcion del servicio">${response.descripcion}</textarea>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 col-md-12">
                            <label>Precio</label>
                            <input class="form-control" type="numer" id="precio" value="${response.precio}" placeholder="0">
                        </div>
                    </div>
                    <div class="row m-2">
                    <div class="col-12 col-md-12">
                        <label>Estatus</label>
                        <select class="form-control" id="estatus">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                <div class="col-12 col-md-12">
                    <label>Tipo</label>
                    <select class="form-control" id="tipo">
                    <option value="balanceo">Servicio al auto</option>
                        <option value="Reparacion">Servicio de Reparación</option>
                        <option value="Cambio2">Servicio a neumatico</option>
                    </select>
                </div>
            </div>
        
                </div>
                `,
                didOpen: function () {
                    $("#estatus").val(response.estatus);
                    $("#tipo").val(response.img);
                },
                showConfirmButton: true,
                confirmButtonText: 'Actualizar',
                showCancelButton: true,
                cancelButtonText: 'Mejor no',
                preConfirm: function () {
                    if(descripcion ==""){
        
                        toastr.error("Especifica una dirección", 'Error' );
                        return false;
                    }else if(precio ==""){
        
                        toastr.error("Especifica una precio", 'Error' );
                        return false;
                    }else{
                        return true;
                    }
                }
            }).then(function(response) {
        
                if (response.isConfirmed){
                    let descripcion = $("#descripcion").val();
                    let precio = $("#precio").val();
                    let estatus = $("#estatus").val();
                    let tipo = $("#tipo").val();
        
                    if(descripcion ==""){
        
                        toastr.error("Especifica una dirección", 'Error' );
                    }else if(precio ==""){
        
                        toastr.error("Especifica una precio", 'Error' );
                    }else{
                        $.ajax({
                            type: "POST",
                            url: "./modelo/servicios/actualizar-servicio.php",
                            data: {"id_serv": id,
                                   "descripcion": descripcion,
                                   "precio": precio,
                                   "estatus": estatus,
                                   "tipo": tipo},
                            success: function (response) {
                                if(response==1){
                                    Swal.fire({
                                        icon: 'success',
                                        html: "<b>Servicio actualizado correctamente</b>"
                                    });
                                    reloadTabla();
        
                                }
                            }
                        });
                    }
                }
            });

        }
    });
    
}

function eliminar(id){
    Swal.fire({
        icon: "question",
        html: "<h3>¿Seguro que desea eliminar este servicio?</h3>",
        showCancelButton: true,
        cancelButtonText:"Mejor no",
        confirmButtonText: "Eliminar"
    }).then(function(response){
        if(response.isConfirmed){
            $.ajax({
                type: "POST",
                url: "./modelo/servicios/eliminar-servicio.php",
                data: {"id": id},
                //dataType: "dataType",
                success: function (response) {
                    Swal.fire({
                        icon: "success",
                        html: "<h3>Servicio eliminado</h3>",
                    });
                    reloadTabla();
                }
            });
        }
    })

}
