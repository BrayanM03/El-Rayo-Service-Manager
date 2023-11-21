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
              });},500);
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
    console.log(filtro_asesor);
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