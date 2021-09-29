//Traer información 

$.ajax({
    type: "POST",
    url: "./modelo/cortes/ventas-sucursal-hoy.php",
    data: "data",
    dataType: "JSON",
    success: function (response) {
      $("#ganancia-sendero").text(response.ganancia_sendero);
      $("#ganancia-pedro").text(response.ganancia_pedro); 
     
    }
}); 

comprobarCortes();


function realizarCorte(id_sucursal){

  $comprobacion_corte = "";

  //hacer validación 
  $.ajax({
    type: "POST",
    url: "./modelo/cortes/validar-btn.php",
    data: {"sucursal": id_sucursal},

    success: function (response) {
      if (response ==1) {
        Swal.fire({
          title: "El corte ya fue realizado",
          showCancelButton: true,
          showConfirmButton: true,
              confirmButtonText: 'Hacer apertura',
              cancelButtonText: 'Cerrar', 
              confirmButtonColor: '#00e059',
              cancelButtonColor:'#ff764d',
              focusConfirm: false,
              iconColor : "#36b9cc",
              html:'Realiza una apertura de sucursal para inciar el corte.',
              }).then((result)=>{
                if (result.isConfirmed) {
                  
                  Swal.fire({
                    title: "Apertura de sucursal",
                    showCancelButton: true,
                        cancelButtonText: 'Cerrar',
                        cancelButtonColor: '#00e059',
                        showConfirmButton: true,
                        confirmButtonText: 'Realizar', 
                        cancelButtonColor:'#ff764d',
                        focusConfirm: false,
                        iconColor : "#36b9cc",
                        html:'<span>Con cuanto dinero quieres aperturar la sucursal</span></br>'+
                        '<input class="form-control mt-3" type="number" id="apertura">'
                        }).then((result)=>{
                          if (result.isConfirmed) {
                            apertura = $("#apertura").val();

                            $.ajax({
                              type: "POST",
                              url: "./modelo/cortes/insertar-apertura.php",
                              data: {"sucursal": id_sucursal, "apertura": apertura},
                              
                              success: function (response) {
                                if (response ==1) {
                                  Swal.fire({
                                    title: "Se realizo apertura",
                                    icon: "success",
                                    showCancelButton: false,
                                        cancelButtonText: 'Cerrar',
                                        cancelButtonColor: '#00e059',
                                        showConfirmButton: true,
                                        confirmButtonText: 'Aceptar', 
                                        cancelButtonColor:'#ff764d',
                                        focusConfirm: false,
                                        iconColor : "#36b9cc",
                                        html:'<span>La apertura se realizo con exito.</span>',
                                        })
                                  


                                  switch (id_sucursal) {
                                    case "Pedro":
                                      $("#corte-btn-pedro").removeClass("btn-success");
                                      $("#corte-btn-pedro").addClass("btn-primary").text("Realizar corte");
                                      break;
        
                                      case "Sendero":
                                        $("#corte-btn-sendero").removeClass("btn-success");
                                        $("#corte-btn-sendero").addClass("btn-primary").text("Realizar corte");
                                        break;
                                  
                                    default:
                                      break;
                                  }
                                }
                              }
                            });
                          }
                        });
                }
              });
      }else{


        Swal.fire({
          title: "¿Realizar corte?",
          showCancelButton: true,
              cancelButtonText: 'Cerrar',
              cancelButtonColor: '#00e059',
              showConfirmButton: true,
              confirmButtonText: 'Realizar', 
              cancelButtonColor:'#ff764d',
              focusConfirm: false,
              iconColor : "#36b9cc",
              html:'<div class="m-auto"><label>El corte establecera en $0.00 la apertura de las cajas</label></div>',
              }).then((result) => { 
                if (result.isConfirmed) {
                  $.ajax({
                    type: "POST",
                    url: "./modelo/cortes/realizar-corte.php",
                    data: {"sucursal_id": id_sucursal},
                    dataType: "JSON",
                    success: function (response) {
        
                     
                        Swal.fire({
                          title: "Corte realizado",
                          icon: "success",
                          showCancelButton: true,
                              cancelButtonText: 'Cerrar',
                              cancelButtonColor: '#00e059',
                              showConfirmButton: true,
                              confirmButtonText: 'Ver', 
                              cancelButtonColor:'#ff764d',
                              focusConfirm: false,
                              iconColor : "#36b9cc",
                              html:'<span>El corte se realizó con exito, todo en orden</span>',
                              }).then((resultado) => { 
                      
                              if (resultado.isConfirmed || resultado.isDenied) {
                                switch (id_sucursal) {
                                  case "Pedro":
                                    $("#corte-btn-pedro").removeClass("btn-primary");
                                    $("#corte-btn-pedro").addClass("btn-success").text("Realizado");
                                    break;
      
                                    case "Sendero":
                                      $("#corte-btn-sendero").removeClass("btn-primary");
                                      $("#corte-btn-sendero").addClass("btn-success").text("Realizado");
                                      break;
                                
                                  default:
                                    break;
                                }
                                
                              }else{
      
                                switch (id_sucursal) {
                                  case "Pedro":
                                    $("#corte-btn-pedro").removeClass("btn-primary");
                                    $("#corte-btn-pedro").addClass("btn-success").text("Realizado");
                                    break;
      
                                    case "Sendero":
                                      $("#corte-btn-sendero").removeClass("btn-primary");
                                      $("#corte-btn-sendero").addClass("btn-success").text("Realizado");
                                      break;
                                
                                  default:
                                    break;
                                }
                                
                              }
                           
                      
                              });
                      
                      
                    }
                  });
                }
                
              });


      }
    }
  });



  

}


function comprobarCortes(){

  $.ajax({
    type: "POST",
    url: "./modelo/cortes/comprobar-cortes.php",
    data: "data",
    dataType: "JSON",
    success: function (response) {
      
        response.forEach(Element => {
           id = Element.id;
           corte = Element.corte;

           if (id ==1) {
             if (corte ==1) {
            $("#corte-btn-pedro").removeClass("btn-primary");
            $("#corte-btn-pedro").addClass("btn-success");
            $("#corte-btn-pedro").text("Realizado");
            
             }
           }else if(id = 2){
             if (corte == 1) {
               
            $("#corte-btn-sendero").removeClass("btn-primary");
            $("#corte-btn-sendero").addClass("btn-success");
            $("#corte-btn-sendero").text("Realizado");
               
             }
           }
        });

    }
  });

}


function resumenCorte(sucu){

  window.open('./resumen-corte.php?sucursal='+ sucu);

}