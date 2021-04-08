function MostrarVentas() {  

table = $('#ventas').DataTable({
      
      
    ajax: {
        method: "POST",
        url: "./modelo/ventas/historial-ventas.php",
        dataType: "json"

    },  

  columns: [   
    { title: "#",              data: null             },
    { title: "Folio",         data: "folio", render: function(data,type,row) {
        return '<span>RAY'+ data +'</span>';
        }},
    { title: "Fecha",          data: "fecha"          },
    { title: "Sucursal",       data: "sucursal"       },
    { title: "Vendedor",       data: "vendedor"       },
    { title: "Cliente",        data: "cliente"        },
    { title: "Cantidad",       data: "cantidad"       },
    { title: "Total",          data: "total"          },
    { title: "Estatus",        data: "estatus"        },
    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row) {
    
        return '<div style="display: flex"><button type="button" class="buttonEditar btn btn-warning" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button><br><button type="button" onclick="borrarVenta('+ row.folio +');" class="buttonBorrar btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
      },
    },
  ],
  paging: true,
  searching: true,
  scrollY: "50vh",
  info: true,
  responsive: true,
  
});

 //Enumerar las filas "index column"
 table.on( 'order.dt search.dt', function () {
    table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
        cell.innerHTML = i+1;
       
    } );
} ).draw();

}

MostrarVentas();


function borrarVenta(id) {

    Swal.fire({
        title: "Eliminar Venta",
        html: '<span>¿Estas seguro de eliminar esta venta?</span>',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        cancelButtonColor: '#00e059',
        showConfirmButton: true,
        confirmButtonText: 'Borrar', 
        cancelButtonColor:'#ff764d',
        focusConfirm: false }).then((result) => { 
        
            if(result.isConfirmed){    

    $.ajax({
        type: "post",
        url: "./modelo/ventas/borraVentaHistorial.php",
        data: {"folio": id},
        success: function (response) {
           if (response==1) {
              
            Swal.fire({
                title: 'Venta eliminada',
                html: "<span>La venta se elimino con exito</span>",
                icon: "success",
                cancelButtonColor: '#00e059',
                showConfirmButton: true,
                confirmButtonText: 'Aceptar', 
                cancelButtonColor:'#ff764d',
            })
            location.reload();
           }else{
            Swal.fire({
                title: 'Venta no eliminada',
                html: "<span>La venta no se pudo eliminar, dedido a algun error inesperado</span>",
                icon: "warning",
                cancelButtonColor: '#00e059',
                showConfirmButton: true,
                confirmButtonText: 'Aceptar', 
                cancelButtonColor:'#ff764d',
                showDenyButton: true,
                
            });

            location.reload();
           }
        }
    });
}

        });
  }


 