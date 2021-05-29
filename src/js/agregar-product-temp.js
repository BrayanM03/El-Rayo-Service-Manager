


$(document).ready(function() {

  $.fn.dataTable.ext.errMode = 'none';

  table = $('#pre-venta').DataTable({
    
    destroy: true,
    serverSide: true,
    processing: false,
    ajax: {
        method: "POST",
        url: "./modelo/ventas/detalle-venta-temp.php",
        dataType: "json",
        error: function(){  // error handling
          numRows = table.column( 0 ).data().length;
     
      if (numRows == 0) {
        $(".pre-venta-error").html("");
        $("#pre-venta tbody").append('<tr><th id="empty-table" style="text-align: center;" colspan="8">Preventa vacia</th></tr>');
        $("#pre-venta_processing").css("display","none");
      }

        
          
        }

    },  

  columns: [   
    { title: "#",               data: null             },
    { title: "Codigo",          data: "codigo",        }, 
    { title: "Descripcion",     data: "descripcion"    },
    { title: "Modelo",          data: "modelo"         },
    { title: "Cantidad",        data: "cantidad"       },
    { title: "Precio",          data: "precio"         },
    { title: "Importe",         data: "importe"        },
    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
    
        return '<span class="hidden-xs"></span></button><br><button type="button" onclick="borrarProductoTmp('+ row.id +"," + row.importe +');" class="borrar-articulo btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
      },
    },
  ],

  paging: false,
  searching: false,
  scrollY: "350px",
  info: false,
  responsive: false,
  order: [0, "desc"],
 
  
});


  table.on( 'order.dt search.dt', function () {
    table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
        cell.innerHTML = i+1;
       
    } );
} );
 


});

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

//Borrar producto temporal

function borrarProductoTmp(id, importe){

  $.ajax({
    type: "POST",
    url: "./modelo/ventas/borrar-producto-temp.php",
    data: {"id": id, "borrar": "borrar"},
    success: function (response) {
      if (response == 1) {
     
      
      numRows = table.column( 0 ).data().length;
     

      if (numRows==1){
       
        table.ajax.reload();
        $("#pre-venta tbody tr").remove();
        $(".pre-venta-error").html("");
        $(".products-grid-error").remove();
        $("#pre-venta tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
        $("#pre-venta_processing").css("display","none");
      
      }else{
        table.ajax.reload();
      }

      toastr.success('Producto borrado con exito', 'Correcto' );
      total = $("#total").val();
      result =  parseInt(total) - parseInt(importe);
      console.log(result);
      
      if(total == 0){
        $("#total").val(0);
      }else{
        $("#total").val(result);
      }

      }else{
        toastr.warning('Hubo un error al borrar el producto', 'Error' );
  
      }
    }
  });

}

function agregarInfo(){
    //Funcion que se encargara de mover informacion del producto a una tabla para luego ser procesada como una venta
    
    valorCant0 = $('#cantidad').val();
    valorCant = parseInt(valorCant0);
    valitationQuanty = $('#cantidad')[0].checkValidity();
    valitationdescription = $("#description")[0].checkValidity();
    valitationModel = $("#modelo")[0].checkValidity();
    valitationMetodoPago = $("#metodos-pago").val();
    valitationPrice = $("#precio").val();
    sucursalVal = $("#sucursal").val();
    cliente = $("#select2-clientes-container").attr("id-cliente");
    


    stockLlanta0 = $("#agregar-producto").attr("stock");
    stockLlanta = parseInt(stockLlanta0);
   
    //console.log(valitationQuanty);
    if (valitationQuanty == false ) {

      toastr.warning('Necesita especificar una cantidad', 'Alerta' );

      
    }else if(!cliente){

      toastr.warning('Necesita seleccionar un cliente', 'Alerta' );

    }else if(!valitationMetodoPago){

      toastr.warning('Seleccione un metodo de pago', 'Alerta' );

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
      importes         =   precio * cantidad; 
      //subtotal         =   parseFloat(importes);
      botones = "<div class='btn btn-danger borrar-articulo' style='margin-right:5px;'><i class='fas fa-trash'></i></div>";

      

      

  
     
    
  
     //Esta es la funcion llanta agregada y es para agregar la llanta si es una llanta no repetida y si el contador de llantas repetidas esta en 0
      function llantaAgregada() {

          $.ajax({
            type: "POST",
            url: "./modelo/ventas/detalle-venta-insertar.php",
            data: {"codigo": idBotonLLanta, "descripcion": descripcion,
                    "modelo": modelo, "cantidad": cantidad, "precio": precio,
                    "subtotal": importes},
            
            success: function (response) {

            if (response == 1) {
              table.ajax.reload(); 
              $("#empty-table").remove();
              toastr.success('Producto agregado correctamente', 'Agregado');

            }else if(response == 2){
              
              toastr.error('La cantidad que especificaste revasa el stock actual', 'Error');
              table.ajax.reload();
              $("#empty-table").remove();

            }else{
              table.ajax.reload();
              $("#empty-table").remove();
            }
             

            },

            
          });



          

      }//Termina la funcion llantaAgregada()

      

      if ( !table.data().any()) {
      
        llantaAgregada();
       
       
       }else{
        llantaAgregada();
        
       }//Se cierra if


           
    } //Se cierra if que valida el formulario

    //Calculamos el valor total

    
    $.ajax({
      type: "POST",
      url: "./modelo/ventas/sumarTotaldetalleVenta.php",
      data: {"data":"data"},
      success: function (response) {
        console.log(response);
        $("#total").val(response);
      }
    });
      
} //Se cierra la funcion anidada a la boton de agregar informacion


function limpiarTabla(){



        
  if ( !table.data().any()){ 

      toastr.warning('La tabla esta vacia', 'Tabla limpia' ); 
  }else{
    

      $.ajax({
          type: "POST",
          url: "./modelo/ventas/vaciar-tabla-temp.php",
          data: {"data": "data"},
          success: function (response) {
              if (response == 1) {
                  toastr.success('Tabla fue vaciada ', 'Listo' );  
                  $('#pre-venta > tbody').empty();
                  $(".pre-venta-error").html("");
                 // $("#pre-venta tbody").append('<tr><th id="empty-table" style="text-align: center;" colspan="8">Preventa vacia</th></tr>');
                  $("#pre-venta_processing").css("display","none"); 
                  $("#total").val(0);
                  table.rows().remove().draw()
              }else if(response == 0){
                  toastr.warning('La tabla ya esta vaciada', 'Advertencia');
                  table.rows().remove().draw()
              
              }else{
                  toastr.danger('Hubo un problema al vaciar la tabla ', 'Error' );
                  
              }
              
          }
      });
    


  }
  //$(".tbody").empty();
}






