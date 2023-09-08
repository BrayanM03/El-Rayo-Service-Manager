
function editarStock(id, id_sucursal){
    //codigo_llanta = $(id).attr("id");
  
   // alert("El codigo de la llanta es " + codigo_llanta);

   $.ajax({
       type: "POST",
       url: "./modelo/inventarios/traer-stock.php", 
       data: {codigo: id, sucursal_id: id_sucursal},
       dataType: "json",
       success: function (response) {
           
           Swal.fire({
            title: "Editar stock",
            didOpen: function () {  

                $("#stock-ind").keyup(function () { 
                    value_stock = $("#stock-ind").val();
                    if(value_stock < 0){
                        flag= $("#stock-ind").hasClass("is-valid");
                        if(flag){
                            $("#stock-ind").removeClass("is-valid")
                            $("#stock-ind").addClass("is-invalid");
                        }else{
                            $("#stock-ind").addClass("is-invalid");
                        }
                       
                    }else if(value_stock == "" || value_stock == null){
                        flag= $("#stock-ind").hasClass("is-valid");
                        if(flag){
                            $("#stock-ind").removeClass("is-valid")
                            $("#stock-ind").addClass("is-invalid");
                            $(".invalid-feedback").text("No se admiten valores nulos o vacios");
                        }else{
                            $("#stock-ind").addClass("is-invalid");
                            
                            $(".invalid-feedback").text("No se admiten valores nulos o vacios");
                        }
                    
                    }else{
                        flag= $("#stock-ind").hasClass("is-invalid");
                        if(flag){
                            $("#stock-ind").removeClass("is-invalid")
                            $("#stock-ind").addClass("is-valid");
                        }else{
                            $("#stock-ind").addClass("is-valid");
                        }
                        
                    }
                 });

                 $("#tipo").on("change", function() {
                    if($(this).val() == "aumentar"){
                        $("#label-movi").text("Ingresa las llantas a agregar");
                    }else if($(this).val() == "reducir"){
                        $("#label-movi").text("Ingresa las llantas a retirar");    
                    }
                 });


                 $('#proveedor').empty();
                 $('#proveedor').append(`
                    <option value="0"></option>
                 `);
                 response.proveedores.forEach(element => {
                    $('#proveedor').append(`
                        <option value="${element.id}">${element.nombre}</option>
                    `);   
                 });

                 $('#proveedor').selectpicker('refresh');
                 
            },
            html: '<form class="mt-4" id="agregar-llanta-inv-total">'+
        
              '<div class="row">'+
               '<div class="col-12">'+
               '<div class="form-group">'+
                    '<span> Llanta codigo '+ response.id +'</span>'+
                  '</div>'+
                  '</div>'+ 
               '</div>'+

           
               '<div class="row">'+
                    '<div class="col-6">'+
                        '<label>Stock actual</label>'+
                        '<input type="number" id="stock-act" class="form-control" value="'+ response.stock +'" disabled>' + 
                    '</div>'+
                    '<div class="col-6">'+
                        '<label>Tipo de operación</label>'+
                        '<select class="form-control mb-2" id="tipo">'+
                            '<option value="aumentar">Agregar stock</option>'+
                            '<option value="reducir">Reducir stock</option>'+
                        '</select>'+ 
                    '</div>'+
                '</div>'+
                
                '<div class="row mt-3">'+ 
                    '<div class="col-12">'+
                        '<label id="label-movi" placeholder="0.00" type="number">Ingresa las llantas a agregar</label>'+
                        '<input type="number" id="stock-ind" placeholder="0" class="form-control">' +
                    '</div>'+
                '</div>'+

                        
                '<div class="mt-4 invalid-feedback">No se pueden ingresar numeros negativos</div>'+
                    
                     
                
               '<div id="alerta" class="mt-4"></div>'+
        
           
        '</form>',
            showCancelButton: true,
            cancelButtonText: 'Cerrar',
            cancelButtonColor: '#00e059',
            showConfirmButton: true,
            confirmButtonText: 'Actualizar', 
            cancelButtonColor:'#ff764d',
            focusConfirm: false,
            iconColor : "#36b9cc",
            preConfirm: function () { 
                value_stock = $("#stock-ind").val();
                proveedor = $("#proveedor").val();
                folio_factura = $("#folio-factura").val();
                return new Promise(function (resolve, reject) {
                    if(value_stock < 0){

                         reject('No puedes introducir cantidades negativas.');
                    }else if(value_stock == "" || value_stock == null){
                        reject('No puedes introducir cantidades vacias.');
                    }else{
                        resolve();
                    }
                  }).catch(err => {
                    $("#alerta").empty().append('<div class="alert alert-warning" role="alert">'+ err +'</div>');
                   // alert(`error: ${err}`)
                    return false
                });   
                
             },
            
            //Si el resultado es OK tons:
          }).then((result) => {  

            if(result.isConfirmed){

                stock_actual       = $("#stock-act").val();
                stock_para_editar       = $("#stock-ind").val();
                type = $("#tipo").val();
                
                $.ajax({
                    type: "POST",
                    url: "./modelo/inventarios/actualizar-stock.php",
                    data: {codigo      : id,
                           stock       : stock_para_editar,
                           stock_actual : stock_actual,
                           sucursal_id : id_sucursal,
                           tipo : type,
                           proveedor,
                           folio_factura
                         },
                    dataType: "json",
                    success: function (response) {
                      
                       if (response) {
                        Swal.fire(
                            "¡Correcto!",
                            "Bien, " +response.llantas_agregadas,
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
                            "El stock que resta sera menor 0",
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


function borrarRegistro(id, sucursal_id) { 
    Swal.fire({
        title: "Eliminar llanta",
        html: '<span>¿Estas seguro de eliminar esta llanta?</span>',
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
        url: "./modelo/inventarios/borrar-llanta-inventario.php",
        data: {codigo: id, sucursal_id : sucursal_id},
        success: function (response) {

            
           
            if (response == 1) {
             Swal.fire(
                 "¡Correcto!",
                 "Se elimino la llanta del inventario",
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
 });
 }