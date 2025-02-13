
function editarRegistro(id){
    //codigo_llanta = $(id).attr("id");
  
   
   $.ajax({
       type: "POST",
       url: "./modelo/catalogo/editar-llanta-inv-total.php",
       data: {codigo: id},
       dataType: "json",
       success: function (response) {
           
            if(response.estatus){

                let datos = response.datos;
                Swal.fire({
                    title: "Editar llanta",
                    width: '700px',
                    html: '<form class="mt-4" id="agregar-llanta-inv-total">'+
                
                    '<div class="row">'+
        
                       '<div class="col-12">'+
                       '<div class="form-group">'+
                       '<label><b>Marca:</b></label></br>'+
                       '<select class="form-control" id="marca" name="marca"></select>'+
                          '</div>'+
                          '</div>'+
                       '</div>'+
                
                    '<div class="row">'+
                        '<div class="col-4">'+
                        '<div class="form-group">'+
                        '<label for="ancho"><b>Ancho:</b></label></br>'+
                        '<input type="number" class="form-control" id="ancho" value="'+ datos.ancho+'" name="ancho" placeholder="Ancho" autocomplete="off" step="0.1">'+
                
                
                   ' </div>'+
                    '</div>'+
                    
                    
                   '<div class="col-4">'+
                    '<div class="form-group">'+
                    '<label><b>Alto:</b></label></br>'+
                    '<input type="number" name="alto" id="alto" value="'+ datos.alto+'" class="form-control" placeholder="Proporcion" step="0.1">'+
                    '</div>'+
                    '</div>'+
                
                    
                        '<div class="col-4">'+
                        '<div class="form-group">'+
                        '<label><b>Rin</b></label>'+
                        '<input type="number" class="form-control" value="'+ datos.rin+'" id="rin" name="rin" placeholder="Diametro" step="0.1">'+
                    '</div>'+
                        '</div>'+
                
                       
                
                        '<div class="col-6">'+
                        '<div class="form-group">'+
                        '<label><b>Modelo</b></label>'+
                        '<input type="text" class="form-control" value="'+ datos.modelo+'" id="modelo" name="modelo" placeholder="Modelo">'+
                        '</div>'+
                        '</div>'+
                
                       
                    '<div class="col-6">'+
                        '<div class="form-group">'+
                            '<label><b>Fecha</b></label>'+
                            '<input type="date" class="form-control" value="'+ datos.fecha +'" name="fecha" id="fecha" >'+
                        '</div>'+
                    '</div>'+
                    
                    
                   
                       
                
                
                    '</div>'+
                
                    '<div class="row" id="precios_area">'+
                        '<div class="col-3">'+
                            '<div class="form-group">'+
                                '<label><b>Costo</b></label>'+
                                '<input type="number" class="form-control" value="'+ datos.costo+'" id="costo" name="costo" placeholder="0.00">'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-3">'+
                            '<div class="form-group">'+
                                '<label><b>Precio lista</b></label>'+
                                '<input type="number" class="form-control" value="'+ datos.precio_lista+'" id="precio_lista" name="costo" placeholder="0.00">'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-3">'+
                        '<div class="form-group">'+
                        '<label><b>Precio desc.</b></label>'+
                        '<input type="number" class="form-control" value="'+ datos.precio+'" name="precio" id="precio" placeholder="0.00">'+
                    '</div>'+
                '</div>'+
                '<div class="col-3">'+
                        '<div class="form-group">'+
                        '<label><b>Mayorista</b></label>'+
                        '<input type="number" class="form-control" value="'+ datos.mayoreo+'" name="mayorista" id="mayorista" placeholder="0.00">'+
                    '</div>'+
                '</div>'+
                        '</div>'+
                    '</div>'+
                
                    '<div class="row  mt-1">'+
                    '<div class="col-12">'+
                    '<div class="form-group" id="area-solucion">'+
                    '<label><b>Descripción</b></label>'+
                    '<textarea class="form-control" style="height:100px" name="descripcion"  id="descripcion" form="formulario-editar-registro" placeholder="Escriba la descripcion del producto">'+ datos.descripcion +'</textarea>'+
                    '</div>'+
                    '</div>'+
                    '</div>'+ 
                            '</div>'+
                '</form>',
                    showCancelButton: true,
                    cancelButtonText: 'Edición avanzada',
                    cancelButtonColor: '#00e059',
                    showConfirmButton: true,
                    confirmButtonText: 'Actualizar', 
                    cancelButtonColor:'#ff764d',
                    focusConfirm: false,
                    iconColor : "#36b9cc",
                    didOpen: function () {
                     
                        $(document).ready(function() { 
                            
                
                            $('#marca').select2({
                                placeholder: datos.marca,
                                theme: "bootstrap",
                                minimumInputLength: 1,
                                ajax: {
                                    url: "./modelo/traer-marca.php",
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
                
                                  templateResult: formatRepo,
                                  templateSelection: formatRepoSelection
                            });
                
                
                            function formatRepo (repo) {
                                
                              if (repo.loading) {
                                return repo.text;
                              }
                              
                                var $container = $(
                                    "<div class='select2-result-repository clearfix'>" +
                                    "<div class='select2-contenedor-principal'>" +
                                    "<div class='select2-result-repository__avatar'><img style='width: 50px; border-radius: 6px' src='./src/img/logos/" + repo.imagen + ".jpg' /></div>" +
                                      "<div class='select2-contenedor'>" +
                                      "<div class='select2_marca' marca='"+ repo.imagen +"'></div>" +
                                      "</div>" +
                                      "</div>" +
                                      "</div>" 
                                );
                              
                                $container.find(".select2_marca").text(repo.nombre);
                
                                
                              
                                return $container;
                              }
                
                             
                
                              function formatRepoSelection (repo) {
                                return repo.imagen || repo.text;
                              }
                
                              let rol = $('#id_rol').attr('role');
                              if(rol != 1 && rol != 4){
                                $("#precio").prop('disabled', true);
                                $("#costo").prop('disabled', true);
                                $("#mayorista").prop('disabled', true);
                              }
                        });
                    } 
                    
                    
                    //Si el resultado es OK tons:
                  }).then((result) => {  
        
                    if(result.isConfirmed){
        
                        marca       = $("#select2-marca-container").text();
                        ancho       = $("#ancho").val();
                        alto        = $("#alto").val();
                        rin         = $("#rin").val();
                        modelo      = $("#modelo").val();
                        fecha       = $("#fecha").val();
                        costo       = $("#costo").val();
                        precio_lista      = $("#precio_lista").val();
                        precio      = $("#precio").val();
                        mayorista   = $("#mayorista").val();
                        descripcion = $("#descripcion").val();
        
                        ancho = parseFloat(ancho);
                        alto = parseFloat(alto);
                        rin = parseFloat(rin);
        
        
                        $.ajax({
                            type: "POST",
                            url: "./modelo/actualizar-llanta-inv-total.php",
                            data: {codigo      : datos.id, 
                                   marca       : marca,
                                   ancho       : ancho,
                                   alto        : alto,
                                   rin         : rin,
                                   modelo      : modelo,
                                   fecha       : fecha,
                                   costo       : costo,
                                   precio_lista,
                                   precio      : precio,
                                   mayorista   : mayorista,
                                   descripcion : descripcion},
                            dataType: "json",
                            success: function (response) {
                              
                               if (response == 1) {
                                Swal.fire(
                                    "¡Correcto!",
                                    "Se actualizo llanta la llanta",
                                    "success"
                                    ).then((result) => { 
                                        if(result.isConfirmed){
                                            table.ajax.reload(null,false);
                                        }
                                        table.ajax.reload(null,false);
                                        })
                               }else{
                                Swal.fire(
                                    "¡Error!",
                                    "Ocurrio un error inesperado",
                                    "error"
                                    )
                               }
                               
                            }
                        });
                        
                        
                    }else if(result.isDismissed && result.dismiss == 'cancel'){
                
                        window.location.href = './editar-llanta.php?id_llanta=' + id + '&id=0&nav=existencia';
                    }
                })
            }else{
                Swal.fire({
                    icon: 'error',
                    title: response.mensaje
                })
            }
           

       }
   });
};


function borrarRegistro(id) { 
    Swal.fire({
        icon: 'warning',
        title: "¿Estas seguro de eliminar esta llanta?",
        html: '<span>Al eliminar esta llanta tambien desaparecera del inventario fisico en tus sucursales donde la tengas agregada.</span>',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        cancelButtonColor: '#00e059',
        showConfirmButton: true,
        confirmButtonText: 'Borrar', 
        cancelButtonColor:'#ff764d',
        focusConfirm: false }).then((result) => { 
            if(result.isConfirmed){
    $.ajax({
        type: "POST",
        url: "./modelo/borrar-llanta-inv-total.php",
        data: {codigo: id},
        success: function (response) {

            
           
            if (response == 1) {
             Swal.fire(
                 "¡Correcto!",
                 "Se elimino la llanta del inventario",
                 "success"
                 ).then((result) =>{

                    if(result.isConfirmed){
                        table.ajax.reload(null,false);
                    }
                    table.ajax.reload(null,false);
                    });
                 
            }else{
             Swal.fire(
                 "¡Error!",
                 "Ocurrio un error inesperado",
                 "error"
                 );
                 table.ajax.reload(null,false);
            }
        
        }
    }); 
    }
 });
 }

 function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}