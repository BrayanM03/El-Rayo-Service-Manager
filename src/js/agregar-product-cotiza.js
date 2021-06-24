$(document).ready(function() {

$.fn.dataTable.ext.errMode = 'none';

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
    { title: "Cantidad",        data: "cantidad"       },
    { title: "Precio",          data: "precio"         },
    { title: "Importe",         data: "importe"        },
    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
    
        return '<div style="display:flex; justify-content: center; align-items:center;">'+
        '<span class="hidden-xs"></span></button><br><button type="button" onclick="borrarProductoTmp('+ row.id +"," + row.importe +');" class="borrar-articulo btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>'+
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
}); 

function agregarProducto() { 

    tyre_precio = $("#btn-agregar").attr("precio");
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
            data: {"id": tyre_id, "descripcion": tyre_description,"cantidad": tyre_amount, "marca": tyre_marca,"precio": tyre_precio, "importe": tyre_import},
          
            success: function (response) {
                
                table.ajax.reload(null,false);

            }
        });

    }

 }

function reload() {
  table.ajax.reload(null,false);
  }
 
 setInterval( reload,1000);


 function borrarProductoTmp(id){
  

  $.ajax({
    type: "POST",
    url: "./modelo/cotizaciones/borrar-cotiza-temp.php",
    data: {"id":id},
    success: function (response) {
      if(response == 1){

           
      numRows = table.column( 0 ).data().length;
     

      if (numRows==1){
       
        table.ajax.reload(null, false);
        $("#pre-cotizacion tbody tr").remove();
        $(".pre-cotizacion-error").html("");
        $(".products-grid-error").remove();
        $("#pre-cotizacion tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Sin productos</th></tr>');
        $("#pre-cotizacion_processing").css("display","none");
      
      }else{
        table.ajax.reload(null,false);
      }
      
        toastr.success('Producto borrado', 'Listo');
        
      }else{
        toastr.error('El producto no pudo ser borrado', 'Error');

      }
      }
      
  });
 }

