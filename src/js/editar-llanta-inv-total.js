
function editarRegistro(id){
    //codigo_llanta = $(id).attr("id");
  
   // alert("El codigo de la llanta es " + codigo_llanta);

   $.ajax({
       type: "POST",
       url: "./modelo/editar-llanta-inv-total.php",
       data: {codigo: id},
       dataType: "json",
       success: function (response) {
           
           Swal.fire({
            title: "Editar llanta",
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
                '<input type="number" class="form-control" id="ancho" value="'+ response.ancho+'" name="ancho" placeholder="Ancho" autocomplete="off">'+
        
        
           ' </div>'+
            '</div>'+
            
            
           '<div class="col-4">'+
            '<div class="form-group">'+
            '<label><b>Alto:</b></label></br>'+
            '<input type="number" name="alto" id="alto" value="'+ response.alto+'" class="form-control" placeholder="Proporcion">'+
            '</div>'+
            '</div>'+
        
            
                '<div class="col-4">'+
                '<div class="form-group">'+
                '<label><b>Rin</b></label>'+
                '<input type="text" class="form-control" value="'+ response.rin+'" id="rin" name="rin" placeholder="Diametro">'+
            '</div>'+
                '</div>'+
        
               
        
                '<div class="col-6">'+
                '<div class="form-group">'+
                '<label><b>Modelo</b></label>'+
                '<input type="text" class="form-control" value="'+ response.modelo+'" id="modelo" name="modelo" placeholder="Modelo">'+
                '</div>'+
                '</div>'+
        
               
            '<div class="col-6">'+
                '<div class="form-group">'+
                    '<label><b>Fecha</b></label>'+
                    '<input type="date" class="form-control" value="'+ response.fecha +'" name="fecha" id="fecha" >'+
                '</div>'+
            '</div>'+
            
            
           
               
        
        
            '</div>'+
        
            '<div class="row">'+
                '<div class="col-4">'+
                    '<div class="form-group">'+
                        '<label><b>Costo</b></label>'+
                        '<input type="number" class="form-control" value="'+ response.costo+'" id="costo" name="costo" placeholder="0.00">'+
                    '</div>'+
                '</div>'+
                '<div class="col-4">'+
                '<div class="form-group">'+
                '<label><b>Precio</b></label>'+
                '<input type="number" class="form-control" value="'+ response.precio+'" name="precio" id="precio" placeholder="0.00">'+
            '</div>'+
        '</div>'+
        '<div class="col-4">'+
                '<div class="form-group">'+
                '<label><b>Mayorista</b></label>'+
                '<input type="number" class="form-control" value="'+ response.mayoreo+'" name="mayorista" id="mayorista" placeholder="0.00">'+
            '</div>'+
        '</div>'+
                '</div>'+
            '</div>'+
        
            '<div class="row  mt-1">'+
            '<div class="col-12">'+
            '<div class="form-group" id="area-solucion">'+
            '<label><b>Descripción</b></label>'+
            '<textarea class="form-control" style="height:100px" name="descripcion"  id="descripcion" form="formulario-editar-registro" placeholder="Escriba la descripcion del producto">'+ response.descripcion +'</textarea>'+
            '</div>'+
            '</div>'+
            '</div>'+
                    '</div>'+
        '</form>',
            showCancelButton: true,
            cancelButtonText: 'Cerrar',
            cancelButtonColor: '#00e059',
            showConfirmButton: true,
            confirmButtonText: 'Actualizar', 
            cancelButtonColor:'#ff764d',
            focusConfirm: false,
            iconColor : "#36b9cc",
            didOpen: function () {
             
                $(document).ready(function() { 
                    
        
                    $('#marca').select2({
                        placeholder: response.marca,
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
                precio      = $("#precio").val();
                mayorista   = $("#mayorista").val();
                descripcion = $("#descripcion").val();

                

                $.ajax({
                    type: "POST",
                    url: "./modelo/actualizar-llanta-inv-total.php",
                    data: {codigo      : response.id,
                           marca       : marca,
                           ancho       : ancho,
                           alto        : alto,
                           rin         : rin,
                           modelo      : modelo,
                           fecha       : fecha,
                           costo       : costo,
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
                
                
            }
        })

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