


$(document).ready(function() {
  

 
  columnDefs = [{
    title: "Codigo"
  }, {
    title: "Descripción"
  }, {
    title: "Modelo"
  },  {
    title: "Cantidad"
  }, {
    title: "Precio"
  }, {
    title: "Importe"
  }, {
    title: "Accion"
  }];
  
  table = $('#pre-venta').DataTable({

    columns: columnDefs,
    paging: false,
    searching: false,
    scrollY: "350px",
    info: false,
    //responsive: true,
   
  
    
  });

 


  table.on( 'click', '.borrar-articulo',  function () {
    hijosTabla = $("#pre-venta tbody").children();
    console.log(hijosTabla);

    table.row( $(this).parents('tr') ).remove().draw(false);
    toastr.success('Producto borrado con exito', 'Correcto' );
  } );

} );

contador = 2;

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

function agregarInfo(){
    //Funcion que se encargara de mover informacion del producto a una tabla para luego ser procesada como una venta

    valorCant0 = $('#cantidad').val();
    valorCant = parseInt(valorCant0);
    valitationQuanty = $('#cantidad')[0].checkValidity();
    valitationdescription = $("#description")[0].checkValidity();
    valitationModel = $("#modelo")[0].checkValidity();
    valitationPrice = $("#precio").val();
    sucursalVal = $("#sucursal").val();

    stockLlanta0 = $("#agregar-producto").attr("stock");
    stockLlanta = parseInt(stockLlanta0);
   
    //console.log(valitationQuanty);
    if (valitationQuanty == false ) {

      toastr.warning('Necesita especificar una cantidad', 'Alerta' );

      
    }else if(valorCant <= 0){

      toastr.warning('La cantidad no puede ser 0 o menor', 'Alerta' );

    }else if(valorCant > stockLlanta){

      toastr.error('La cantidad es mayor que el stock', 'Error' );

    }else if(sucursalVal == null){

      toastr.error('Debe colocar una sucursal', 'Error' );

    }else if(valitationdescription == false){

      toastr.warning('La descripción no puede ir vacia', 'Alerta' );

    }else if(valitationModel == false ){
      toastr.warning('Especifique un modelo', 'Alerta');
      
    } else if(valitationPrice == 0 ){
      toastr.warning('Anote un precio', 'Alerta');
      
    }else if(valitationPrice == 0 ){
      toastr.warning('Anote un precio', 'Alerta');
      
    }else {
     

      idBotonLLanta    =   $("#agregar-producto").attr("codigo");
      descripcion      =   $("#description").val();
      modelo           =   $("#modelo").val();
      marca            =   $(".logo-marca").attr("marca");
      cantidad         =   $("#cantidad").val();
      precio           =   $("#precio").val();
      sucursal         =   $("select[id = sucursal] option:selected").text();
      subtotal         =   precio * cantidad;
      botones = "<div class='btn btn-danger borrar-articulo' style='margin-right:5px;'><i class='fas fa-trash'></i></div>";

      

  
     


  
     //Esta es la funcion llanta agregada y es para agregar la llanta si es una llanta no repetida y si el contador de llantas repetidas esta en 0
      function llantaAgregada() {
         fila = table.row.add( [
              idBotonLLanta,
              descripcion, 
              modelo, 
              cantidad, 
              precio,  
              subtotal,
              botones
          ] ).draw(false); 

          total = $("#total").val();
          newTotal = subtotal + parseInt(total);
          $("#total").val(newTotal);
          
          toastr.success('Producto agregado correctamente', 'Correcto' );

      }//Termina la funcion llantaAgregada()

      

      if ( !table.data().any()) {
      
        llantaAgregada();
        
       
       }else{

        Rows =  table.rows();  //Agregamos las filas a la variable Rows
        flag =0;

        //Recorremos los datos de esas filas 
        Rows.data().each(function (value, index) {

          codigo = value[0];
          cantidadLlantas = value[3];
          totalCant = parseInt(cantidadLlantas) + parseInt(cantidad);

          thisRow = table.row(this);
          
  
        

          if(flag == 1){
           
            return false;
          }
          
          if (codigo == idBotonLLanta) { //Si es la misma llanta se actualiza
            sameRow =  $("td").filter(":contains('"+codigo+"')").parents("tr");
             
            table.row(sameRow).data( [
              idBotonLLanta,
              descripcion, 
              modelo, 
              totalCant, 
              precio,  
              subtotal,
              botones
          ] ).draw(false);

          total = $("#total").val();
          newTotal = subtotal + parseInt(total);
          $("#total").val(newTotal);
         
          flag = 1;
          return false;
          

          }else{  //Si no es la misma llanta

            primerFila0 = $( "#pre-venta tbody" ).children();
            primerFila = primerFila0[index];
            despuesFila= $(primerFila).next("tr");
            anteriorFila= $(primerFila).prevAll("tr");
            anterior = parseInt(anteriorFila.length);
            posterior = parseInt(despuesFila.length);

              if(anterior == 0){ //No hay filas atras 
                
              
                      if (posterior == 0) { //Y no hay enfrente
                       
                          table.row.add( [
                            idBotonLLanta,
                            descripcion, 
                            modelo, 
                            cantidad, 
                            precio,  
                            subtotal,
                            botones
                        ] ).draw(false);

                        total = $("#total").val();
                        newTotal = subtotal + parseInt(total);
                        $("#total").val(newTotal);

                        flag = 1;
                        return false;

                      }else{//Si hay enfrente
                       
                        if (codigo == idBotonLLanta) {//Es el mismo codigo

                          sameRow =  $("td").filter(":contains('"+codigo+"')").parents("tr");
                            
                            
                            table.row(sameRow).data( [
                              idBotonLLanta,
                              descripcion, 
                              modelo, 
                              totalCant, 
                              precio,  
                              subtotal,
                              botones
                          ] ).draw(false);

                          total = $("#total").val();
                          newTotal = subtotal + parseInt(total);
                          $("#total").val(newTotal);

                          
                          flag = 1;
                          return false;
                        }else{
                         
                        }
                      }
              }else{  //SI hay filas atras

                primerFila0 = $( "#pre-venta tbody" ).children();
                primerFila = primerFila0[index];
                despuesFila= $(primerFila).next("tr");
                anteriorFila= $(primerFila).prevAll("tr");
                anterior = parseInt(anteriorFila.length);
                posterior = parseInt(despuesFila.length);

              

                
                    if (codigo == idBotonLLanta) { //Es el mismo codigo

                            sameRow =  $("td").filter(":contains('"+codigo+"')").parents("tr");
                          
                            
                            table.row(sameRow).data( [
                              idBotonLLanta,
                              descripcion, 
                              modelo, 
                              totalCant, 
                              precio,  
                              subtotal,
                              botones
                          ] ).draw(false);

                          total = $("#total").val();
                          newTotal = subtotal + parseInt(total);
                          $("#total").val(newTotal);

                         
                          flag = 1;
                          return false;
                    }else{ //No es el mismo codigo
                          if (posterior == 0) { //No hay fila enfrente
                           
                            table.row.add( [
                              idBotonLLanta,
                              descripcion, 
                              modelo, 
                              cantidad, 
                              precio,  
                              subtotal,
                              botones
                          ] ).draw(false); 

                          total = $("#total").val();
                          newTotal = subtotal + parseInt(total);
                          $("#total").val(newTotal);

                          flag = 1;
                          return false;
                          }else{ //Si hay una enfrente
                            if (codigo == idBotonLLanta) { //Y es la misma
                              sameRow =  $("td").filter(":contains('"+codigo+"')").parents("tr");
                             
                              
                              table.row(sameRow).data( [
                                idBotonLLanta,
                                descripcion, 
                                modelo, 
                                totalCant, 
                                precio,  
                                subtotal,
                                botones
                            ] ).draw(false);

                            total = $("#total").val();
                            newTotal = subtotal + parseInt(total);
                            $("#total").val(newTotal);
                           
                            flag = 1;
                            return false;
                            }else{
                             
                            }
                          }
                    }
              }
          }
         
          
         
    
      }); //Cerramos el ciclo que recorre las filas de la tabla

       }//Se cierra if
           
    } //Se cierra if que valida el formulario

   
      
} //Se cierra la funcion anidada a la boton de agregar informacion




