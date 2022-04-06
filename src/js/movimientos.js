function MostrarClientes() {  
    $.fn.dataTable.ext.errMode = 'none';

table = $('#movimientos').DataTable({
      
    serverSide: false,
    ajax: {
        method: "POST",
        url: "./modelo/movimientos/traer-movimientos.php",
        dataType: "json"
 
    },  

  columns: [   
    { title: "#",              data: null             },
    { title: "descripcion",    data: "descripcion", width: "30%"},
    { title: "mercancia",      data: "mercancia", visible:false},
    { title: "fecha",          data: "fecha" },
    { title: "hora",           data: "hora"},
    { title: "id",           data: "id"},
    { title: "usuario",        data: "usuario"},
    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
        if(row.tipo == 1){

          return `<div class="btn btn-danger" onclick="remisionSalida(${row.id})"><i class="fas fa-file-pdf"></i><div>`;
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
  console.log(id);
  window.open('./modelo/movimientos/remision-salida.php?id='+ id, '_blank');
}


   
 

 