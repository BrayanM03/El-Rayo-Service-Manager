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
  ocultarSidebar();
function setFilters(){
    let valor =$("#filtro").val();

    console.log(valor);
    switch (valor) {
            case "no aplica":
            $(".filters-area").empty();
            break;

            case "sucursal":
            $(".filters-area").empty().append(`
            <div class="row justify-content-center" style="display:flex; align-items:end;">
                <div class="col-12 col-md-2 text-center">
                <label>Sucursal</select>
                <select class="form-control clase_unica" id="id_sucursal"></select>
                </div>

                <div class="col-12 col-md-1 mb-2">
                <a href="#" class="btn btn-info btn-icon-split"  id="search-by-filter" onclick="buscarVentas('id_sucursal');">
                           <span class="icon text-white-50">
                               <i class="fas fa-search"></i>
                           </span>
                           <span class="text">Buscar</span>
               </a>
           
               </div>
            `);

            $("#id_sucursal").empty().append("<option value='nulo'>Selecciona una sucursal</option>");

            $.ajax({
                type: "POST",
                url: "./modelo/busqueda/traer-sucursales.php",
                data: "data",
                dataType: "JSON",
                success: function (response) {
                    response.forEach(element => {
                       
                    $("#id_sucursal").append(`
                    <option value="${element.id}">${element.nombre}</option>
                    `); 
                    });
                }
            });
            break;

            case "vendedor":
            $(".filters-area").empty().append(`
               <div class="row justify-content-center" style="display:flex; align-items:end;">
                  <div class="col-12 col-md-3 text-center">
                    <label for="f-vendedor">Vendedor</select>
                    <select class="form-control clase_unica" id="id_Usuarios"></select>
                </div>
                <div class="col-12 col-md-1 text-center mb-2">
                <a href="#" class="btn btn-info btn-icon-split"  id="search-by-filter" onclick="buscarVentas('id_Usuarios');">
                               <span class="icon text-white-50">
                                   <i class="fas fa-search"></i>
                               </span>
                               <span class="text">Buscar</span>
                    </a>
                </div>
                </div>
            `);


            $("#id_Usuarios").empty().append("<option value='nulo'>Selecciona una vendedor</option>");

            $.ajax({
                type: "POST",
                url: "./modelo/busqueda/traer-usuarios.php",
                data: "data",
                dataType: "JSON",
                success: function (response) {
                    response.forEach(element => {
                       
                    $("#id_Usuarios").append(`
                    <option value="${element.id}">${element.nombre}</option>
                    `); 
                    });
                }
            });
            break;

            case "cliente":
            $(".filters-area").empty().append(`
            <div class="row justify-content-center" style="display:flex; align-items:end;">
                <div class="col-12 col-md-5">
                    <label for="f-cliente">Cliente</label>
                    <select class="form-control clase_unica" id="id_Cliente"></select>
                </div>

                <div class="col-12 col-md-1">
                 <a href="#" class="btn btn-info btn-icon-split"  id="search-by-filter" onclick="buscarVentas('id_Cliente');">
                            <span class="icon text-white-50">
                                <i class="fas fa-search"></i>
                            </span>
                            <span class="text">Buscar</span>
                </a>
            
                </div>
                </div>
            `);

            $("#id_Cliente").select2({
                placeholder: "Clientes",
                theme: "bootstrap",
                ajax: {
                    url: "./modelo/ventas/traer_clientes.php",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
        
                    data: function (params) {
                     return {
                       searchTerm: params.term // search term
                       
                     };
                    },
                    processResults: function (data) {
                        return {
                           results: data
                        }; 
                      },
                   
                    cache: true
        
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
        
                  templateResult: formatResultClientes,
                  templateSelection: formatSelection
        
            });
        
            function formatResultClientes(repo){
        
        
                if (repo.loading) {
                    return repo.text;
                  }
                  
                  if (repo.credito == 0) {
                      cred = "Sin credito"
                      badge="badge-info";
                  }else if (repo.credito == 1){
                      cred= "Con credito";
                      badge = "badge-warning";
                  }
        
                    var $container = $(
                        "<span id='"+repo.id+"'>"+ repo.nombre +" <span class='badge " + badge +"'>"+ cred +"</span></span>"
                    );
                  
                   
                    //
                  
                    return $container;
        
            }
        
            function formatSelection (repo) {
                //A partir de aqui puedes agregar los clientes
                
                $("#select2-clientes-container").attr("id-cliente", repo.id);
             
               
        
                return repo.text || repo.nombre;
              }
            break;

            case "medidas":
            $(".filters-area").empty().append(`
            <div class="row justify-content-center mb-3" style="display:flex; align-items:end;">
            <div class="col-12 col-md-2">
                 <label for="ancho">Ancho</label>
                 <select name="" class="form-control" id="ancho" onclick="getMedidas('ancho');">
                     <option value="No aplica">Selecciona un ancho</option>
                 
                 </select>
            </div>
            <div class="col-12 col-md-2">
                 <label for="proporcion">Perfil</label>
                 <select name="" class="form-control" id="proporcion" onclick="getMedidas('proporcion');">
                     <option value="No aplica">Selecciona una proporcion</option>
                 </select>
            </div>
            <div class="col-12 col-md-2">
                 <label for="diametro">Diametro</label>
                 <select name="" class="form-control" id="diametro" onclick="getMedidas('diametro');">
                      <option value="No aplica">Selecciona un diametro</option>
                 </select>
            </div>
            <div class="col-12 col-md-1">
                 <a href="#" class="btn btn-info btn-icon-split"  id="search-by-filter" onclick="buscarVentasPorMedida('medidas');">
                            <span class="icon text-white-50">
                                <i class="fas fa-search"></i>
                            </span>
                            <span class="text">Buscar</span>
                </a>
            </div>
           
        </div>
            `);
            break;
        default:
            break;
    }
}


function getMedidas(parametro) {

    let $select = $("#"+parametro);
    $.ajax({
        type: "POST",
        url: `./modelo/ventas/traer-datos-filtro-${parametro}.php`,
        data: "sucursal_id",
        dataType: "JSON",
        success: function (response) {
          
            response.sort(function(a, b) {return a - b});
            let selectedValue = $("#"+parametro).val();
            
            let html = response.filter((e, i, a) => a.indexOf(e) === i).map(item => `<option value="${item}">${item}</option>`); 
            
            $select.html(html).val(selectedValue);
    
    
        }
    });
}


function buscarVentas(selector){
   filtro =  $(".clase_unica").val();
   $(".resultados").empty().append(`
   <a href="#" class="list-group-item list-group-item-action">
   <div class="row">
       <div class="col-12 col-md-12 text-center"><img src="./src/img/preload.gif" style="width:70px;"><br></img>Buscando...</div>
   </div>
   </a>`);
    $.ajax({
        type: "POST",
        url: "./modelo/busqueda/busqueda-avanzada-ventas.php",
        data: {"filtro": filtro, "columna": selector, "comienzo": 0},
        dataType: "JSON",
        success: function (response) {
            $(".resultados").empty();
            if(response.status !== false){
                let total_reg = response.total_registros;
                let paginas_sin_redondear = total_reg / 5;
                let paginas = Math.ceil(paginas_sin_redondear);
                
               /*  contador =0;
                response.registros.forEach(element => {
                    contador++;
                    $(".resultados").append(`
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="row">
                            <div class="col-12 col-md-1">${contador}</div>
                            <div class="col-12 col-md-1">RAY${element.id}</div>
                            <div class="col-12 col-md-1">${element.Fecha}</div>
                            <div class="col-12 col-md-1">${element.sucursal}</div>
                            <div class="col-12 col-md-1">${element.vendedor}</div>
                            <div class="col-12 col-md-3">${element.cliente}</div>
                            <div class="col-12 col-md-1">${element.tipo}</div>
                            <div class="col-12 col-md-1">${element.Total}</div>
                            <div class="col-12 col-md-1">${element.estatus}</div>
                            <div class="col-12 col-md-1"></div>
                        </div>
                    </a>
                    `);
                });
                console.log(paginas); */
                //paginacion(paginas);
                $('#pagination-container').pagination({
                    dataSource: response.registros,
                    ulClassName : "pagination",
                    showGoInput: true,
                    showGoButton: true,
                    goButtonText: "Ir",
                    callback: function(data, pagination) {
                        // template method of yourself
                        var html = simpleTemplate(data, selector);
                        $('.resultados').html(html);
                    }
                });

               
                
            }else{
                $('#pagination-container').pagination({
                    dataSource: [],
                    ulClassName : "pagination",
                    showGoInput: true,
                    showGoButton: true,
                    goButtonText: "Ir",
                    callback: function(data, pagination) {
                        // template method of yourself
                        var html = simpleTemplate(data, selector);
                        $('.resultados').html(html);
                    }
                });

                $(".resultados").empty().append(`
                <a href="#" class="list-group-item list-group-item-action">
                <div class="row">
                    <div class="col-12 col-md-12 text-center"><img src="./src/img/undraw_Notify_re_65on.svg" style="width:100px;"><br><br></img>Ups, no econtramos nada</div>
                </div>
                </a>`);
            }
        }
    });
}


function simpleTemplate(data, selector) {
    var html = "";
    contador =0;
    $.each(data, function(index, item){
       
        contador++;
        html += `<a href="#" class="list-group-item list-group-item-action" onclick="showOptions(${item.id}, '${item.tipo}', '${selector}')">
         <div class="row">`;
        html += `<div class="col-12 col-md-1">${contador}</div>
        <div class="col-12 col-md-1">RAY${item.id}</div>
        <div class="col-12 col-md-1">${item.Fecha}</div>
        <div class="col-12 col-md-1">${item.sucursal}</div>
        <div class="col-12 col-md-2">${item.vendedor}</div>
        <div class="col-12 col-md-3">${item.cliente}</div>
        <div class="col-12 col-md-1">${item.tipo}</div>
        <div class="col-12 col-md-1">${item.Total}</div>
        <div class="col-12 col-md-1">${item.estatus}</div>`;
        html += '</div></a>';
    });
   
    return html;
}



function showOptions(id, tipo, selector){
    if(tipo == "Normal"){
        Swal.fire({
            icon: 'info',
            title: 'Opciones',
            confirmButtonText: 'Cerrar',
            html: `<div class="container">
                        <div class="row">
                            <div class="col-12 col-md-12">
                            <ul class="list-group">
                            <li class="list-group-item list-group-item-action" style="cursor: pointer;" onclick="traerPdf(${id})">Ver nota de venta PDF</li>
                            <li class="list-group-item list-group-item-action" style="cursor: pointer;" onclick="cancelarVenta(${id},'${selector}')">Cancelar</li>
                            <li class="list-group-item list-group-item-action" style="cursor: pointer;" onclick="borrarVenta(${id}, 1, '${selector}')">Elminar</li>
                          </ul>
                            </div>
                        <div>
                   <div>
            `
        });
    }else if(tipo == "Credito"){
        Swal.fire({
            icon: 'info',
            title: 'Opciones',
            confirmButtonText: 'Cerrar',
            html: `<div class="container">
                        <div class="row">
                            <div class="col-12 col-md-12">
                            <ul class="list-group">
                            <li class="list-group-item list-group-item-action" style="cursor: pointer;" onclick="traerPdfCredito(${id})">Ver nota de credito PDF</li>
                            <li class="list-group-item list-group-item-action" style="cursor: pointer;" onclick="cancelarVenta(${id},'${selector}')">Cancelar</li>
                            <li class="list-group-item list-group-item-action" style="cursor: pointer;" onclick="borrarVenta(${id}, 2, '${selector}')">Elminar</li>
                          </ul>
                            </div>
                        <div>
                   <div>
            `
        });
    }
    
}


function traerPdf(folio){
    window.open('./modelo/ventas/generar-reporte-venta.php?id='+ folio , '_blank');
  }

  function traerPdfCredito(folio){
      window.open('./modelo/creditos/generar-reporte-credito.php?id='+ folio, '_blank');
  }

  function cancelarVenta(id, selector) { 

    Swal.fire({
        title: "Cancelar Venta",
        html: '<span>¿Estas seguro de cancelar esta venta?</span><br><br>'+
        '<div class="m-auto"><label>Motivo de la cancelación.</label><br><textarea id="motivo" name="motivo" placeholder="Escribe el motivo del porque estas cancelando esta venta." class="form-control m-auto" style="width:300px;height:80px;" ></textarea></div>',
        showCancelButton: true,
        cancelButtonText: 'Cerrar',
        cancelButtonColor: '#00e059',
        showConfirmButton: true,
        confirmButtonText: 'Cancelar', 
        cancelButtonColor:'#ff764d',
        focusConfirm: false }).then((result) => { 
        
            if(result.isConfirmed){ 
                motivo = $("#motivo").val();
               // if($("#motivo").val())
                motivo = $("#motivo").val();
                $.ajax({
                    type: "POST",
                    url: "./modelo/ventas/cancelar-venta.php",
                    data: {"id_venta": id, "motivo_cancel": motivo},
                    success: function (response) {
                        if(response == 0){

                            Swal.fire({
                                title: 'Error',
                                html: "<span>La venta no se pudo cancelar</span>",
                                icon: "error",
                                cancelButtonColor: '#00e059',
                                showConfirmButton: true,
                                confirmButtonText: 'Aceptar', 
                                cancelButtonColor:'#ff764d',
                            }).then((result) => {  
                
                                if(result.isConfirmed){
                                   
                                }});
                               if(selector == "medidas"){
                                buscarVentasPorMedida(selector);
                               }else{
                                buscarVentas(selector);
                               }

                        }else if(response == 1 || response ==11){
                            Swal.fire({
                                title: 'Venta cancelada',
                                html: "<span>La venta se a cancelado.</span>",
                                icon: "success",
                                cancelButtonColor: '#00e059',
                                showConfirmButton: true,
                                confirmButtonText: 'Aceptar', 
                                cancelButtonColor:'#ff764d',
                            }).then((result) => {  
                
                                if(result.isConfirmed){
                                  
                                }});
                              
                                if(selector == "medidas"){
                                    buscarVentasPorMedida(selector);
                                   }else{
                                    buscarVentas(selector);
                                   }

                        }else if(response == 3){
                            Swal.fire({
                                title: 'Venta ya cancelada',
                                html: "<span>Esta venta ya esta cancelada.</span>",
                                icon: "warning",
                                cancelButtonColor: '#00e059',
                                showConfirmButton: true,
                                confirmButtonText: 'Aceptar', 
                                cancelButtonColor:'#ff764d',
                            }).then((result) => {  
                
                                if(result.isConfirmed){
                                   
                                }});
                               
                                if(selector == "medidas"){
                                    buscarVentasPorMedida(selector);
                                   }else{
                                    buscarVentas(selector);
                                   }
                        }
                      
                    }
                });
                

            }

        });

   }

   function borrarVenta(id, tipo, selector) {

    Swal.fire({
        title: "Eliminar Venta",
        html: '<span>¿Estas seguro de eliminar esta venta?</span>',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        cancelButtonColor: '#00e059',
        showConfirmButton: true,
        confirmButtonText: 'Borrar', 
        cancelButtonColor:'#ff764d',
        focusConfirm: false }).then((result) => { 
        
            if(result.isConfirmed){    

    $.ajax({
        type: "post",
        url: "./modelo/ventas/borraVentaHistorial.php",
        data: {"folio": id, "tipo": tipo},
        success: function (response) {
           if (response==1) {
              
            Swal.fire({
                title: 'Venta eliminada',
                html: "<span>La venta se elimino con exito</span>",
                icon: "success",
                cancelButtonColor: '#00e059',
                showConfirmButton: true,
                confirmButtonText: 'Aceptar', 
                cancelButtonColor:'#ff764d',
            }).then((result) => {  
                if(selector == "medidas"){
                    buscarVentasPorMedida(selector);
                   }else{
                    buscarVentas(selector);
                   }
            });

           
           }else{
            Swal.fire({
                title: 'Venta no eliminada',
                html: "<span>La venta no se pudo eliminar, dedido a algun error inesperado</span>",
                icon: "warning",
                cancelButtonColor: '#00e059',
                showConfirmButton: true,
                confirmButtonText: 'Aceptar', 
                cancelButtonColor:'#ff764d',
                showDenyButton: true,
                
            }).then((result) => {  
                if(selector == "medidas"){
                    buscarVentasPorMedida(selector);
                   }else{
                    buscarVentas(selector);
                   }
            });
           }
        }
    });
}

        });
  }


  function buscarVentasPorMedida(selector) {
    let ancho =  $("#ancho").val();
    let proporcion =  $("#proporcion").val();
    let diametro =  $("#diametro").val();

    if($("#ancho").val() == "No aplica"){

        toastr.error('Selecciona un ancho a buscar.', 'Elige un ancho'); 
    }else if($("#proporcion").val() == "No aplica"){
    
        toastr.error('Selecciona un alto o perfil a buscar.', 'Elige un alto'); 
    }else if($("#diametro").val() == "No aplica"){
    
        toastr.error('Selecciona un diametro del rin a buscar.', 'Elige un diametro'); 
    }else{
    
        $(".resultados").empty().append(`
        <a href="#" class="list-group-item list-group-item-action">
        <div class="row">
            <div class="col-12 col-md-12 text-center"><img src="./src/img/preload.gif" style="width:70px;"><br></img>Buscando...</div>
        </div>
        </a>`);
         $.ajax({
             type: "POST",
             url: "./modelo/busqueda/busqueda-avanzada-por-medida.php",
             data: {"ancho": ancho, "proporcion": proporcion, "diametro": diametro},
             dataType: "JSON",
             success: function (response) {
                 $(".resultados").empty();
                 if(response.status !== false){
                     let total_reg = response.total_registros;
                     let paginas_sin_redondear = total_reg / 5;
                     let paginas = Math.ceil(paginas_sin_redondear);
                     
                    /*  contador =0;
                     response.registros.forEach(element => {
                         contador++;
                         $(".resultados").append(`
                         <a href="#" class="list-group-item list-group-item-action">
                             <div class="row">
                                 <div class="col-12 col-md-1">${contador}</div>
                                 <div class="col-12 col-md-1">RAY${element.id}</div>
                                 <div class="col-12 col-md-1">${element.Fecha}</div>
                                 <div class="col-12 col-md-1">${element.sucursal}</div>
                                 <div class="col-12 col-md-1">${element.vendedor}</div>
                                 <div class="col-12 col-md-3">${element.cliente}</div>
                                 <div class="col-12 col-md-1">${element.tipo}</div>
                                 <div class="col-12 col-md-1">${element.Total}</div>
                                 <div class="col-12 col-md-1">${element.estatus}</div>
                                 <div class="col-12 col-md-1"></div>
                             </div>
                         </a>
                         `);
                     });
                     console.log(paginas); */
                     //paginacion(paginas);
                     $('#pagination-container').pagination({
                         dataSource: response.registros,
                         ulClassName : "pagination",
                         showGoInput: true,
                         showGoButton: true,
                         goButtonText: "Ir",
                         callback: function(data, pagination) {
                             // template method of yourself
                             var html = simpleTemplate(data, selector);
                             $('.resultados').html(html);
                         }
                     });
     
                    
                     
                 }else{
                    $('#pagination-container').pagination({
                        dataSource: [],
                        ulClassName : "pagination",
                        showGoInput: true,
                        showGoButton: true,
                        goButtonText: "Ir",
                        callback: function(data, pagination) {
                            // template method of yourself
                            var html = simpleTemplate(data, selector);
                            $('.resultados').html(html);
                        }
                    });

                     $(".resultados").empty().append(`
                     <a href="#" class="list-group-item list-group-item-action">
                     <div class="row">
                         <div class="col-12 col-md-12 text-center"><img src="./src/img/undraw_Notify_re_65on.svg" style="width:100px;"><br><br></img>Ups, no econtramos nada</div>
                     </div>
                     </a>`);
                 }
             }
         });

    }; 
  };

  function ocultarSidebar(){
    let sesion = $("#emp-title").attr("sesion_rol");
    if(sesion == 4){
      $(".rol-4").addClass("d-none");
  
    }
  };