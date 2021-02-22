


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

      

      //Despues de que se valida el formulario se recorre la tabla para revisar que no se repita una llanta con la funcion Recorrer()
      Recorrer(); 
  
       function Recorrer() { 
  
      Rows =  table.rows();  //Agregamos las filas a la variable Rows
      
      //Recorremos los datos de esas filas 
      Rows.data().each(function (value, index) {
        codigo = value[0];
        llanta = value[1];
        cantidadLlantas = value[3];

        thisRow =  $("td").filter(":contains('"+codigo+"')").parents("tr");
        despuesFila= $(thisRow).next();
        cantFilasDespues = despuesFila.length;
        anteriorFila= $(thisRow).prevAll();
        cantFilasAnteriores = anteriorFila.length;

        switch (codigo) {
          case idBotonLLanta:
             
              alert("Es la misma llanta");
              totalCant = parseInt(cantidadLlantas) + parseInt(cantidad);  //Sumamos esa cantidad con la cantidad que mandamos y la agregamos a la variable
              
              table.row(thisRow).data([
              idBotonLLanta, descripcion, modelo, totalCant, precio, subtotal, botones]).draw(false); //Borramos la fila
             
              thisRow.addClass("repetida");

              if (cantFilasDespues >=1) {
                contador =1
                console.log("Filas posteriores: "+despuesFila.length);  
                console.log("Filas anteriores: "+anteriorFila.length);  
              }else{
                contador = 0;
                console.log("<b>el contador vale: " + contador);
                console.log("Filas posteriores: "+despuesFila.length);
                console.log("Filas anteriores: "+anteriorFila.length);
               
              }
              
              
            break;
        
          default:
              alert("No es la misma llanta");
                if (contador == 1 ) {
                  
                
                 if (cantFilasAnteriores == 0) {
                   contador =2;
                   console.log("Filas posteriores: "+despuesFila.length);
                   console.log("Filas anteriores: "+anteriorFila.length);    
                 }else{
                  console.log("Filas posteriores: "+despuesFila.length);
                  console.log("Filas anteriores: "+anteriorFila.length);
                  alert("llanta abajo de una que ya fue duplicada");
                 }
                 

                }else if(contador ==0){
                  contador =0;
                  console.log("Filas posteriores: "+despuesFila.length);
                  console.log("Filas anteriores: "+anteriorFila.length); 
                }
                
            break;
        }
  
    }); //Cerramos el ciclo que recorre las filas de la tabla

   

 } //Aqui termina la función Recorrer()

  
     //Esta es la funcion llanta agregada y es para agregar la llanta si es una llanta no repetida y si el contador de llantas repetidas esta en 0
      function llantaAgregada() {
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
          
          toastr.success('Producto agregado correctamente', 'Correcto' );

      }//Termina la funcion llantaAgregada()

      if ( !table.data().any() || contador ==2) {
      
        llantaAgregada();
        console.log("Si se agrego llanta");
        contador = 2;
        console.log("<b>el contador vale: </b>" + contador);

       
       }else if(contador == 0){
         alert("Se termina validaciones");
       }//Se cierra if
           
    } //Se cierra if que valida el formulario

   
      
} //Se cierra la funcion anidada a la boton de agregar informacion



