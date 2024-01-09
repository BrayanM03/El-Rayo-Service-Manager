$('#cuentas-por-pagar tbody').attr('id', 'tbody-cuentas');
$('#movimientos tbody').attr('id', 'tbody-movimientos');

function procesarFiltros(){
    var tabla_ventas = $('#ventas');

    let fecha_inicial = $("#filtro-fecha-inicial").val()
    let fecha_final = $("#filtro-fecha-final").val()
    let sucursal = $("#buscador-sucursal").val()
    let vendedor = $("#buscador-vendedor").val()
    let cliente = $("#buscador-clientes").val()
    let folio = $("#filtro-folio").val()
    let marca_llanta = $("#buscador-marcas").val() //Multiple values
    let ancho_llanta = $("#Ancho").val()
    let alto_llanta = $("#Proporcion").val()
    let rin_llanta = $("#Diametro").val()
    let filtro_tipo = $("#filtro-tipo").val() //Multiple values
    let filtro_estatus = $("#filtro-estatus").val() //Multiple values
    let filtro_asesor = $("#buscador-asesor").val() //Multiple values

    table.destroy()
    if ($.fn.DataTable.isDataTable( '#ventas' ) ) {
        tabla_ventas.destroy();
      }
    tabla_ventas.empty();
    tabla_ventas.append(`
    <div class="row" style="background-color:white !important;">
        <div class="col-12 col-md-12 text-center"><img src="./src/img/preload.gif" style="width:70px;"><br></img>Buscando...</div>
    </div>
    `);
    $.ajax({
        type: "post",
        url: "./modelo/filtros/aplicar-filtro-ventas.php",
        data: {folio, fecha_final, fecha_inicial, sucursal, vendedor, cliente, marca_llanta, ancho_llanta, alto_llanta, rin_llanta, filtro_estatus, filtro_tipo, filtro_asesor},
        dataType: "JSON",
        success: function (response) {

            if(response.estatus){
                
                //Conversion de arreglo de objectos a arreglos de arrays
                response.data = response.data.length == 0 ? [] : response.data;
                const data_convertida = response.data.map(objeto => [
                    '',
                    objeto.id,
                    objeto.Fecha,
                    objeto.sucursal,
                    objeto.vendedor,
                    objeto.cliente,
                    objeto.tipo,
                    objeto.Total,
                    objeto.estatus
                ]);
                clearTimeout();
                setTimeout(function(){
                tabla_ventas.empty();
                tabla_ventas.DataTable({    
                rowCallback: function(row, data, index) {
                    var info = this.api().page.info();
                    var page = info.page;
                    var length = info.length;
                    var columnIndex = 0; // Índice de la primera columna a enumerar
              
                    $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
                  },
                "bDestroy": true,
                columns: [   
                { title: '#' },
                { title: 'Folio'},
                {title:  'Fecha'},
                { title: 'Sucursal'},
                { title: 'Vendedor'},
                { title: 'Cliente'},
                { title: 'Tipo'},
                { title: 'Total'},
                { title: 'Estatus'},
                { title: 'Accion', 
                data: null,
                className: "celda-acciones",
                render: function (row, data) {
                  rol = $("#titulo-hv").attr("rol");
                  
                 if(rol == "1"){
                      if (row[6] == "Credito") {
                          return '<div style="display: flex; width: auto;">'+
                          '<button onclick="traerPdfCredito(' +row[1]+ ');" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px">'+
                          '<span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>'+
                          '<button type="button" onclick="cancelarVenta('+ row[1] +');" class="buttonBorrar btn btn-primary">'+
                          '<span class="fa fa-ban"></span><span class="hidden-xs"></span></button>'+
                          '<button type="button" onclick="redirigirCredito('+ row[1] +');" class="buttonBorrar btn btn-info" style="margin-left: 8px">'+
                          '<span class="fa fa-share-square"></span><span class="hidden-xs"></span></button>'+
                          '<button type="button" onclick="borrarVenta('+ row[1] +',2);" class="buttonBorrar btn btn-warning" style="margin-left: 8px">'+
                          '<span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
                          
                      }else if(row[6] == "Normal"){
                          return '<div style="display: flex; width: auto;">'+
                          '<button onclick="traerPdf(' +row[1]+ ');" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px">'+
                          '<span class="fa fa-file-pdf"></span><s1an class="hidden-xs"></span></button><br>'+
                          '<button type="button" onclick="cancelarVenta('+ row[1] +');" class="buttonBorrar btn btn-primary">'+
                          '<span class="fa fa-ban"></span><span class="hidden-xs"></span></button>'+
                          '<button type="button" onclick="borrarVenta('+ row[1] +',1);" class="buttonBorrar btn btn-warning" style="margin-left: 8px">'+
                          '<span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
                   
                      }else if(row[6] == ""){
                          return '<div style="display: flex; width: auto;">'+
                          '<button onclick="traerPdf(' +row[1]+ ');" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px">'+
                          '<span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>'+
                          '<button type="button" onclick="cancelarVenta('+ row[1] +');" class="buttonBorrar btn btn-primary">'+
                          '<span class="fa fa-ban"></span><span class="hidden-xs"></span></button>'+
                          '<button type="button" onclick="borrarVenta('+ row[1] +');" class="buttonBorrar btn btn-warning" style="margin-left: 8px">'+
                          '<span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
                      }else if(row[6] == "Apartado"){
                          return '<div style="display: flex; width: auto;">'+
                          '<button onclick="traerPdfApartado(' +row[1]+ ');" title="Ver reporte" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px">'+
                          '<span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>'+
                          '<button type="button" onclick="cancelarVenta('+ row[1] +');" class="buttonBorrar btn btn-primary">'+
                          '<span class="fa fa-ban"></span><span class="hidden-xs"></span></button>'+
                          '<button type="button" onclick="borrarVenta('+ row[1] +',1);" class="buttonBorrar btn btn-warning" style="margin-left: 8px">'+
                          '<span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
                      }else if(row[6] == "Pedido"){
                          return '<div style="display: flex; width: auto;">'+
                          '<button onclick="traerPdfPedido(' +row[1]+ ');" title="Ver reporte" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px">'+
                          '<span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>'+
                          '<button type="button" onclick="cancelarVenta('+ row[1] +');" class="buttonBorrar btn btn-primary">'+
                          '<span class="fa fa-ban"></span><span class="hidden-xs"></span></button>'+
                          '<button type="button" onclick="borrarVenta('+ row[1] +',1);" class="buttonBorrar btn btn-warning" style="margin-left: 8px">'+
                          '<span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
                      }else{
                          return '';
                      }
                  }else{
                      if (row[6] == "Credito") {
                          return '<div style="display: flex"><button onclick="traerPdfCredito(' +row[1]+ ');" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px"><span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>';
                          
                      }else if(row[6] == "Normal"){
                          return '<div style="display: flex"><button onclick="traerPdf(' +row[1]+ ');" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px"><span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>';
                   
                      }else if(row[6] == "Apartado"){
                          return '<div style="display: flex"><button onclick="traerPdfApartado(' +row[1]+ ');" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px"><span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>';
                      }else if(row[6] == "Pedido"){
                          return '<div style="display: flex"><button onclick="traerPdfPedido(' +row[1]+ ');" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px"><span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>';
                      }else if(row.estatus == "Cancelada"){
                          
                      }
                  }
                  
                   },},
                ],
                data: data_convertida,
                paging: true,
                searching: true,
               // scrollY: "50vh",
                info: false,
                responsive: false,
                ordering: "enable",
                multiColumnSort: true,
              });
            
              $("table.dataTable thead").addClass("table-dark")
              $("table.dataTable thead").addClass("text-white")
            
            },500);
          }else{
            tabla_ventas.empty()
            .append(`
              <div class="row" style="background-color:white !important;">
                  <div class="col-12 col-md-12 text-center">No se encontraron resultados</div>
              </div>
              `);
            Swal.fire({
                icon:'error',
                html:`
                    Ocurrio un error, los filtros arrojaron un error en la consulta
                `
            })
          }
        }
    });

    
}

function descargarReporteFiltro(){

    let fecha_inicial = $("#filtro-fecha-inicial").val()
    let fecha_final = $("#filtro-fecha-final").val()
    let sucursal = $("#buscador-sucursal").val()
    let vendedor = $("#buscador-vendedor").val() //Multiple values
    let cliente = $("#buscador-clientes").val()
    let folio = $("#filtro-folio").val()
    let marca_llanta = $("#buscador-marcas").val() //Multiple values
    let ancho_llanta = $("#Ancho").val()
    let alto_llanta = $("#Proporcion").val()
    let rin_llanta = $("#Diametro").val()
    let filtro_tipo = $("#filtro-tipo").val() //Multiple values
    let filtro_estatus = $("#filtro-estatus").val() //Multiple values
    let filtro_asesor = $("#buscador-asesor").val() //Multiple values
    // Construir la URL con los parámetros
    let url = `./modelo/filtros/descargar-filtro-ventas.php?folio=${folio}&fecha_final=${fecha_final}&fecha_inicial=${fecha_inicial}&sucursal=${sucursal}&vendedor=${vendedor}&cliente=${cliente}&marca_llanta=${marca_llanta}&ancho_llanta=${ancho_llanta}&alto_llanta=${alto_llanta}&rin_llanta=${rin_llanta}&filtro_estatus=${filtro_estatus}&filtro_tipo=${filtro_tipo}&filtro_asesor=${filtro_asesor}`;

    // Abrir una nueva ventana o pestaña del navegador con la URL construida
    window.open(url);
/* 
    $.ajax({
        type: "post",
        url: "./modelo/filtros/descargar-filtro-ventas.php",
        data: {folio, fecha_final, fecha_inicial, sucursal, vendedor, cliente, marca_llanta, ancho_llanta, alto_llanta, rin_llanta, filtro_estatus, filtro_tipo},
        dataType: "JSON",
        success: function (response) {

        }
    }); */


}

function procesarFiltrosMovimientos(){
    var tabla = $('#movimientos');
    let fecha_inicial = $("#filtro-fecha-inicial").val()
    let fecha_final = $("#filtro-fecha-final").val()
    let sucursal_ubicacion = $("#sucursal-ubicacion").val()
    let sucursal_destino = $("#sucursal-destino").val()
    let folio = $("#filtro-folio").val()
    let factura = $("#filtro-factura").val()
    let marca_llanta = $("#buscador-marcas").val() //Multiple values
    let ancho_llanta = $("#Ancho").val()
    let alto_llanta = $("#Proporcion").val()
    let rin_llanta = $("#Diametro").val()
    let filtro_tipo = $("#filtro-tipo").val() //Multiple values
    let filtro_estatus = $("#filtro-estatus").val() //Multiple values
    let filtro_proveedor = $("#buscador-proveedor").val() //Multiple values
    let filtro_estado = $("#filtro-estado").val() //
    $('#movimientos tbody').attr('id', 'tbody-movimientos');
    $('#tbody-movimientos').empty();
    $('#tbody-movimientos').append(`
    <tr>
    <td colspan="13" style="margin: 0px important!; padding:0px !important;">
    <div class="row" style="background-color:white !important;">
        <div class="col-12 col-md-12 text-center"><img src="./src/img/preload.gif" style="width:70px;"><br></img>Buscando...</div>
    </div>
    </td>
    </tr>
    `);
    $.ajax({
        type: "post",
        url: "./modelo/filtros/aplicar-filtro-movimientos.php",
        data: {folio, factura, fecha_final, fecha_inicial, sucursal_ubicacion, sucursal_destino, marca_llanta, ancho_llanta, alto_llanta, rin_llanta, filtro_estatus, filtro_tipo, filtro_proveedor, filtro_estado},
        dataType: "JSON",
        success: function (response) {

            if(response.estatus){
                //Conversion de arreglo de objectos a arreglos de arrays
                response.data = response.data.length == 0 ? [] : response.data;
                const data_convertida = response.data.map(objeto => [
                    '',
                    objeto.id,
                    objeto.descripcion,
                    objeto.mercancia,
                    objeto.nombre,
                    objeto.folio_factura,
                    objeto.estado_factura,
                    objeto.fecha,
                    objeto.tipo,
                    objeto.estatus,
                    objeto.usuario
                ]);
                clearTimeout();
                setTimeout(function(){
                  if ($.fn.DataTable.isDataTable( '#movimientos' ) ) {
                    $("#contenedor-movimientos").empty().append(`
                    <table id="movimientos" class="table table-striped table-bordered table-hover">                   
                    </table>
                    `);
                    //tabla.destroy();
                  }
                  var tabla = $("#movimientos")
                  tabla.empty();
                tabla.DataTable({    
                rowCallback: function(row, data, index) {
                    var info = this.api().page.info();
                    var page = info.page;
                    var length = info.length;
                    var columnIndex = 0; // Índice de la primera columna a enumerar
              
                    $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
                  },
                "bDestroy": true,
                columns: [   
                { title: '#' },
                { title: 'Folio'},
                {title:  'Descripcion'},
                { title: 'Mercancia'},
                { title: 'proveedor',   render:(data, row)=>{
                    if(data == null){
                      var prov = 'NA'
                    }else{
                      var prov = data;
                    }
                    return prov;
                  }},
                { title: 'Factura'},
                { title: 'Estado fact.', render(data, row) {
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
                { title: 'Fecha'},
                { title: "Tipo", render(data, row) {
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
                { title: 'Estatus'},
                { title: 'Usuario'},
                { title: "Accion",
                    data: null,
                    className: "celda-acciones",
                    render: function (row, data) {
                        if(row[9]=='Completado'){
                        class_btn_check = 'btn-secondary disabled';
                        candado ='ss';
                        }else{
                        candado = '';
                        class_btn_check = 'btn-success';
                        }
                        if(row[8] == 1 || row[8] ==3 || row[8] ==4){
                        return `
                        <div style="display:flex;">
                            <div class="btn btn-danger mr-2" onclick="remisionSalida(${row[1]})"><i class="fas fa-file-pdf"></i></div>
                            <div class="btn ${class_btn_check}" onclic${candado}k="AprobarMovimiento(${row[1]})"><i class="fas fa-check" disabled></i></div>
                        </div>
                            `;
                        }else if(row[8] ==2){
                        return `
                        <div style="display:flex;">
                            <div class="btn btn-danger mr-2" onclick="remisionIngreso(${row[1]})"><i class="fas fa-file-pdf"></i></div>
                            <div class="btn ${class_btn_check}" onclick="AprobarMovimiento(${row[1]})"><i class="fas fa-check"></i></div>
                        </div>`;

                        }else{
                        return `<span>No disp</span>`;
                        }
                    },
                    }, 
                ],
                data: data_convertida,
                paging: true,
                searching: true,
                scrollY: "50vh",
                info: false,
                responsive: false,
                order: [1, "desc"],
              });},500);
          }else{
            tabla.empty()
            .append(`
              <div class="row" style="background-color:white !important;">
                  <div class="col-12 col-md-12 text-center">No se encontraron resultados</div>
              </div>
              `);
            Swal.fire({
                icon:'error',
                html:`
                    Ocurrio un error, los filtros arrojaron un error en la consulta
                `
            })
          }
        }
    });

    
}

function procesarFiltrosCuentasPorPagar(){

  let fecha_inicial = $("#filtro-fecha-inicial").val()
  let fecha_final = $("#filtro-fecha-final").val()
  let sucursal_ubicacion = $("#sucursal-ubicacion").val()
  let sucursal_destino = $("#sucursal-destino").val()
  let folio = $("#filtro-folio").val()
  let factura = $("#filtro-factura").val()
  let marca_llanta = $("#buscador-marcas").val() //Multiple values
  let ancho_llanta = $("#Ancho").val()
  let alto_llanta = $("#Proporcion").val()
  let rin_llanta = $("#Diametro").val()
  let filtro_tipo = $("#filtro-tipo").val() //Multiple values
  let filtro_estatus = $("#filtro-estatus").val() //Multiple values
  let filtro_proveedor = $("#buscador-proveedor").val() //Multiple values
  let filtro_estado = $("#filtro-estado").val() //
  

  $('#cuentas-por-pagar tbody').attr('id', 'tbody-cuentas');
  $('#tbody-cuentas').empty();
  $('#tbody-cuentas').append(`
  <tr>
  <td colspan="14" style="margin: 0px important!; padding:0px !important;">
  <div class="row" style="background-color:white !important;">
      <div class="col-12 col-md-12 text-center"><img src="./src/img/preload.gif" style="width:70px;"><br></img>Buscando...</div>
  </div>
  </td>
  </tr>
  `);
  //return false;
    $.ajax({
      type: "post",
      url: "./modelo/filtros/aplicar-filtro-cuentas.php",
      data: {folio, factura, fecha_final, fecha_inicial, sucursal_ubicacion, sucursal_destino, marca_llanta, ancho_llanta, alto_llanta, rin_llanta, filtro_estatus, filtro_tipo, filtro_proveedor, filtro_estado},
      dataType: "JSON",
      success: function (response) {
        if(response.estatus){
              //Conversion de arreglo de objectos a arreglos de arrays
              response.data = response.data.length == 0 ? [] : response.data;
              const data_convertida = response.data.map(objeto => [
                  null,
                  objeto.id,
                  objeto.mercancia,
                  objeto.nombre,
                  objeto.folio_factura,
                  objeto.estado_factura,
                  objeto.total,
                  objeto.pagado,
                  objeto.restante,
                  objeto.fecha,
                  objeto.hora,
                  objeto.tipo,
                  objeto.usuario
              ]);
             
              setTimeout(function(){
                if ($.fn.DataTable.isDataTable( '#cuentas-por-pagar' ) ) {
                  console.log('Entre');
                    $("#contenedor-cuentas-pagar").empty().append(`
                      <table id="cuentas-por-pagar" class="table table-striped table-bordered table-hover">                   
                      </table>
                      `);
                    //tabla.destroy();
                  }
              var tabla = $('#cuentas-por-pagar');
              tabla.empty();
              //var tabla = $('#cuentas-por-pagar');
              tabla.DataTable({    
              rowCallback: function(row, data, index) {
                  var info = this.api().page.info();
                  var page = info.page;
                  var length = info.length;
                  var columnIndex = 0; // Índice de la primera columna a enumerar
            
                  $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
                },
               
               "bDestroy": true,
              columns: [   
                { title: "#",              data: null},
                { title: "Folio",          data: 1 },
                { title: "mercancia",      data: 2},
                { title: 'proveedor',      data: 3, render:(data, row)=>{
                  if(data == null){
                    var prov = 'NA'
                  }else{
                    var prov = data;
                  }
                  return prov;
                }},
                { title: 'factura',        data: 4},
                { title: 'Estado fact.', data:5, render(data, row) {
                  if(data == 1){
                    estado_factura_ = 'Sin factura';
                  }else if(data == 2){
                    estado_factura_ = 'Factura completa';
                  }else if(data == 3){
                    estado_factura_ = 'Factura incompleta';
                  }else if(data == 4){
                    estado_factura_ = '<span class="badge bg-success p-2 text-white">Factura pagada</span>';
                  }else{
                    estado_factura_ = 'No aplica'
                  }
                  return estado_factura_;
                }},
                { title: "total",          data: 6, render: function(data){
                    return new Intl.NumberFormat().format(data)
                } },
                { title: "pagado",          data: 7, render: function(data){
                  return new Intl.NumberFormat().format(data)
              } },
              { title: "restante",          data: 8, render: function(data){
                return new Intl.NumberFormat().format(data)
            } },
                { title: "fecha",          data: 9 },
                { title: "hora",           data: 10 },
                { title: "tipo",           data: 11, render(data, row) {
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
                { title: "usuario",        data: 12 },
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
                    if(row[11] == 1 || row[6] ==3 || row[6] ==4){
                      return `
                      <div style="display:flex;">
                          <div class="btn btn-danger mr-2" onclick="remisionSalida(${row[0]})"><i class="fas fa-file-pdf"></i></div>
                          <div class="btn ${class_btn_check}" onclic${candado}k="administrarCuenta(${row[1]}, ${row[11]})"><i class="fas fa-check" disabled></i></div>
                      </div>
                          `;
                    }else if(row[11] ==2){
                      return `
                      <div style="display:flex;">
                         <div class="btn btn-danger mr-2" onclick="remisionIngreso(${row[0]})"><i class="fas fa-file-pdf"></i></div>
                         <div class="btn ${class_btn_check}" onclick="administrarCuenta(${row[1]},  ${row[11]})"><i class="fas fa-check"></i></div>
                      </div>`;
            
                    }else{
                      return `<span>No disp</span>`;
                    }
                  },
                }, 
              ],
              columnDefs:[{
                width: '50px',
                targets:0
              }],
              data: data_convertida,
              paging: true,
              searching: true,
              scrollY: "50vh",
              info: false,
              order: [1, "desc"],
            });
          
            $("table.dataTable thead").addClass("table-dark")
            $("table.dataTable thead").addClass("text-white")
          },500);
        }else{
          var tabla = $('#cuentas-por-pagar');
          tabla.empty()
          .append(`
            <div class="row" style="background-color:white !important;">
                <div class="col-12 col-md-12 text-center">No se encontraron resultados</div>
            </div>
            `);
          Swal.fire({
              icon:'error',
              html:`
                  Ocurrio un error, los filtros arrojaron un error en la consulta
              `
          })
        }
        
      }
  });
  

  
}