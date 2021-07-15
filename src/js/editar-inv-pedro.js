
function editarInvPedro(id){
    //codigo_llanta = $(id).attr("id");
  
   // alert("El codigo de la llanta es " + codigo_llanta);

   $.ajax({
       type: "POST",
       url: "./modelo/editar-llanta-pedro.php", 
       data: {codigo: id},
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
               '<div class="col-12">'+
               '<div class="form-group">'+
                    '<label>Stock</label>'+
                    '<input type="number" id="stock-ind" class="form-control" value="'+ response.stock +'">' + 
                    '<div class="invalid-feedback">No se pueden ingresar numeros negativos</div>'+
                    
                  '</div>'+
                  '</div>'+ 
               '</div>'+
               '<div id="alerta"></div>'+
        
           
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
                return new Promise(function (resolve, reject) {
                    if(value_stock < 0){

                         reject('No puedes introducir cantidades negativas.');
                    }else if(value_stock == "" || value_stock == null){
                        reject('No puedes introducir cantidades vacias.');
                    }else{
                        resolve();
                    }
                  }).catch(err => {
                    $("#alerta").append('<div class="alert alert-warning" role="alert">'+ err +'</div>');
                   // alert(`error: ${err}`)
                    return false
                });   
                
             },
            
            //Si el resultado es OK tons:
          }).then((result) => {  

            if(result.isConfirmed){

                
                stock_para_editar       = $("#stock-ind").val();
              

                

                $.ajax({
                    type: "POST",
                    url: "./modelo/actualizar-stock-inv-pedro.php",
                    data: {codigo      : id,
                           stock       : stock_para_editar
                         },
                    dataType: "json",
                    success: function (response) {
                      
                       if (response) {
                        Swal.fire(
                            "¡Correcto!",
                            "Bien, " +response.llantas_dif,
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
        url: "./modelo/borrar-llanta-inv-pedro.php",
        data: {codigo: id},
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