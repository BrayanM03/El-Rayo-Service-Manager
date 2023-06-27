function MostrarCotizaciones() {  
    // $.fn.dataTable.ext.errMode = 'none';
 
 table = $('#lista-cotizaciones').DataTable({
     
     "processing": true,
     "serverSide": true,
      "ajax": './modelo/cotizaciones/server_processing.php', 
      "responsive": true,
      rowCallback: function(row, data, index) {
        var info = this.api().page.info();
        var page = info.page;
        var length = info.length;
        var columnIndex = 0; // Índice de la primera columna a enumerar
  
        $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
      },
      columns: [   
        { title: "#",              data: null    },
        { title: "Fecha",          data: 1       },
        { title: "Usuario",        data: 2       },
        { title: "Cliente",        data: 3       },
        { title: "Total",          data: 4       },
        { title: "Estatus",        data: 5       },
        { title: "Hora",           data: 6       },
        { title: "Comentario",     data: 7       },
        { title: "Accion",     data: null, render: function (data) {  
            var folio = data[0]
           
            return "<div class='btn btn-danger m-2' onclick='abrir("+ folio +")'><i class='fas fa-file-pdf'></i></div>"+
            "<div class='btn btn-primary' onclick='elimnarCotizacion("+ folio +")'><i class='fas fa-trash-alt'></i></div>"}
        }]
 });

 }
 
 MostrarCotizaciones();

 function abrir(folio) {
    window.open('./modelo/cotizaciones/generar-reporte-cotizacion.php?id='+ folio, '_blank');
}

function elimnarCotizacion(folio) { 

    Swal.fire({
        imageUrl: './src/img/alert.png',
        imageWidth: 90,
        imageHeight: 90,
        title: "Eliminar cotización",
        html: '<span>¿Estas seguro de eliminar esta cotización?</span>',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        cancelButtonColor: '#00e059',
        showConfirmButton: true,
        confirmButtonText: 'Borrar', 
        cancelButtonColor:'#ff764d',
        focusConfirm: false }).then((result) => { 
        
            if(result.isConfirmed){ 

                $.ajax({
                    type: "POST",
                    url: "./modelo/cotizaciones/borrar-cotizacion.php",
                    data: {"folio":folio},
                    //dataType: "JSON",
                    success: function (response) {
                      if(response == 1){
                        Swal.fire({
                            title: 'Cotización eliminada',
                            html: "<span>La cotización se elimino con exito</span>",
                            icon: "success",
                            cancelButtonColor: '#00e059',
                            showConfirmButton: true,
                            confirmButtonText: 'Aceptar', 
                            cancelButtonColor:'#ff764d',
                        }).then((result) => {
                            if(result.isConfirmed){
                                table.ajax.reload(null, false);
                            }
                        });
                      }  
                    }
                });

             } });

   

 }