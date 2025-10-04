function MostrarCuentasPorPagar() {  
    //$.fn.dataTable.ext.errMode = 'none';
    //ocultarSidebar();
    table = $('#cuentas-por-pagar').DataTable({
      
        "bDestroy": true,
        processing: true,
        serverSide: true,
        ajax: './modelo/cuentas_pagar/historial-cuentas-pagar.php',  
        rowCallback: function(row, data, index) {
          var info = this.api().page.info();
          var page = info.page;
          var length = info.length;
          var columnIndex = 0; // Índice de la primera columna a enumerar
    
          $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
          if(data[11] == 4){
            $(row).css('background-color','#eaefc8')
          }
          if(data[11] == 5){
            $(row).css('background-color','#feajb6')
          }
        },
      columns: [   
        { title: "#",              data: null             },
        { title: "Folio",          data: 0 },
       /*  { title: "descripcion",    data: 1, width: "30%"}, */
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
          }else if(data == 4){
            estado_factura_ = '<span class="badge bg-success p-2 text-white">Factura pagada</span>';
          }else if(data == 5){
            estado_factura_ = '<span class="badge bg-danger p-2 text-white">Factura vencida</span>';
          }else{
            estado_factura_ = 'No aplica'
          }
          return estado_factura_;
        }},
        { title: "total",          data: 12, render: function(data){
            return new Intl.NumberFormat().format(data)
        } },
        { title: "pagado",          data: 13, render: function(data){
          return new Intl.NumberFormat().format(data)
      } },
      { title: "restante",          data: 14, render: function(data){
        return new Intl.NumberFormat().format(data)
    } },
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
        { title: "usuario",        data: 5 },
        { title: "Accion",
          data: null,
          className: "celda-acciones",
          render: function (row, data) {
            if(row[11] =='patata'){
              class_btn_check = 'btn-secondary disabled';
              candado ='ss';
            }else{
              candado = '';
              class_btn_check = 'btn-primary';
            }
            if(row[6] == 1 || row[6] ==3 || row[6] ==4){
              return `
              <div style="display:flex;">
                  <div class="btn btn-danger mr-2" onclick="remisionSalida(${row[0]})"><i class="fas fa-file-pdf"></i></div>
                  <div class="btn ${class_btn_check}" onclic${candado}k="administrarCuenta(${row[0]}, ${row[6]})"><i class="fas fa-check" disabled></i></div>
              </div>
                  `;
            }else if(row[6] ==2){
              return `
              <div style="display:flex;">
                 <div class="btn btn-danger mr-2" onclick="remisionIngreso(${row[0]})"><i class="fas fa-file-pdf"></i></div>
                 <div class="btn ${class_btn_check}" onclick="administrarCuenta(${row[0]},  ${row[6]})"><i class="fas fa-check"></i></div>
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
//table.columns( [6] ).visible( true );
$("table.dataTable thead").addClass("table-dark")
$("table.dataTable thead").addClass("text-white")

}

MostrarCuentasPorPagar();

function administrarCuenta(id, tipo_remision){
  window.open("administracion-movimiento.php?id=" + id + "&tipo_remision="+tipo_remision, '_blank');
}

function remisionIngreso(id){

  window.open('./modelo/movimientos/remision-ingreso.php?id='+ id, '_blank');
}

function modalEstadoCuenta(){
  $.ajax({
    type: "post",
    url: "./modelo/cuentas_pagar/obtener-proveedores.php",
    data: {'data':'data'},
    dataType: "json",
    success: function (response) {
      Swal.fire({
        icon: 'question',
        title: 'Generar estado de cuenta',
        html: `
        <div class="container">
            <div class="row">  
                <div class="col-12">
                    <label>Proveedor</label>
                    <select class="form-control" id="proveedores-ed">

                    </select>
                </div> 
            </div>  
        </div>
        `,
        didOpen: ()=>{
          $("#proveedores-ed").empty()
          response.data.forEach(element => {
            $("#proveedores-ed").append(`
                <option value="${element.id}">${element.nombre}</option>
            `)
          });
        },
        confirmButtonText:'Generar estado de cuenta',  
      }).then((r)=>{
        if(r.isConfirmed){
          let id_proveedor = $("#proveedores-ed").val();
          window.open('estado-cuenta-proveedor.php?id_proveedor='+id_proveedor, '_blank')
        }

      })
    }
  });
  

}



