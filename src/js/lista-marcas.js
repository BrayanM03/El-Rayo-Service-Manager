function MostrarMarcas() {  
    // $.fn.dataTable.ext.errMode = 'none';
 
 table = $('#lista-marcas').DataTable({
     
     "processing": true,
     "serverSide": true,
      "ajax": './modelo/marcas/server_processing.php', 
      "responsive": true,
      columns: [   
        { title: "#",               data: null , width: "20px",   },
        { title: "Nombre",          data: 1       },
        { title: "Logo",            data: null, width: "100px", render: function (data) {  
            var logo = data[2]
           
            return "<img style='width: 100px; margin:auto;' src='./src/img/logos/"+ logo +".jpg'></img>"}
        },
        { title: "Accion",    width: "30px",  data: null, render: function (data) {  
            var id_marca = data[0]
           
            return "<div class='btn btn-primary' onclick='elimnarMarca("+ id_marca +")'><i class='fas fa-trash-alt'></i></div>"}
        }],
        scrollY: "300px",
 });


//Colocal indice en columna contador
table.on( 'draw.dt', function () {
    var PageInfo = $('#lista-marcas').DataTable().page.info();
         table.column(0, { page: 'current' }).nodes().each( function (cell, i) {
            cell.innerHTML = i + 1 + PageInfo.start;
        } );
    } );


    table.on( 'shown.bs.tab', function (e) {
        $($.fn.dataTable.tables( true ) ).css('width', '100%');
        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
    } );

    

 }
 
 MostrarMarcas();

function elimnarMarca(id_marca) { 

    Swal.fire({
        imageUrl: './src/img/alert.png',
        imageWidth: 90,
        imageHeight: 90,
        title: "Eliminar marca",
        html: '<span>¿Estas seguro de eliminar esta marca?</span>',
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
                    url: "./modelo/marcas/borrar-marca.php",
                    data: {"id_marca":id_marca},
                    //dataType: "JSON",
                    success: function (response) {
                      if(response == 1){
                        Swal.fire({
                            title: 'Marca eliminada',
                            html: "<span>La marca se elimino con exito</span>",
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