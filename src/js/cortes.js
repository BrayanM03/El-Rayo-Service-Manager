//Traer información 

$.ajax({
    type: "POST",
    url: "./modelo/cortes/ganancias_hoy.php",
    data: "data", 
    dataType: "JSON",
    success: function (response) {
      response.forEach(element => {
        let id_sucursal = element.id;
        let venta = element.venta_hoy;
        let ganancia = element.ganancia_hoy;

        venta = round(venta);
        ganancia = round(ganancia);
       
        $("#ventas_"+id_sucursal).text(venta);
        $("#ganancia_"+id_sucursal).text(ganancia);;
        
      });
     
    }
}); 

function round(num) {
  var m = Number((Math.abs(num) * 100).toPrecision(15));
  return Math.round(m) / 100 * Math.sign(num);
}


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
              html:'Realiza una apertura de sucursal para iniciar el corte.',
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
                                        });

                                       
                               $("#btn_estatus_corte_"+id_sucursal).removeClass().addClass("btn btn-primary m-1").empty().append("Realizar corte");


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
              html:'<div class="m-auto"><label>El corte se guardara en el historial de cortes</label></div>',
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
                      
                              if (resultado.isConfirmed) {
                                
                                $("#btn_estatus_corte_"+id_sucursal).removeClass().addClass("btn btn-success m-1").empty().append("Realizado");
                                window.location.href = `resumen-corte.php?id=0&nav=resumen_corte&sucursal=${id_sucursal}`;

                              }else if(resultado.isDenied){
                                $("#btn_estatus_corte_"+id_sucursal).removeClass().addClass("btn btn-success m-1").empty().append("Realizado");
                               
                              }else{
                                
                                $("#btn_estatus_corte_"+id_sucursal).removeClass().addClass("btn btn-success m-1").empty().append("Realizado");
                                
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

           console.log("Suc " + id + " corte: " + corte);
           if(corte == 2){
            $("#btn_estatus_corte_"+id).removeClass().addClass("btn btn-primary m-1").empty().append("Realizar corte");
           }else{
           $("#btn_estatus_corte_"+id).removeClass().addClass("btn btn-success m-1").empty().append("Realizado");
           }
           
        });

    }
  });

}


function resumenCorte(sucu){

  window.open('./resumen-corte.php?id=0&nav=resumen_corte&sucursal='+ sucu);

}