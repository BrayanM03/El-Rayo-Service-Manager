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
        $(".pre-venta-error").html("");
        $("#pre-venta tbody").append('<tr><th id="empty-table" style="text-align: center;" colspan="8">Preventa vacia</th></tr>');
        $("#pre-venta_processing").css("display","none");
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
    
        return '<span class="hidden-xs"></span></button><br><button type="button" onclick="borrarProductoTmp('+ row.id +"," + row.importe +');" class="borrar-articulo btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
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


function agregarProducto() { 

    tyre_precio = $("#btn-agregar").attr("precio");
    tyre_amount = $("#cantidad").val();
    tyre_import = parseFloat(tyre_precio) * tyre_amount;

    if(tyre_amount <= 0 ){
        toastr.warning('La cantidad no puede estar vacia, ser 0 o negativo', 'Alerta');
    }else{
        tyre_id = $("#btn-agregar").attr("idcode");
        tyre_description = $("#btn-agregar").attr("descripcion");
        tyre_marca = $("#btn-agregar").attr("marca");

        $.ajax({
            type: "POST",
            url: "./modelo/cotizaciones/agregar-cotizacion-temp.php",
            data: {"id": tyre_id, "descripcion": tyre_description,"cantidad": tyre_amount, "marca": tyre_marca,"precio": tyre_precio, "importe": tyre_import},
          
            success: function (response) {
                
                table.ajax.reload();

            }
        });

    }

 }