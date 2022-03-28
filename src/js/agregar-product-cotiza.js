$(document).ready(function() {

$.fn.dataTable.ext.errMode = 'none';


$.ajax({
  type: "POST",
  url: "./modelo/cotizaciones/borrar-tabla-cotiza-temp.php",
  data: {"data": "data"},
  success: function (response) {}
    
});

table = $('#pre-cotizacion').DataTable({
  

  
    destroy: true,
    serverSide: true,
    processing: false,
    ajax: {
        method: "POST",
        url: "./modelo/cotizaciones/traer-cotizacion-temp.php",
        dataType: "json",
        error: function(){  // error handling
          numRows = table.column( 0 ).data().length;
     
      if (numRows == 0) {
        $(".pre-cotizacion-error").html("");
        $('#pre-cotizacion > tbody').empty();
        $("#pre-cotizacion tbody").append('<tr><th id="empty-table" style="text-align: center;" colspan="8">Preventa vacia</th></tr>');
        $("#pre-cotizacion_processing").css("display","none");
      }

        
          
        }

    },  

  columns: [   
    { title: "#",               data: null             },
    { title: "Descripcion",     data: "descripcion"    },
    { title: "Modelo",          data: "modelo"    },
    { title: "Cantidad",        data: "cantidad"       },
    { title: "Precio",          data: "precio"         },
    { title: "Importe",         data: "importe"        },
    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
    
        return '<div style="display:flex; justify-content: center; align-items:center;">'+
        '<span class="hidden-xs"></span></button><br><button type="button" rowid="'+ row.id +'" class="borrar-articulo btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>'+
        '</div>';
      },
    },
  ],

  paging: false,
  searching: false,
  //scrollY: "auto",
  info: false,
  responsive: true,
  order: [0, "desc"],
 
  
});


  table.on( 'order.dt search.dt', function () {
    table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
        cell.innerHTML = i+1;
       
    } );
} );



table.on('click', '.borrar-articulo', function() {
  
  let $tr = $(this).closest('tr');
  let id = $(this).attr("rowid");
  let $importe = $(this).attr("importe");

  $.ajax({
    type: "POST",
    url: "./modelo/cotizaciones/borrar-cotiza-temp.php",
    data: {"id":id},
    success: function(response) {
      if(response == 1){
        $.ajax({
          type: "POST",
          url: "./modelo/cotizaciones/traer-importe-cotizacion.php",
          data: "data",
          success: function (response) {
            $("#total-cotizacion").val(response);
            
            toastr.success('Producto borrado', 'Listo');
          }
        });
          //tabla_presalida.ajax.reload(null, false);
          // Le pedimos al DataTable que borre la fila
          table.row(this).remove().draw();

          
      toastr.success('Producto borrado con exito', 'Correcto' );
    
      
     
      }else{
        toastr.warning('Hubo un error al borrar el producto', 'Error' );
      }

    }

  });


});


}); 


function agregarProducto() { 

    tyre_precio = $("#precio").val();
    modelo = $("#btn-agregar").attr("modelo");
    tyre_amount = $("#cantidad").val();
    tyre_import = parseFloat(tyre_precio) * tyre_amount;
    tyre_description = $("#btn-agregar").attr("descripcion");

    if(tyre_amount <= 0 ){
        toastr.warning('La cantidad no puede estar vacia, ser 0 o negativo', 'Alerta');
    }else if(tyre_description == null){

      toastr.warning('Selecciona una llanta', 'Alerta');
    }else{
        tyre_id = $("#btn-agregar").attr("idcode");
        tyre_marca = $("#btn-agregar").attr("marca");

        $.ajax({
            type: "POST",
            url: "./modelo/cotizaciones/agregar-cotizacion-temp.php",
            data: {"id": tyre_id, "descripcion": tyre_description, "modelo": modelo, "cantidad": tyre_amount, "marca": tyre_marca,"precio": tyre_precio, "importe": tyre_import},
          
            success: function (response) {

              if (response ==1 || response == 2) {
                table.ajax.reload(null,false);
                $.ajax({
                  type: "POST",
                  url: "./modelo/cotizaciones/traer-importe-cotizacion.php",
                  data: "data",
                  success: function (response) {
                    $("#total-cotizacion").val(response);
                    
                toastr.success('Producto agregado', 'Listo');
                  }
                });
              }
                
               


            }
        });

    }

 }

function reload() {
  table.ajax.reload(null,false);
  }
 



 