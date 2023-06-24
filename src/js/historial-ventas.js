function MostrarVentas() {  
    $.fn.dataTable.ext.errMode = 'none';
    ocultarSidebar();
table = $('#ventas').DataTable({
      
   
    processing: true,
    serverSide: true,
    ajax: './modelo/ventas/historial-ventas.php',
    columns: [   
    { title: "#",              data: null             },
    { title: "Folio",         data: "folio", render: function(data,type,row) {
        return '<span>RAY'+ row[0] +'</span>';
        }},
    { title: "Fecha",          data: 1          },
    { title: "Sucursal",       data: 2       }, 
    { title: "Vendedor",       data: 3       },
    { title: "Cliente",        data: 4        },
    //{ title: "ID",       data: "folio"  },
    { title: "Tipo",           data: 7       },
    { title: "Total",          data: 6          },
    { title: "Estatus",        data: 8        }, 
    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
        
        rol = $("#titulo-hv").attr("rol");
        
       if(rol == "1"){
            if (row[7] == "Credito") {
                return '<div style="display: flex; width: auto;">'+
                '<button onclick="traerPdfCredito(' +row[0]+ ');" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px">'+
                '<span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>'+
                '<button type="button" onclick="cancelarVenta('+ row[0] +');" class="buttonBorrar btn btn-primary">'+
                '<span class="fa fa-ban"></span><span class="hidden-xs"></span></button>'+
                '<button type="button" onclick="redirigirCredito('+ row[0] +');" class="buttonBorrar btn btn-info" style="margin-left: 8px">'+
                '<span class="fa fa-share-square"></span><span class="hidden-xs"></span></button>'+
                '<button type="button" onclick="borrarVenta('+ row[0] +',2);" class="buttonBorrar btn btn-warning" style="margin-left: 8px">'+
                '<span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
                
            }else if(row[7] == "Normal"){
                return '<div style="display: flex; width: auto;">'+
                '<button onclick="traerPdf(' +row[0]+ ');" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px">'+
                '<span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>'+
                '<button type="button" onclick="cancelarVenta('+ row[0] +');" class="buttonBorrar btn btn-primary">'+
                '<span class="fa fa-ban"></span><span class="hidden-xs"></span></button>'+
                '<button type="button" onclick="borrarVenta('+ row[0] +',1);" class="buttonBorrar btn btn-warning" style="margin-left: 8px">'+
                '<span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
         
            }else if(row[7] == ""){
                return '<div style="display: flex; width: auto;">'+
                '<button onclick="traerPdf(' +row[0]+ ');" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px">'+
                '<span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>'+
                '<button type="button" onclick="cancelarVenta('+ row[0] +');" class="buttonBorrar btn btn-primary">'+
                '<span class="fa fa-ban"></span><span class="hidden-xs"></span></button>'+
                '<button type="button" onclick="borrarVenta('+ row[0] +');" class="buttonBorrar btn btn-warning" style="margin-left: 8px">'+
                '<span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
            }
        }else{
            if (row[8] == "Abierta") {
                return '<div style="display: flex"><button onclick="traerPdfCredito(' +row[7]+ ');" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px"><span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>';
                
            }else if(row[8] == "Pagado"){
                return '<div style="display: flex"><button onclick="traerPdf(' +row[7]+ ');" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px"><span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>';
         
            }else if(row.estatus == "Cancelada"){
                
            }
        }
        
         },
    },
  ],
  paging: true,
  searching: true,
  scrollY: "50vh",
  info: false,
  responsive: false,
  ordering: "enable",
  order: [6, "desc"],
 
  
});
table.columns( [6] ).visible( false );

$("table.dataTable thead").addClass("table-info")

 //Enumerar las filas "index column"
 table.on( 'order.dt search.dt', function () {
    table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
        cell.innerHTML = i+1;
       
    } );
} ).draw();

}

MostrarVentas();


function borrarVenta(id, tipo) {

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
        data: {"folio": id, "tipo": tipo},
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
            }).then((result) => {  

                if(result.isConfirmed){
                    table.ajax.reload(null, false);
                }});

           
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
                
            }).then((result) => {  

                if(result.isConfirmed){
                    table.ajax.reload(null, false);
                }});
           }
        }
    });
}

        });
  }


  function traerPdf(folio){
    window.open('./modelo/ventas/generar-reporte-venta.php?id='+ folio , '_blank');
  }

  function traerPdfCredito(folio){
      window.open('./modelo/creditos/generar-reporte-credito.php?id='+ folio, '_blank');
  }

  function cancelarVenta(id) { 

    Swal.fire({
        title: "Cancelar Venta",
        html: '<span>¿Estas seguro de cancelar esta venta?</span><br><br>'+
        '<div class="m-auto"><label>Motivo de la cancelación.</label><br><textarea id="motivo" name="motivo" placeholder="Escribe el motivo del porque estas cancelando esta venta." class="form-control m-auto" style="width:300px;height:80px;" ></textarea></div>',
        showCancelButton: true,
        cancelButtonText: 'Cerrar',
        cancelButtonColor: '#00e059',
        showConfirmButton: true,
        confirmButtonText: 'Cancelar', 
        cancelButtonColor:'#ff764d',
        focusConfirm: false }).then((result) => { 
        
            if(result.isConfirmed){ 
                motivo = $("#motivo").val();
               // if($("#motivo").val())
                motivo = $("#motivo").val();
                $.ajax({
                    type: "POST",
                    url: "./modelo/ventas/cancelar-venta.php",
                    data: {"id_venta": id, "motivo_cancel": motivo},
                    success: function (response) {
                        if(response == 0){

                            Swal.fire({
                                title: 'Error',
                                html: "<span>La venta no se pudo cancelar</span>",
                                icon: "error",
                                cancelButtonColor: '#00e059',
                                showConfirmButton: true,
                                confirmButtonText: 'Aceptar', 
                                cancelButtonColor:'#ff764d',
                            }).then((result) => {  
                
                                if(result.isConfirmed){
                                    table.ajax.reload(null, false);
                                }});
                                table.ajax.reload(null, false);
                        }else if(response == 1 || response ==11){
                            Swal.fire({ 
                                title: 'Venta cancelada',
                                html: "<span>La venta se a cancelado.</span>",
                                icon: "success",
                                cancelButtonColor: '#00e059',
                                showConfirmButton: true,
                                confirmButtonText: 'Aceptar', 
                                cancelButtonColor:'#ff764d',
                            }).then((result) => {  
                
                                if(result.isConfirmed){
                                    table.ajax.reload(null, false);
                                }});
                                table.ajax.reload(null, false);

                        }else if(response == 3){
                            Swal.fire({
                                title: 'Venta ya cancelada',
                                html: "<span>Esta venta ya esta cancelada.</span>",
                                icon: "warning",
                                cancelButtonColor: '#00e059',
                                showConfirmButton: true,
                                confirmButtonText: 'Aceptar', 
                                cancelButtonColor:'#ff764d',
                            }).then((result) => {  
                
                                if(result.isConfirmed){
                                    table.ajax.reload(null, false);
                                }});
                                table.ajax.reload(null, false);
                        }
                      
                    }
                });
                

            }

        });

   }


  function redirigirCredito(id){
    window.open('./detalle-credito.php?id='+ id, '_blank');
   }



function ocultarSidebar(){
    let sesion = $("#emp-title").attr("sesion_rol");
  if(sesion == 4){
    $(".rol-4").addClass("d-none");

  }
  };