
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
                    '<label>Stok</label>'+
                    '<input type="number" id="stock-ind" class="form-control" value="'+ response.stock +'">' + 
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
                      
                       if (response == 1) {
                        Swal.fire(
                            "¡Correcto!",
                            "Se actualizo stock de la llanta",
                            "success"
                            ).then((result) =>{

                            if(result.isConfirmed){
                                table.ajax.reload();
                            }
                            table.ajax.reload();
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
                table.ajax.reload();
            }
            table.ajax.reload();
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