function actualizarCreditoVencido() {
  $.ajax({
    type: "POST",
    url: "./modelo/creditos/actualizar-estatus-creditos-vencidos.php",
    data: "data",
    //dataType: "dataType",
    success: function (response) {
    },
  });
}

actualizarCreditoVencido();

function MostrarCreditos() {
  //$.fn.dataTable.ext.errMode = 'none';

  table = $("#creditos").DataTable({
    processing: true,
    serverSide: true,
    ajax: './modelo/creditos/traer-creditos.php',
    rowCallback: function(row, data, index) {
      var info = this.api().page.info();
      var page = info.page;
      var length = info.length;
      var columnIndex = 0; // Índice de la primera columna a enumerar

      $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
    },
    columns: [
      { title: "#", data: null },
      {title: "id",data: 10},
      { title: "cod", data: 0 },
      { title: "Cliente", data: 9 },
      { title: "Fecha inicio", data: 5 },
      { title: "Fecha final", data: 6 },
      { title: "Total", data: 3 },
      { title: "Pagado", data: 1 },
      { title: "Restante", data: 2 },
      {
        title: "Estatus",
        data: null,
        render: function (data) {
          
          switch (data[4]) {
            case "0":
              return '<span class="badge badge-primary">Sin abono</span>';
              break;

            case "1":
              return '<span class="badge badge-info">Primer abono</span>';
              break;
            case "2":
              return '<span class="badge badge-warning">Pagando</span>';
              break;
            case "3":
              return '<span class="badge badge-success">Finalizado</span>';
              break;
            case "4":
              return '<span class="badge badge-danger">Vencido</span>';
              break;
            case "5":
              return '<span class="badge badge-dark">Cancelada</span>';
              break;
            default:
              break;
          }
        },
      },
      {
        title: "Plazo",
        data: null,
        render: function (data) {
          switch (data[7]) {
            case 1:
              return "<span>7 dias</span>";
              break;
            case 2:
              return "<span>15 dias</span>";
              break;
            case 3:
              return "<span>1 mes</span>";
              break;
            case 4:
              return "<span>1 año</span>";
              break;
            case 5:
              return "<span>7 dias</span>";
              break;
              case 6:
                return "<span>1 día</span>";
                break;
            default:
              return ''
              break;
          }
        },
        search: {
          regex: true, // Habilitar búsqueda con expresiones regulares
          smart: false // Deshabilitar el procesamiento inteligente del buscador
          }
      },
      {title: "Venta", data: 11,
      },
      {
        title: "Accion",
        data: null,
        className: "celda-acciones",
        render: function (data) {
          id_sesion = $("#emp-title").attr("sesion_id");

          if (id_sesion == "5" || id_sesion == "6" || id_sesion == "16") {
            //Esta configuracion es especifica para el usuario de Mario, Javier y Amita se debe en un futuro hacer mas dinamico
            return (
              '<div style="display: flex"><button onclick="traerCredito(' + data[0] + ", " + data[8] + ');" type="button" class="buttonPDF btn btn-primary" style="margin-right: 8px"><span class="fa fa-eye"></span><span class="hidden-xs"></span><br>'+
              '<button type="button" onclick="traerPdfCredito(' + data[8] + ');" class="btn ml-2 btn-danger"><span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button></div>'
            );
          } else {
            return (
              '<div style="display: flex"><button onclick="traerCredito(' +data[0] +", " +data[8] +');" type="button" class="buttonPDF btn btn-primary" style="margin-right: 8px"><span class="fa fa-eye"></span><span class="hidden-xs"></span></button><br>'+
              //'<button type="button" onclick="borrarCredito(' + data[0] +');" class="buttonBorrar btn btn-warning"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button><br>'+
              '<button type="button" onclick="traerPdfCredito(' + data[8] + ');" class="btn ml-2 btn-danger"><span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button></div>'
            );
          }
        },
      },
    ],
    paging: true,
    searching: true,
    scrollY: "50vh",
    info: false,
    responsive: true,
    order: [[1, "desc"]],
    'columnDefs': [
      { 'orderData':[2], 'targets': [1] },
      {
          'targets': [2],
          'visible': false,
          'searchable': false
      },
  ]
  });

  $("table.dataTable thead").addClass("table-info");
  //table.columns([3]).visible(false);
}

MostrarCreditos();

function traerPdfCredito(id) {
  window.open(
    "./modelo/creditos/generar-reporte-credito.php?id=" + id,
    "_blank"
  );
}

function borrarCredito(id) {
  Swal.fire({
    title: "Eliminar credito",
    html: "<span>¿Estas seguro de eliminar este credito?</span>",
    showCancelButton: true,
    cancelButtonText: "Cancelar",
    cancelButtonColor: "#00e059",
    showConfirmButton: true,
    confirmButtonText: "Borrar",
    cancelButtonColor: "#ff764d",
    focusConfirm: false,
    customClass: {
      container: 'borrar-credito'
    },
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "post",
        url: "./modelo/creditos/borrar-credito.php",
        data: { id: id },
        success: function (response) {
          if (response == 1) {
            Swal.fire({
              title: "Credito eliminado",
              html: "<span>El credito se eliminó con exito</span>",
              icon: "success",
              cancelButtonColor: "#00e059",
              showConfirmButton: true,
              confirmButtonText: "Aceptar",
              cancelButtonColor: "#ff764d",
            }).then((result) => {
              if (result.isConfirmed) {
                table.ajax.reload(null, false);
              }
            });
          } else {
            Swal.fire({
              title: "Venta no eliminada",
              html: "<span>La venta no se pudo eliminar, dedido a algun error inesperado</span>",
              icon: "warning",
              cancelButtonColor: "#00e059",
              showConfirmButton: true,
              confirmButtonText: "Aceptar",
              cancelButtonColor: "#ff764d",
              showDenyButton: true,
            }).then((result) => {
              if (result.isConfirmed) {
                table.ajax.reload(null, false);
              }
            });
          }
        },
      });
    }
  });
}

function traerCredito(id, id_venta) {
  $.ajax({
    type: "post",
    url: "./modelo/creditos/traer-abonos.php",
    data: { id_credito: id },
    dataType: "JSON",
    success: function (response) {
      Swal.fire({
        title: "Historial de credito",
        heightAuto:false,
        background: "#dcdcdc",
        customClass: {
          popup: 'mostrar-creditos'
        },
        width: "96vw",
        didOpen: function () {
          $(document).ready(function () {
            let rol_usuario = $("#emp-title").attr('sesion_rol');
            let id_usuario = $("#emp-title").attr('sesion_id');
            if(rol_usuario != 1 && id_usuario !=7){
              $("#formas-pago-area-credito").empty();
            }

            $('.selectpicker').selectpicker('refresh');

            restante = $("#restante").val();
            pagado = $("#pagado").val();
            if (restante == "$0.00" && pagado != "$0.00") {
              $("#alerta").empty();
              $("#alerta").append(
                '<div class="alert alert-success" role="alert">' +
                  "Credito pagado" +
                  "</div>"
              );
            }
         

            tabla = $("#tabla-abonos").DataTable({
              //destroy: true,
              //processing: true,
              //serverSide: true,
              ajax: {
                type: "POST",
                data: { id_cred: id },
                url: "./modelo/creditos/traer-abonos.php",
                dataType: "JSON",
              },
              columns: [
                { title: "#", data: null, width: "60px" },
                { title: "Abono", data: "abono" },
                { title: "Fecha", data: "fecha_abono" },
                { title: "Hora", data: "hora_abono" },
                { title: "Efectivo", data: "pago_efectivo"},
                { title: "Tarjeta", data: "pago_tarjeta"},
                { title: "Transferencia", data: "pago_transferencia"},
                { title: "Cheque", data: "pago_cheque"},
                { title: "Deposito", data: "pago_deposito"},
                { title: "Sin definir", data: "pago_sin_definir"},
                { title: "Usuario", data: "usuario" },
                { title: "Comentario", data: "comentario",
                 render: function(data){
                    if(data == null){
                      return ''
                    }else{
                      return data
                    }
                 }},
                { 
                  title: "Accion",
                  data: null,
                  className: "celda-acciones",
                  render: function (row, data, index) {
                    sort = $(".sorting_1").text();
                    if(rol_usuario != 1){
                      return (
                        '<div style="display: flex">'+
                        '<div class="btn btn-danger ml-1" onclick="pdfAbono(' +
                        row.id +
                        ", " +
                        id +
                        ", " +
                        id_venta +
                        ')"><i class="fas fa-file-pdf"></i></div></div>'
                      );
                    }else{
                      return (
                        '<div style="display: flex"><button metodo_efectivo="' +
                        row.pago_efectivo +
                        '" metodo_tarjeta="' +
                        row.pago_tarjeta +
                        '" metodo_transferencia="' +
                        row.pago_transferencia +
                        '" metodo_cheque="' +
                        row.pago_cheque +
                        '" metodo_deposito="' +
                        row.pago_deposito +
                        '" metodo_sin_definir="' +
                        row.pago_sin_definir +
                        '" fecha="' +
                        row.fecha_abono +
                        '" abono="' +
                        row.abono +
                        '" abono_id="' +
                        row.abono_id +
                        '" idrow="' +
                        row.id +
                        '" type="button" class="buttonedit btn btn-primary" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button><br><button type="button" onclick="borrarAbono(' +
                        row.id +
                        ');" class="buttonBorrar btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button>' +
                        '<div class="btn btn-danger ml-1" onclick="pdfAbono(' +
                        row.id +
                        ", " +
                        id +
                        ", " +
                        id_venta +
                        ')"><i class="fas fa-file-pdf"></i></div></div>'
                      );
                    }
                  },
                },
              ],

              paging: false,
              searching: false,
              scrollY: "170px",
              info: false,
              responsive: true,
            });

            tabla
              .on("order.dt search.dt", function () {
                tabla
                  .column(0, { search: "applied", order: "applied" })
                  .nodes()
                  .each(function (cell, i) {
                    cell.innerHTML = i + 1;
                  });
              })
              .draw();

            if (response.estatus == "5") {
              $("#alerta").empty();
              $("#alerta").append(
                '<div class="alert alert-danger" role="alert">' +
                  "Esta venta esta cancelada." +
                  "</div>"
              );
            }

            //Edit
            tabla.on("click", ".buttonedit", function () {
              //tabla.ajax.reload(null,false);
              //$("#tabla-abonos tbody").css("background-color", "whitesmoke");
              $("#cuerpo_edit").empty();
              let $tr = $(this).closest("tr");
              $tr.css("background", "bisque");
              abono = $(this).attr("abono");
              fecha = $(this).attr("fecha");
              var atr_metodo_efectivo = $(this).attr("metodo_efectivo");
              var atr_metodo_tarjeta = $(this).attr("metodo_tarjeta");
              var atr_metodo_transferencia = $(this).attr("metodo_transferencia");
              var atr_metodo_cheque = $(this).attr("metodo_cheque");
              var atr_metodo_deposito = $(this).attr("metodo_deposito");
              var atr_metodo_sin_definir = $(this).attr("metodo_sin_definir");
              id_abono = $(this).attr("abono_id");

              function formatDate(date) {
                var d = new Date(date),
                  month = "" + (d.getMonth() + 1),
                  day = "" + d.getDate(),
                  year = d.getFullYear();

                if (month.length < 2) month = "0" + month;
                if (day.length < 2) day = "0" + day;

                return [year, day, month].join("-");
              }

              fechaFormated = formatDate(fecha);
             
              $("#cuerpo_edit").append(
                '<div class="row">' +
              /*     '<div col="col-12 col-md-3" style="margin-left:20px;">' +
                  'Fecha: <input type="date" id="fecha-abono" class="form-control" value="' +
                  fechaFormated +
                  '"></div>' + */
                  '<div col="col-12 col-md-6" style="margin-left:20px;">' +
                  'Metodo de pago: <select type="text" id="metodo_p" class="selectpicker form-control mb-2" val="hola" multiple onchange="setearFormPagosEditar('+atr_metodo_efectivo+','+atr_metodo_tarjeta+','+atr_metodo_transferencia+','+atr_metodo_cheque+','+atr_metodo_deposito+','+atr_metodo_sin_definir+')">' +
                  '<option value="0">Efectivo</option>' +
                  '<option value="1">Tarjeta</option>' +
                  '<option value="2">Transferencia</option>' +
                  '<option value="3">Cheque</option>' +
                  '<option value="5">Deposito</option>' +
                  '<option value="4">Sin definir</option>' +
                  "</select></div>" +
                  '<div col="col-12 col-md-3">' +
                  '<buttom class="btn btn-warning ml-2 mt-4" name="registrar-abono" onclick="registrarAbonoEditado('+id_abono+','+atr_metodo_efectivo+','+atr_metodo_tarjeta+','+atr_metodo_transferencia+','+atr_metodo_cheque+','+atr_metodo_deposito+','+atr_metodo_sin_definir+')" id="registrar-abono-2">Actualizar</buttom>' +
                  "</div>" +
                  "</div>"+
                  '<div class="row" id="area-metodos-editar">' +
                  "</div>"
              );
              let options_default = [0,1,2,3,5,4];
              $("#metodo_p").val(options_default);
              $('#metodo_p').selectpicker('refresh');
              setearFormPagosEditar(atr_metodo_efectivo,atr_metodo_tarjeta,atr_metodo_transferencia,atr_metodo_cheque, atr_metodo_deposito, atr_metodo_sin_definir)

              /*   let $id = $(this).attr("idrow");
                    let $importe = $(this).attr("importe"); */
            });
          });
          
        },

        html:
          '<div class="row">' +
          '<div class="col-12 col-md-8">' +
          '<div class="form-group">' +
          "<label><b>Cliente:</b></label>" +
          '<input class="form-control" type="text" value="' +
          response.cliente +
          '" id="cliente" name="cliente" disabled>' +
          "</div>" +
          "</div>" +
          '<div class="col-12 col-md-3">' +
         /*  '<div class="form-group">' +
          "<label><b>Fecha nuevo abono</b></label>" +
          '<input type="date" class="form-control" name="fecha" id="fecha">' +
          "</div>" + */
          "</div>" +
          '<div class="col-12 col-sm-3" >' +
          '<form class="mt-4" id="abonos">' +
          '<div class="row">' +
          '<div class="col-12 col-md-12">' +
          '<div class="form-group" id="area-solucion">' +
          "<label><b>Total</b></label>" +
          '<input type="text" class="form-control" value="$' +
          response.total +
          '" name="total" id="total" placeholder="0.00" disabled>' +
          "</div>" +
          "</div>" +
          '<div class="col-12 col-md-12">' +
          '<div class="form-group">' +
          "<label><b>Pagado</b></label>" +
          '<input type="text" class="form-control" value="$' +
          response.pagado +
          '" name="pagado" id="pagado" placeholder="0.00" disabled>' +
          "</div>" +
          "</div>" +
          "</div>" +
          '<div class="col-12 col-md-12">' +
          '<div class="form-group" id="area-solucion">' +
          "<label><b>Restante</b></label>" +
          '<input type="text" class="form-control" value="$' +
          response.restante +
          '" name="restante" id="restante" placeholder="0.00" disabled>' +
          "</div>" +
          "</div>" +
          "</form>" +
          "</div>" +
          '<div class="col-12 col-sm-8 mt-4">' +
          '<div class="row">' +

          '<div class="col-12 col-md-12">' +
          '<div class="form-group" id="formas-pago-area-credito">' +
          "<label><b>Metodo de pago</b></label>" +
          '<select class="form-control selectpicker" multiple id="metodos_pago" name="metodo_pago" onchange="setearFormPagos('+id+')">' +
          '<option value="0">Efectivo</option>' +
          '<option value="1">Tarjeta</option>' +
          '<option value="2">Transferencia</option>' +
          '<option value="3">Cheque</option>' +
          '<option value="5">Deposito</option>' +
          '<option value="4">Sin definir</option>' +
          "</select>" +
          '<div class="invalid-feedback">Sobrepasaste el stock.</div>' +
          "</div>" +
          "</div>" +

          '<div class="col-12 col-md-12">' +
              '<textarea class="form-control" placeholder="Observación del abono" id="observacion-abono"></textarea>' +
          "</div>" +
          '<div id="area-metodos-pagos-creditos" class="col-12 mt-3" style="display:flex;">' +
          "</div>" +
          "</div>" +

          '<div class="row">' +
          '<div class="col-12 col-md-12">' +
          '<span><table id="tabla-abonos" class="table table-primary table-hover table-bordered"></table></span>' +
          "</div>" +
          '<div id="alerta"></div>' +
          '<div class="col-12 col-md-12">' +
          '<div id="cuerpo_edit" style="margin:auto;"></div>' +
          "</div>" +
          "</div>",

        /*   html: '<form class="mt-4" id="formulario-editar-abono">'+
            
                  '<div class="row">'+
                      '<div class="col-8">'+
                      '<div class="form-group">'+
                      '<label><b>Cliente:</b></label></br>'+
                      '<input class="form-control" value="'+ response.cliente +'" disabled>'+
                      '</div>'+
                      '</div>'+
            
                      '<div class="col-4">'+
                      '<div class="form-group">'+
                      '<label for=""><b>Total:</b></label></br>'+
                      '<input type="text" class="form-control" id="total" value="$ '+ response.total+'" autocomplete="off" disabled>'+
                      '</div>'+
                      '</div>'+
                   '</div>'+
            
            
                   '<div class="card tabla-abonos">'+

                   '<div class="row">'+
                   '<div class="col-4">'+
                   '<div id="abonar-btn" class="btn btn-info" style="width: 100px; margin: 10px; ">Abonar<i id="chevron" class="chevron-abaj ml-2 fas fa-chevron-down"></i></div>'+
                   '</div>'+
                   '<div class="col-8">'+
                   '<div id="contenedor-abono" class="form-group" style="display:flex; margin-top:10px; "></div>'+
                   '</div>'+
                   '</div>'+
                   

                   '<div class="row">'+
                   '<div class="col-12 aling-items-center">'+
                   '<table style="margin: 8px;" id="tabla-abonos" class="table table-hover table-bordered">'+  
                   '<thead class="thead-dark"><tr>'+
                   '<th>#</th>'+ 
                   '<th>cantidad</th>'+
                   '<th>Fecha</th>'+
                   '</tr>'+
                   '</thead>'+
                   
                   '<tbody>'+
                   
                   '</tbody>'+
                   '</table>'+
                   '<div id="alerta"></div>'+
                   '</div></div>'+

                          
                   '</div>'+
                   '<div class="row">'+
                   '<div class="col-6 aling-items-center">'+
                   '<div class="mt-2"><b>Pagado:</br> </div>'+
                   '<input value="$'+response.pagado+'" id="pagado" class="form-control" disabled>'+
                   '</div>'+
                   '<div class="col-6 aling-items-center">'+
                   '<div class="mt-2"><b>Restante:</br> </div>'+
                   '<input value="$'+response.restante+'" id="restante" class="form-control" disabled>'+
                   '</div>'+
                   '</div>'+
            
            '</form>', */
        showCancelButton: true,
        cancelButtonText: "Cerrar",
        cancelButtonColor: "#00e059",
        showConfirmButton: false,
        confirmButtonText: "Actualizar",
        cancelButtonColor: "#ff764d",
      }).then((result) => {
        //table.ajax.reload(null, false);
      });

      //table.ajax.reload(null, false);
    },
  });
}

function borrarAbono(id_abono) {
  $.ajax({
    type: "post",
    url: "./modelo/creditos/borrar-abono.php",
    data: { id_abono: id_abono },
    dataType: "JSON",
    success: function (response) {
      tabla.ajax.reload(null, false);
      table.ajax.reload(null, false);
    },
  });
}

function pdfAbono(id_abono, id_credito, id_venta) {
  window.open(
    "./modelo/creditos/reporte-abono-test.php?id=" +
      id_venta +
      "&id_credito=" +
      id_credito +
      "&id_abono=" +
      id_abono,
    "_blank"
  );
}

function setearFormPagos(id_cred){
  let met = $("#metodos_pago").val();

  let area = $("#area-metodos-pagos-creditos");
  let opciones = {
    0: "Efectivo",
    1: "Tarjeta",
    2: "Transferencia",
    3: "Cheque",
    5: "Deposito",
    4: "Sin definir"
  };

  let metKeys = new Set(met); // Crear un conjunto de claves presentes en met
  let valoresGuardados = {}; // Objeto para almacenar los valores de los inputs
    // Guardar los valores de los inputs existentes antes de eliminarlos
    area.find(".col-md-2").each(function () {
      let input = $(this).find("input");
      let inputId = input.attr("id");
      valoresGuardados[inputId] = input.val();
    })
    area.empty(); 
  met.forEach(element => {
    let inputId = `metodo_abono_${element}`;
    let existingInput = area.find(`#${inputId}`);
    if (existingInput.length === 0) {
    area.append(`
    <div class="col-md-2">
      <div class="form-group">
        <label><b>${opciones[element]}</b></label>
        <input type="number" class="form-control" id="metodo_abono_${element}" placeholder="$ 00.00">
      </div>
    </div>    
  `);
}
  });

  area.append(`
    <div class="col-md-2">
      <div class="form-group">
        <buttom class="btn btn-info" style="height:40px; margin-top:27px" name="registrar-abono" id="registrar-abono" onclick="registrarAbono(${id_cred})">Abonar</buttom>
      </div>
    </div>    
  `);

    // Establecer los valores guardados en los nuevos inputs
   
    for (let inputId in valoresGuardados) {
      if (metKeys.has(inputId.split("_")[2])) { // Verificar si la clave está presente en met
        let inputValue = valoresGuardados[inputId];
        $("#" + inputId).val(inputValue);
      }
    }
  
}

function setearFormPagosEditar(atr_metodo_efectivo,atr_metodo_tarjeta,atr_metodo_transferencia,atr_metodo_cheque, atr_metodo_deposito, atr_metodo_sin_definir){
 
  let met = $("#metodo_p").val();
  let area = $("#area-metodos-editar");
  let opciones = {
    0: "Efectivo",
    1: "Tarjeta",
    2: "Transferencia",
    3: "Cheque",
    5: "Deposito",
    4: "Sin definir"
  };
  
  let metKeys = new Set(met); // Crear un conjunto de claves presentes en met
  let valoresGuardados = {}; // Objeto para almacenar los valores de los inputs
    // Guardar los valores de los inputs existentes antes de eliminarlos
    area.find(".col-md-2").each(function () {
      let input = $(this).find("input");
      let inputId = input.attr("id");
      valoresGuardados[inputId] = input.val();
    })
    area.empty(); 
  met.forEach(element => {
    switch(element){
      case "0":
        valor_metodo = atr_metodo_efectivo;
        break;
      case "1":
        valor_metodo = atr_metodo_tarjeta;
        break;
      case "2":
        valor_metodo = atr_metodo_transferencia;
        break;
      case "3":
        valor_metodo = atr_metodo_cheque;
        break;
      case "3":
        valor_metodo = atr_metodo_deposito;
      break;
      case "4":
        valor_metodo = atr_metodo_sin_definir;
        break; 
        default: 0;
        break;       
    }
    let inputId = `metodo_abono_${element}`;
    let existingInput = area.find(`#${inputId}`);
    if (existingInput.length === 0) {
    area.append(`
    <div col="col-12 col-md-2">
    <label><b>${opciones[element]}</b></label>
    <input type="number" class="form-control" id="abono_editar_${element}" placeholder="0.00" value="${valor_metodo}">
    </div> 
  `);
}
  });

    for (let inputId in valoresGuardados) {
      if (metKeys.has(inputId.split("_")[2])) { // Verificar si la clave está presente en met
        let inputValue = valoresGuardados[inputId];
        $("#" + inputId).val(inputValue);
      }
    }
  
}

function registrarAbono(id) {

  var metodos_pago = $("#metodos_pago").val();
  let sumatoria_abonos = 0;
  metodos_pago.forEach(element => {
    let _monto = $(`#metodo_abono_${element}`).val() ? $(`#metodo_abono_${element}`).val() : 0;
    sumatoria_abonos += parseFloat(_monto);
  });   
  let _restante = $("#restante").val();  
  var restante = parseFloat(_restante.replace(/\$/g, ""));
 
  fecha = $("#fecha").val();
  if ($("#restante").val() == "$0.00") {
    $("#alerta").empty();
    $("#alerta").append(
      '<div class="alert alert-success" role="alert">' +
        "El credito ya esta pagado." +
        "</div>"
    );
  } else {
    if (sumatoria_abonos == null || sumatoria_abonos == 0) {
      $("#alerta").empty();
      $("#alerta").append(
        '<div class="alert alert-warning" role="alert">' +
          "Ingresa una cantidad." +
          "</div>"
      );
    } else if (sumatoria_abonos < 0) {
      $("#alerta").empty();
      $("#alerta").append(
        '<div class="alert alert-warning" role="alert">' +
          "No puedes ingresar cantidades negativas." +
          "</div>"
      );
    } else if (metodos_pago.length == 0) {
      $("#alerta").empty();
      $("#alerta").append(
        '<div class="alert alert-warning" role="alert">' +
          "Elige un metodo de pago." +
          "</div>"
      );
    } else if (sumatoria_abonos > restante) {
      $("#alerta").empty();
      $("#alerta").append(
        '<div class="alert alert-warning" role="alert">' +
          "El abono sobrepasa la cantidad restante." +
          "</div>"
      );
    }else {

       //Creando objecto de pagos
       var opciones = {
           0: "Efectivo",
           1: "Tarjeta",
           2: "Transferencia",
           3: "Cheque",
           5: "Deposito",
           4: "Sin definir"
       };
     
      //Transfotmando el arreglo de los metodos
      var metodosPago = []; // Arreglo donde se almacenarán los métodos de pago

       var inputs = document.querySelectorAll('#area-metodos-pagos-creditos input[type="number"]');

       inputs.forEach(function(input) {
     
       var clave =  input.id.split("_").pop(); // Obtener la clave del método de pago del ID del input
     
       var metodo = opciones[clave]; // Obtener el nombre del método de pago según la clave
       let monto_ing = input.value ? input.value : 0;
       var monto = parseFloat(monto_ing); // Obtener el monto ingresado en el input
       
       
       // Crear un objeto con la información del método de pago y el monto
       var metodoPago = {
           clave: clave,
           metodo: metodo,
           monto: monto
       };
       
       metodosPago.push(metodoPago); // Agregar el objeto al arreglo metodosPago
       });
       let comentario_abono = $("#observacion-abono").val();
       let sin_definir_found = metodosPago.find((element) => element.clave == 4);
       if(comentario_abono.trim()=="" && sin_definir_found != undefined) {
        $("#alerta").empty();
        $("#alerta").append(
          '<div class="alert alert-warning" role="alert">' +
            "El forma de pago sin definir debes definir un comentario" +
            "</div>"
        );
       }else{
        $.ajax({
          type: "POST",
          url: "./modelo/creditos/insertar-abono.php",
          data: {
            "id-credito": id,
            metodo: metodosPago,
            fecha: fecha,
            comentario_abono
          },
          dataType: "JSON",
          success: function (response) {
            if (response == 1) {
              $("#alerta").empty();
              $("#alerta").append(
                '<div class="alert alert-warning" role="alert">' +
                  "El abono sobrepasa el total" +
                  "</div>"
              );
            } else if (response == 6) {
              $("#alerta").empty();
              $("#alerta").append(
                '<div class="alert alert-warning" role="alert">' +
                  "Esta venta esta cancelada, no puedes agregar mas abonos." +
                  "</div>"
              );
            } else {
              $("#alerta").empty();
              tabla.ajax.reload(null, false);
              table.ajax.reload(null, false);
              $("#pagado").val(response.pagado_nuevo);
              $("#restante").val(response.restante_nuevo);
            }
          },
        });
       }
    }
  }
}

function registrarAbonoEditado(id, monto1, monto2, monto3, monto4, monto5) {
                
  fecha_actualizada = $("#fecha-abono").val();
  let suma_monto_anterior = parseFloat(monto1) + parseFloat(monto2) + parseFloat(monto3) + parseFloat(monto4) + parseFloat(monto5);
    var metodos_pago = $("#metodo_p").val();
  let sumatoria_abonos = 0;
  metodos_pago.forEach(element => {
    let _monto = $(`#abono_editar_${element}`).val() ? $(`#abono_editar_${element}`).val() : 0;
    sumatoria_abonos += parseFloat(_monto);
  });   
  let _restante = $("#restante").val();  
  var restante = parseFloat(_restante.replace(/\$/g, ""));
  let pagado = parseFloat($("#pagado").val().replace(/\$/g, ""));
  let total_venta = parseFloat($("#total").val().replace(/\$/g, ""));
  let nuevo_pagado = (pagado - suma_monto_anterior) + sumatoria_abonos;
  let nuevo_restante = (total_venta - nuevo_pagado);
  
  if (nuevo_restante < 0) {
    $("#alerta").empty();
    $("#alerta").append(
      '<div class="alert alert-danger" role="alert">' +
        "La suma del abono sobrepasa el total." +
        "</div>"
    );
  } else {
    if (sumatoria_abonos == null || sumatoria_abonos == 0) {
      $("#alerta").empty();
      $("#alerta").append(
        '<div class="alert alert-warning" role="alert">' +
          "Ingresa una cantidad." +
          "</div>"
      );
    } else if (sumatoria_abonos < 0) {
      $("#alerta").empty();
      $("#alerta").append(
        '<div class="alert alert-warning" role="alert">' +
          "No puedes ingresar cantidades negativas." +
          "</div>"
      );
    } else if (metodos_pago.length == 0) {
      $("#alerta").empty();
      $("#alerta").append(
        '<div class="alert alert-warning" role="alert">' +
          "Elige un metodo de pago." +
          "</div>"
      );
    }else {

       //Creando objecto de pagos
      
       var opciones = {
           0: "Efectivo",
           1: "Tarjeta",
           2: "Transferencia",
           3: "Cheque",
           3: "Deposito",
           4: "Sin definir"
       };
     
      //Transfotmando el arreglo de los metodos
      var metodosPago = []; // Arreglo donde se almacenarán los métodos de pago

       var inputs = document.querySelectorAll('#area-metodos-editar input[type="number"]');
     
       inputs.forEach(function(input) {
    
       var clave =  input.id.split("_").pop(); // Obtener la clave del método de pago del ID del input
      
       var metodo = opciones[clave]; // Obtener el nombre del método de pago según la clave
       let monto_ing = input.value ? input.value : 0;
       var monto = parseFloat(monto_ing); // Obtener el monto ingresado en el input
       
       // Crear un objeto con la información del método de pago y el monto

       var metodoPago = {
           clave: clave,
           metodo: metodo,
           monto: monto
       };
       
       metodosPago.push(metodoPago); // Agregar el objeto al arreglo metodosPago
       });

     
       $.ajax({
        type: "POST",
        url: "./modelo/creditos/editar-abono.php",
        data: {
          id: id,
          metodo: metodosPago,
          fecha: fecha_actualizada,
          suma_monto_anterior: suma_monto_anterior,
        },
        dataType: "JSON",
        success: function (responses) {
          if (responses == 0) {
            $("#alerta").empty();
            $("#alerta").append(
              '<div class="alert alert-warning" role="alert">' +
                " El abono sobrepasa la cantidad restante." +
                "</div>"
            );
          } else if (responses == 2) {
            $("#alerta").empty();
            $("#alerta").append(
              '<div class="alert alert-warning" role="alert">' +
                "No ingreses abonos negativos." +
                "</div>"
            );
          } else {
            $("#cuerpo_edit").empty();
  
            tabla.ajax.reload(null, false);
            table.ajax.reload(null, false);
            pagado_response = responses.nuevo_pagado.toFixed(2);
            restante_response = responses.nuevo_restante.toFixed(2);
            $("#restante").val("$ " + restante_response);
            $("#pagado").val("$ " + pagado_response);
            $("#alerta").empty();
            $("#alerta").append(
              '<div class="alert alert-success" role="alert">' +
                "Abono actualizado correctamente." +
                "</div>"
            );
          }
        },
      });
    }
  }

    

}
