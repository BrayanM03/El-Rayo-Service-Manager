function MostrarClientes() {  
    $.fn.dataTable.ext.errMode = 'none';

table = $('#movimientos').DataTable({
      
    processing: true,
    serverSide: true,
    ajax:'./modelo/movimientos/server-processing.php',
    rowCallback: function(row, data, index) {
      var info = this.api().page.info();
      var page = info.page;
      var length = info.length;
      var columnIndex = 0; // Índice de la primera columna a enumerar

      $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
    },
  columns: [   
    { title: "#",              data: null             },
    { data: 1, title: "Descripcion",     width: "30%"},
    { title: "Fecha",      data: null, visible:true, render: (row, data)=>{return `${row[2]} - ${row[5]}`}},
    //{ title: "id_usuario",          data: 3 },
    { title: "Usuario",           data: 4},
    { title: "Sucursal",        data: 8},
    { title: "Tipo",        data: 6},
    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
        if(row.tipo == 1 || row.tipo ==2 || row.tipo ==3 || row.tipo ==4){

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
  order: [2, "desc"],
 
  
});

$("table.dataTable thead").addClass("table-info");

table.columns( [5] ).visible( false );


}

MostrarClientes();


function remisionSalida(id){
  console.log(id);
  window.open('./modelo/movimientos/remision-salida.php?id='+ id, '_blank');
}


   
 

 