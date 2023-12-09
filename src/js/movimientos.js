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
    { title: 'factura',        data: 9, visible:true},
    { title: 'Estado fact.', data:11, render(data, row) {
      if(data == 1){
        estado_factura_ = 'Sin factura';
      }else if(data == 2){
        estado_factura_ = 'Factura completa';
      }else if(data == 3){
        estado_factura_ = 'Factura incompleta';
      }else{
        estado_factura_ = 'No aplica'
      }
      return estado_factura_;
    }},
    { title: "fecha",          data: 3 },
    { title: "hora",           data: 4 },
    { title: "tipo",           data: 6, render(data, row) {
      if(data == 1){
        tipo = 'Movimiento';
      }else if(data == 2){
        tipo = 'Ingreso';
      }else if(data == 3){
        tipo = 'Retiro';
      }else if(data == 4){
        tipo = 'Ingreso';
      }else if(data == 5){
        tipo = 'Borrado';
      }else{
        tipo = data;
      }
      return tipo;
    }},
    { title: "estatus",        data: 10 },
    { title: "usuario",        data: 5 },
    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
        if(row[10]=='Completado'){
          class_btn_check = 'btn-secondary disabled';
          candado ='ss';
        }else{
          candado = '';
          class_btn_check = 'btn-success';
        }
        if(row[6] == 1 || row[6] ==3 || row[6] ==4){
          return `
          <div style="display:flex;">
              <div class="btn btn-danger mr-2" onclick="remisionSalida(${row[0]})"><i class="fas fa-file-pdf"></i></div>
              <div class="btn ${class_btn_check}" onclic${candado}k="AprobarMovimiento(${row[0]})"><i class="fas fa-check" disabled></i></div>
          </div>
              `;
        }else if(row[6] ==2){
          return `
          <div style="display:flex;">
             <div class="btn btn-danger mr-2" onclick="remisionIngreso(${row[0]})"><i class="fas fa-file-pdf"></i></div>
             <div class="btn ${class_btn_check}" onclick="AprobarMovimiento(${row[0]})"><i class="fas fa-check"></i></div>
          </div>`;

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
  order: [1, "desc"],
 
  
});

$("table.dataTable thead").addClass("table-info");

//table.columns( [5] ).visible( false );

}

MostrarClientes();


function remisionSalida(id){

  window.open('./modelo/movimientos/remision-salida.php?id='+ id, '_blank');
}

function remisionIngreso(id){

  window.open('./modelo/movimientos/remision-ingreso.php?id='+ id, '_blank');
}

function AprobarMovimiento(id_mov){
    Swal.fire({
      icon: 'question',
      html: '¿Deseas aprobar el movimiento',
      confirmButtonText: 'Aprobar el movimiento',
    }).then(function(resp){
      if(resp.isConfirmed){
        $.ajax({
          type: "post",
          url: "./modelo/requerimientos/aprobar-movimiento.php",
          data: {id_mov},
          dataType: "JSON",
          success: function (response) {
            if(response.estatus){
              Swal.fire({
                icon:'success',
                html: response.mensaje
              })
            }else{
              Swal.fire({
                icon:'error',
                html: response.mensaje
              })
            }
            table.ajax.reload(false, null)
          }
        });
      }
    })
}


   
 

 