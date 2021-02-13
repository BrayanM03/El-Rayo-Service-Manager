


$(document).ready(function() {
  
  columnDefs = [{
    title: "ID"
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

} );

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


    valitationQuanty = $('#cantidad')[0].checkValidity();
    valitationdescription = $("#description")[0].checkValidity();
    valitationModel = $("#modelo")[0].checkValidity();
    valitationPrice = $("#precio").val();

    //console.log(valitationQuanty);
    if (valitationQuanty == false) {

      toastr.warning('Necesita especificar una cantidad', 'Alerta' );

      
    }else if(valitationdescription == false){

      toastr.warning('La descripción no puede ir vacia', 'Alerta' );

    }else if(valitationModel == false ){
      toastr.warning('Especifique un modelo', 'Alerta');
      
    } else if(valitationPrice == 0 ){
      toastr.warning('Anote un precio', 'Alerta');
      
    }else {
      
      idBotonLLanta    =   $("#agregar-producto").attr("idLlanta");
      descripcion      =   $("#description").val();
      modelo           =   $("#modelo").val();
      marca            =   $(".logo-marca").attr("marca");
      cantidad         =   $("#cantidad").val();
      precio           =   $("#precio").val();
      sucursal         =   $("select[id = sucursal] option:selected").text();
      subtotal         =   precio * cantidad;
      botones = "<div style='display:flex;'><div class='btn btn-success' id='realizar-venta' style='margin-right:5px;'><i class='fas fa-pen'></i></div>" +
                "<div class='btn btn-danger' id='realizar-venta'><i class='fas fa-trash'></i></div></div>";
      
      
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

    }

   
      
}