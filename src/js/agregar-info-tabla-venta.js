


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

          elemento = document.getElementsByTagName("tr");
          for (let index = 0; index < elemento.length; index++) {
            elemento[index].className = idBotonLLanta;
            
          }
         
          llanta = idBotonLLanta;

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

          //thisRow = table.row(this);
          thisRow = $("."+llanta); 

          alert("Iteracion: "+ index + " Llanta iterada:  "+codigo);
          despuesFila= $(thisRow).next("tr");
          anteriorFila= $(thisRow).prevAll();
          anterior = parseInt(anteriorFila.length);
          posterior = parseInt(despuesFila.length);

          if(flag == 1){
            alert("Se cancela el pedo");
            return false;
          }
          
          if (codigo == idBotonLLanta) { //Si es la misma llanta se actualiza
            sameRow =  $("td").filter(":contains('"+codigo+"')").parents("tr");
            console.log("Es la misma");
            
            table.row(sameRow).data( [
              idBotonLLanta,
              descripcion, 
              modelo, 
              totalCant, 
              precio,  
              subtotal,
              botones
          ] ).draw(false);
          alert("Se actualizo la llanta "  + codigo);
          flag = 1;
          return false;
          

          }else{  //Si no es la misma llanta

              if(anterior == 0){ //No hay filas atras 
                
                alert("No hay fila atras para llanta " + codigo + "hay " +anterior + "filas atras y " + posterior +"filas enfrente");
                      if (posterior == 0) { //Y no hay enfrente
                        alert("No hay fila enfrente para llanta " + codigo +" por lo tanto se agrega esta llanta 1");
                          table.row.add( [
                            idBotonLLanta,
                            descripcion, 
                            modelo, 
                            cantidad, 
                            precio,  
                            subtotal,
                            botones
                        ] ).draw(false);

                        flag = 1;
                        return false;

                      }else{//Si hay enfrente
                        alert("Si hay una llanta enfrente de "+codigo);
                        if (codigo == idBotonLLanta) {//Es el mismo codigo

                          sameRow =  $("td").filter(":contains('"+codigo+"')").parents("tr");
                            console.log("Es la misma");
                            
                            table.row(sameRow).data( [
                              idBotonLLanta,
                              descripcion, 
                              modelo, 
                              totalCant, 
                              precio,  
                              subtotal,
                              botones
                          ] ).draw(false);

                          alert("La llanta" + idBotonLLanta + "ya se encuentra, por lo tanto se actualiza 1");
                          flag = 1;
                          return false;
                        }else{
                          alert("Siguiente iteracion");
                        }
                      }
              }else{  //SI hay filas atras
                alert("Si hay fila atras para la llanta "+ codigo);
                    if (codigo == idBotonLLanta) { //Es el mismo codigo

                            sameRow =  $("td").filter(":contains('"+codigo+"')").parents("tr");
                            console.log("Es la misma");
                            
                            table.row(sameRow).data( [
                              idBotonLLanta,
                              descripcion, 
                              modelo, 
                              totalCant, 
                              precio,  
                              subtotal,
                              botones
                          ] ).draw(false);

                          alert("La llanta" + idBotonLLanta + "ya se encuentra, por lo tanto se actualiza 2");
                          flag = 1;
                          return false;
                    }else{ //No es el mismo codigo
                          if (posterior == 0) { //No hay fila enfrente
                            alert("No hay fila enfrente para llanta " + idBotonLLanta +" por lo tanto se agrega esta llanta 2");
                            table.row.add( [
                              idBotonLLanta,
                              descripcion, 
                              modelo, 
                              cantidad, 
                              precio,  
                              subtotal,
                              botones
                          ] ).draw(false); 
                          flag = 1;
                          return false;
                          }else{ //Si hay una enfrente
                            if (codigo == idBotonLLanta) { //Y es la misma
                              sameRow =  $("td").filter(":contains('"+codigo+"')").parents("tr");
                              console.log("Es la misma");
                              
                              table.row(sameRow).data( [
                                idBotonLLanta,
                                descripcion, 
                                modelo, 
                                totalCant, 
                                precio,  
                                subtotal,
                                botones
                            ] ).draw(false);
                            alert("La llanta" + idBotonLLanta + "ya se encuentra, por lo tanto se actualiza 3");
                            flag = 1;
                            return false;
                            }else{
                              alert("Siguiente iteracion");
                            }
                          }
                    }
              }
          }
         
          alert("Siguiente iteracion perrillo");
         
    
      }); //Cerramos el ciclo que recorre las filas de la tabla

       }//Se cierra if
           
    } //Se cierra if que valida el formulario

   
      
} //Se cierra la funcion anidada a la boton de agregar informacion




