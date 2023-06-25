function MostrarClientes() {  
    $.fn.dataTable.ext.errMode = 'none';

table = $('#movimientos').DataTable({
      
    processing: true,
    serverSide: true,
    ajax: './modelo/movimientos/traer-movimientos.php',  

  columns: [   
    { title: "#",              data: null             },
    { title: "descripcion",    data: 1, width: "30%"},
    { title: "mercancia",      data: 2, visible:true},
    { title: "fecha",          data: 3 },
    { title: "hora",           data: 4 },
    { title: "id",             data: 0 },
    { title: "usuario",        data: 5 },
    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
        if(row[6] == 1 || row[6] ==2 || row[6] ==3 || row[6] ==4){

          return `<div class="btn btn-danger" onclick="remisionSalida(${row[0]})"><i class="fas fa-file-pdf"></i><div>`;
        }else{

          return `<span>No disp</span>`;
        }
      },
    }, 
  ],
  paging: true,
  searching: true,
  scrollY: "50vh",
  info: false,
  responsive: false,
  order: [5, "desc"],
 
  
});

$("table.dataTable thead").addClass("table-info");

table.columns( [5] ).visible( false );

 //Enumerar las filas "index column"
 table.on( 'order.dt search.dt', function () {
    table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
        cell.innerHTML = i+1;
       
    } );
} ).draw();

}

MostrarClientes();


function remisionSalida(id){

  window.open('./modelo/movimientos/remision-salida.php?id='+ id, '_blank');
}


   
 

 