function MostrarClientes() {  
    $.fn.dataTable.ext.errMode = 'none';

table = $('#movimientos').DataTable({
      
    processing: true,
    serverSide: true,
    ajax: './modelo/movimientos/traer-movimientos.php',  
    rowCallback: function(row, data, index) {
      var info = this.api().page.info();
      var page = info.page;
      var length = info.length;
      var columnIndex = 0; // Índice de la primera columna a enumerar

      $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
    },
  columns: [   
    { title: "#",              data: null             },
    { title: "Folio",          data: 0 },
    { title: "descripcion",    data: 1, width: "30%"},
    { title: "mercancia",      data: 2, visible:true},
    { title: 'proveedor',      data: 8, render:(data, row)=>{
      if(data == null){
        var prov = 'NA'
      }else{
        var prov = data;
      }
      return prov;
    }},
    { title: 'factura',        data: 9},
    { title: "fecha",          data: 3 },
    { title: "hora",           data: 4 },
    { title: "tipo",           data: 6, render(data, row) {
      if(data == 1){
        tipo = 'Movimiento';
      }else if(data == 2){
        tipo = 'Ingreso';
      }else if(data == 3){
        tipo = 'Retiro';
      }
      return tipo;
    }},
    { title: "usuario",        data: 5 },
    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
        if(row[6] == 1 || row[6] ==3 || row[6] ==4){

          return `<div class="btn btn-danger" onclick="remisionSalida(${row[0]})"><i class="fas fa-file-pdf"></i><div>`;
        }else if(row[6] ==2){
          return `<div class="btn btn-danger" onclick="remisionIngreso(${row[0]})"><i class="fas fa-file-pdf"></i><div>`;

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

}

MostrarClientes();


function remisionSalida(id){

  window.open('./modelo/movimientos/remision-salida.php?id='+ id, '_blank');
}

function remisionIngreso(id){

  window.open('./modelo/movimientos/remision-ingreso.php?id='+ id, '_blank');
}


   
 

 