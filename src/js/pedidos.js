toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": false,
  "progressBar": true,
  "positionClass": "toast-bottom-right",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}

MostrarPedidos();
const meses = [
  "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
  "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
];

function procesarApartado(){
    let metodos_pagos = $("#metodos-pago").val();  

    if ( !table.data().any()){
      
      toastr.warning('La tabla no tiene productos', 'Sin productos' ); 
    }else{
      if(metodos_pagos.length == 0){
          toastr.warning('Agrega un metodo de pago', 'Sin metodo pago' ); 
      }else{
        llantaData = $("#pre-venta").dataTable().fnGetData();
        designarAdelanto(metodos_pagos, llantaData);
      }
     /* 
        var opciones = {
          0: "Efectivo",
          1: "Tarjeta",
          2: "Transferencia",
          3: "Cheque",
          4: "Sin definir"
        };

        let metodos_formateado = metodos_pagos.reduce(function(result, key) {
          let monto_total = $("#total").val();
          result[key] = {"id_metodo":key, "metodo":opciones[key], "monto": monto_total};
          return result;
        }, {}); */
        
      
    }  
  }

function designarAdelanto(metodo_pago, llantaData){

    Swal.fire({
      title: "Monto del adelanto",
      background: "#dcdcdc" ,
      width: '800px',
      showCancelButton: true,
      cancelButtonText: 'Cerrar',
      cancelButtonColor: '#00e059',
      showConfirmButton: true,
      confirmButtonText: 'Realizar apartado', 
      cancelButtonColor:'#ff764d',
      html: `
      <div class="container">
          <div id="contenedor-metodos">
          </div>
      </div>`,
      didOpen: function () { 
        let button_confirm = document.querySelector('.swal2-confirm');
         button_confirm.style.backgroundColor = '#858796';  
        $("#contenedor-metodos").empty();
        var opciones = {
          0: "Efectivo",
          1: "Tarjeta",
          2: "Transferencia",
          3: "Cheque",
          5: "Deposito",
          4: "Sin definir"
        };

          var importe_total_actual =  llantaData.reduce(function(total, element) {
          let total_importe = parseFloat(element.importe) + parseFloat(total);
          return  total_importe;
        }, 0);

        var arregloMetodos= metodo_pago.reduce(function(result, key) {
          result[key] = opciones[key];
          return result;
        }, {});
     
        for(var clave in arregloMetodos) {
          if (arregloMetodos.hasOwnProperty(clave)) {
            var nombre_metodo = arregloMetodos[clave];
            $("#contenedor-metodos").append(`
              <div class="row mt-2">
              <div class="col-md-12">
                  <label>Monto para pago ${nombre_metodo}</label>
                  <input type="number" class="form-control" id="monto_metodo_${clave}" onkeyup="calcularMontosAdelanto(${importe_total_actual})" placeholder="0.00">
              </div>
              </div>
        `);}
          }

          $("#contenedor-metodos").append(`
          <div class="row mt-3">
          <div class="col-md-6">
              <label>Importe total</label>
              <h1><span class="badge badge-info" id="badge-total">$${importe_total_actual}</span><h1>
              <input type="hidden" value="${importe_total_actual}" class="form-control" id="total_importe" disabled>
          </div>
          <div class="col-md-6">
              <label>Restante</label>
              <h1><span class="badge badge-secondary" id="badge-restante">$${importe_total_actual}</span><h1>
              <input type="hidden" value="${importe_total_actual}" class="form-control" is-valid="false" id="total_restante" disabled>
          </div>
          <div class="col-md-12">
              <h4 id="validador-adelanto"><span id="text-message" class="text-secondary"></span><h4>
          </div>
          </div>
          `) 
          calcularMontosAdelanto(importe_total_actual)
        },
        preConfirm: function(){
        if($("#validador-adelanto").attr("is-valid") == "false"){
          Swal.showValidationMessage(
            `No se puede apartar, corrija los montos`
          )
        }
        }
      
    }).then(function (ress) {
      if(ress.isConfirmed){
        var opciones = {
          0: "Efectivo",
          1: "Tarjeta",
          2: "Transferencia",
          3: "Cheque",
          5: "Deposito",
          4: "Sin definir"
        };

        var arregloMetodos= metodo_pago.reduce(function(result, key) {
          let monto = parseFloat(document.getElementById(`monto_metodo_${key}`).value);
          result[key] = {"id_metodo":key, "metodo":opciones[key], "monto": monto};
          return result;
        }, {});
         realizarApartado(arregloMetodos);
      }
    })
    
   }  

function realizarApartado(metodos_pagos){
    if ( !table.data().any()){

        toastr.warning('La tabla no tiene productos', 'Sin productos' ); 

    }else{

        $("#realizar-venta").addClass("disabled");;
        $("#realizar-venta").text("Espere...");

        llantaData = $("#pre-venta").dataTable().fnGetData();
          
        total = $("#total").val();
        fecha = $("#fecha").val(); 
        cliente = $("#select2-clientes-container").attr("id-cliente");
        tienda = $("#sucursal").val();
        comentario = $("#hacer-comentario").attr("comentario");
        restante = $("#total_restante").val();
        //Enviando data
        
        $.ajax({
            type: "POST",
            url: "./modelo/apartados/realizar-apartado.php", 
            data: {'data': llantaData,
                   'cliente': cliente,
                   'metodos_pago': metodos_pagos,
                   'fecha': fecha,
                   'sucursal': tienda,
                   'restante' : restante,
                   'total': total,
                   'comentario': comentario,
                   'tipo': 'apartado',
                   'plazo': '1 mes'},
            dataType: "JSON",
            success: function (response) {
               
                if (response) {
                    Swal.fire({
                        title: 'Apartado realizado',
                        html: "<span>El apartado se realizó con exito</br></span>"+
                        "ID Venta: AP" + response,
                        icon: "success",
                        cancelButtonColor: '#00e059',
                        showConfirmButton: true,
                        confirmButtonText: 'Aceptar', 
                        cancelButtonColor:'#ff764d',
                        showDenyButton: true,
                        allowOutsideClick: false,
                        denyButtonText: 'Reporte'
                    },
                       
                      ).then((result) =>{
          
                        if(result.isConfirmed){
                           //location.reload();
                           table.ajax.reload(null,false);
                            $("#pre-venta tbody tr").remove();
                            $(".pre-venta-error").html("");
                            $(".products-grid-error").remove();
                            $("#pre-venta tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
                            $("#pre-venta_processing").css("display","none");
                            $("#total").val(0);
                            table.clear().draw();

                            $("#realizar-venta").removeClass("disabled");
                            $("#realizar-venta").text("Realizar venta");

                            borrarFormulario();
                           

                        }else if(result.isDenied){

                            window.open('./modelo/apartados/reporte-apartado.php?id='+ response, '_blank');
                            table.ajax.reload(null,false);
                            $("#pre-venta tbody tr").remove();
                            $(".pre-venta-error").html("");
                            $(".products-grid-error").remove();
                            $("#pre-venta tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
                            $("#pre-venta_processing").css("display","none");
                            $("#total").val(0);
                            table.clear().draw();


                            $("#realizar-venta").removeClass("disabled");
                            $("#realizar-venta").text("Realizar venta");
                                 
                            borrarFormulario();
                            
                        }else{
                            table.ajax.reload(null,false);
                            $("#pre-venta tbody tr").remove();
                            $(".pre-venta-error").html("");
                            $(".products-grid-error").remove();
                            $("#pre-venta tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
                            $("#pre-venta_processing").css("display","none");
                            $("#total").val(0);
                            table.clear().draw();


                            $("#realizar-venta").removeClass("disabled");
                            $("#realizar-venta").text("Realizar venta");

                            borrarFormulario();
                        }
        
                       $("#hacer-comentario").attr("comentario", " ");
                        });

                        
                }
                
            }
        }); 

    }
};

function calcularMontosAdelanto(importe){
   
  let button_confirm = document.querySelector('.swal2-confirm');
    var inputs = document.querySelectorAll("#contenedor-metodos input[type=number]");  // Obtener todos los inputs
    var resta = 0;
    var sumatoria_monto = 0;
    inputs.forEach(function(input) {
      var valor = parseFloat(input.value);
      if (isNaN(valor)) {
        //valor = valor == '' ? 0 : valor
       valor = 0;
      }
      sumatoria_monto += valor; 
      resta = importe - sumatoria_monto;
    });
    

    // Verificar si la suma es igual al precio_llanta y actualizar el badge
    var badgeRestante = document.getElementById("badge-restante");
    var text_message = document.getElementById("text-message");

    // Calculamos los montos minimos del adelanto
    if(importe < 3000){
      var porcentaje = 0.10;
    }else if(importe >= 3000 && importe < 10000){
      var porcentaje = 0.15;
    }else if(importe >= 10000 && importe < 20000){
      var porcentaje = 0.20;
    }else if(importe >= 20000){
      var porcentaje = 0.25;
    }

    const porcentajeFormateado = (porcentaje * 100).toFixed(0) + '%';
    const monto_minimo = importe * porcentaje;

    //Codigo para obtener el monto minimo del 24% del monto total
    const redondeado = Math.round(monto_minimo + 0.001);
    const resta_redondeada = Math.round(resta + 0.001);
    const formatter = new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
      });
    const monto_formateado = formatter.format(redondeado);
    const resta_formateada = formatter.format(resta_redondeada);

    if (sumatoria_monto < redondeado) {
      badgeRestante.classList.remove("badge-success");
      badgeRestante.classList.remove("badge-danger");
      badgeRestante.classList.add("badge-secondary");


      button_confirm.style.backgroundColor = '#858796';
      button_confirm.style.borderColor = '#858796';
      text_message.classList.remove("text-success");
      text_message.classList.remove("text-danger");
      text_message.classList.add("text-secondary");
      
      text_message.textContent = `Agregue un monto minimo del ${porcentajeFormateado} = ${monto_formateado}`;
      $("#validador-adelanto").attr("is-valid", "false")
          
    }else if(resta < 0){
      
      badgeRestante.classList.remove("badge-success");
      badgeRestante.classList.remove("badge-secondary");
      badgeRestante.classList.add("badge-danger");

      button_confirm.style.backgroundColor = '#dc3545';
      button_confirm.style.borderColor = '#dc3545';
      text_message.classList.remove("text-success");
      text_message.classList.remove("text-secondary");
      text_message.classList.add("text-danger");
      text_message.textContent = 'El resta es menor que el total';
      $("#validador-adelanto").attr("is-valid", "false")
    }else if(sumatoria_monto >= redondeado){

      badgeRestante.classList.remove("badge-secondary");
      badgeRestante.classList.remove("badge-danger");
      badgeRestante.classList.add("badge-success");

      button_confirm.style.backgroundColor = '#1cc88a';
      button_confirm.style.borderColor = '#1cc88a';
      text_message.classList.remove("text-success");
      text_message.classList.remove("text-danger");
      text_message.classList.add("text-secondary");

      text_message.textContent = '';
      $("#validador-adelanto").attr("is-valid", "true")
      audio_2.play();  
    }
    $("#badge-restante").empty().append(`${resta_formateada}`);
    $("#total_restante").val(resta_redondeada);
}

function MostrarPedidos() {  
  //$.fn.dataTable.ext.errMode = 'none';
  ocultarSidebar();
table = $('#pedidos').DataTable({
    
  processing: true,
  serverSide: true,
  ajax: './modelo/pedidos/historial-pedidos.php',
  rowCallback: function(row, data, index) {
      var info = this.api().page.info();
      var page = info.page;
      var length = info.length;
      var columnIndex = 0; // Índice de la primera columna a enumerar
      
      $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
      if(data[19] == 1){
        $(row).css('background-color','#c0f6b4')
      }else{
        $(row).css('background-color','#ffffbf')
      }
    },
   
  columns: [   
  { title: "#",              data: null     },
  { title: "Folio",          data: 1        },
  { title: "Cliente",        data: 2       },
  { title: "Fecha inicio",   data: 3 },
  { title: "Hora inicio",    data: 5        },
  { title: "Fecha final",    data: 4,  render: function(data, type, row) {
    if (data == null) {
      return '-';
    } else {
      return data;
    }
  }},
  { title: "Hora final",     data: 6, render: function(data, type, row) {
    if (data == null) {
      return '-';
    } else {
      return data;
    }
  }     },
  { title: "Sucursal",       data: 7        }, 
  { title: "Abonado",        data: 17        }, 
  { title: "Restante",       data: 18        }, 
  { title: "Total",          data: 8        },
  { title: "Estatus",        data: 9       }, 
  { title: "Tipo",           data: 10       }, 
  { title: "Comentario",     data: 11      },
  { title: "Vendedor",       data: 12        },
  { title: "Accion",
    data: null,
    className: "celda-acciones",
    render: function (row, data) {
      rol = $("#titulo-hv").attr("rol");

      if(rol == "1" || rol == '2'){
          if (row[9] == "Activo") {
            
              return '<div style="display: flex; width: auto;">'+
              '<button onclick="traerPdfApartado(' +row[1]+ ');" title="Ver reporte" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px">'+
              '<span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>'+
              '<button type="button" onclick="cancelarApartadox('+ row[1] +');" title="Cancelar apartado" class="buttonBorrar btn btn-primary">'+
              '<span class="fa fa-ban"></span><span class="hidden-xs"></span></button>'+
              '<button type="button" onclick="procesarOrden('+ row[1] +');" title="Procesar venta" class="buttonBorrar btn btn-success" style="margin-left: 8px">'+
              '<span class="fa fa-check"></span><span class="hidden-xs"></span></button>'+
              '<button type="button" onclick="confirmarPedido('+ row[1] +', '+row[19]+');" title="Procesar venta" class="buttonBorrar btn btn-info" style="margin-left: 8px">'+
              '<span class="fa fa-thumbs-up"></span><span class="hidden-xs"></span></button></div>';;
              
          }else if(row[9] == "Cancelada"){
              return '<div style="display: flex; width: auto;">'+
              '<button onclick="traerPdfApartado(' +row[1]+ ');" title="Ver reporte" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px">'+
              '<span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>'+
              '<button type="button" title="Ya esta cancelada" onclick="ventaYaCancelada()" class="buttonBorrar btn btn-secondary">'+
              '<span class="fa fa-ban"></span><span class="hidden-xs"></span></button></div>';
          }else{
            return '';
          }
      }else{
        if (row[9] == "Activo") {
          return '<div style="display: flex; width: auto;">'+
          '<button onclick="traerPdfApartado(' +row[1]+ ');" title="Ver reporte" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px">'+
          '<span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>'+
          '<button type="button" onclick="procesarOrden('+ row[1] +');" title="Procesar venta" class="buttonBorrar btn btn-success" style="margin-left: 8px">'+
          '<span class="fa fa-check"></span><span class="hidden-xs"></span></button>'+
          '</div>'
        }else if(row[9] == "Cancelada"){
          return '<div style="display: flex; width: auto;">'+
          '<button onclick="traerPdfApartado(' +row[1]+ ');" title="Ver reporte" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px">'+
          '<span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>'+
          '</div>'
        }
        
      }
      
       },
  },
],
paging: true,
searching: true,
scrollY: "50vh",
info: false,
responsive: false,
ordering: "enable",
multiColumnSort: true,
order: [[1, "desc"]],

//order: [1, "desc"],


});
//table.columns( [6] ).visible( true );
$("table.dataTable thead").addClass("table-info")

}

function ocultarSidebar(){
  let sesion = $("#emp-title").attr("sesion_rol");
if(sesion == 4){
  $(".rol-4").addClass("d-none");

}
};

function traerPdfApartado(id){
  window.open("./modelo/pedidos/generar-reporte-pedido.php?id="+id);
}

function traerPdfAbonoApartado(id_abono, id_apartado){
  window.open("./modelo/pedidos/reporte-abono-pedido.php?id="+id_apartado+"&id_abono="+id_abono);
}

var tooltipSpan = document.getElementById('tooltip-span');

window.onmousemove = function (e) {
    var x = e.clientX,
        y = e.clientY;
   // tooltipSpan.style.top = (y + 20) + 'px';
   // tooltipSpan.style.left = (x + 20) + 'px';
};


function procesarOrden(id_apartado){  
  Swal.fire({
    title: 'Procesar venta',
    width: 800,
    text: "¿Como quieres procesar la venta?",
    html: `
      <div class="container">
        <div class="row">
          <div class="col-12">
            
            <div class="form-group header-preview">
              <div class="row">
                <div class="col-12 text-left">
                  <label for="nombre">Nombre del cliente:</label>
                  <span id="nombre_cliente"></label>
                </div>
              </div>
              <div class="row">
                <div class="col-4 text-left">
                  <label>Vendedor:</label>
                  <span id="vendedor"></label>
                </div>
                <div class="col-8 text-left">
                  <label for="">Plazo:</label>
                  <span id="plazo"></label>
                </div>
              </div>
              <div class="row">
                <div class="col-4 text-left">
                  <label for="nombre">Sucursal:</label>
                  <span id="sucursal"></label>
                </div>
                <div class="col-8 text-left">
                  <label for="">Hora:</label>
                  <span id="hora"></label>
                </div>
              </div>
              <div class="row">
                <div class="col-4 text-left">
                  <label>Adelanto:</label>
                  <span id="adelanto"></label>
                </div>
                <div class="col-8 text-left">
                  <label for="">Metodo:</label>
                  <span id="forma_pago"></label>
                </div>
              </div>      
            </div>
            
            <div class="row">
                <div class="col-12">
                  <table id="detalle_apartado" class="table table-bordered table-success"></div>
                </div>
            </div>  

            <div class="row mt-3">
                <div class="col-12">
                  <table class="" id="importes">
                      <tr>
                          <td>Total:</td>
                          <td id="total_apartado" class="dark-cell"></td>
                      </tr> 
                      <tr>
                          <td>Adelanto:</td>
                          <td id="adelanto_apartado" class="dark-cell"></td>
                      </tr>
                      <tr>
                          <td>Restante:</td>
                          <td id="restante_apartado" class="dark-cell"></td>
                      </tr>    
                  </div>
                </div>
            </div>  

          </div>
        </div>    
      </div>
    `,
    showCancelButton: true,
    showConfirmButton:true,
    showDenyButton: true,
    showCloseButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#dc3545',
    denyButtonColor: '#5DC1B9',
    confirmButtonText: 'Hacer Credito', 
    denyButtonText: 'Abonar', 
    //denyButtonText: 'Venta a credito',
    cancelButtonText: 'Cancelar',
    showLoaderOnConfirm: true,
    didOpen: () => {
      $.ajax({
        type: "post",
        url: "./modelo/pedidos/traer-data-orden.php",
        data: {"id": id_apartado},
        dataType: "JSON",
        success: function (response) {

          //Conversion fechas y monedas
          // Crear un objeto Date a partir de la fecha ISO
          const fechaInicioObjeto = new Date(response.fecha_inicio);
          const fechaFinalObjeto = new Date(response.fecha_final);
          var restante_ = parseFloat(response.restante);
          var adelanto_ = parseFloat(response.primer_abono);
          var total_ = parseFloat(response.total);
          const formatoMonedaAdelanto = adelanto_.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
          const formatoMonedaRestante = restante_.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
          const formatoMonedaTotal = total_.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });


          // Obtener el día, mes y año de la fecha
          const dia_inicio = fechaInicioObjeto.getDate();
          const dia_final = fechaFinalObjeto.getDate();
          const mes_inicio = meses[fechaInicioObjeto.getMonth()];
          const mes_final = meses[fechaFinalObjeto.getMonth()];
          const año_inicio = fechaInicioObjeto.getFullYear();
          const año_final = fechaFinalObjeto.getFullYear();

          // Crear la cadena de texto en el nuevo formato
          const fechaFormateadaInicial = `${dia_inicio} de ${mes_inicio} del ${año_inicio}`;
          const fechaFormateadaFinal = `${dia_final} de ${mes_final} del ${año_final}`;

          //Seteo de datos
          $("#nombre_cliente").text(response.cliente);
          $("#plazo").text(fechaFormateadaInicial + " al " + fechaFormateadaFinal);
          $("#sucursal").text(response.sucursal);
          $("#hora").text(response.hora_inicio);
          $("#adelanto").text(formatoMonedaAdelanto);
          $("#forma_pago").text(response.metodo_pago);
          $("#total_apartado").text(formatoMonedaTotal);
          $("#adelanto_apartado").text(formatoMonedaAdelanto);
          $("#restante_apartado").text(formatoMonedaRestante);
          $("#vendedor").text(response.vendedor_usuario);

          //Conversion de arreglo de objectos a arreglos de arrays
          response.detalles = response.detalles.length == 0 ? [] : response.detalles;
          const data_convertida = response.detalles.map(objeto => [
            objeto.cantidad,
            objeto.modelo,
            objeto.descripcion,
            objeto.marca,
            objeto.precio_unitario,
            objeto.importe,
            objeto.caracteres
        ]);

          table_apartado = $('#detalle_apartado').DataTable({
             
            columns: [   
            { title: 'Cantidad' },
            { title: 'Modelo'},
            {title:  'Descripcion'},
            { title: 'Marca'},
            { title: 'Precio unit.'},
            { title: 'Importe'}
            ],
            data: data_convertida,
          });
        }
      });
    },
    allowOutsideClick: () => !Swal.isLoading(),

  }).then((respuesta) => {
    if(respuesta.isConfirmed){

      

      Swal.fire({
        icon: 'info',
        title: '¿Deseas continuar?',
        text: '',
        html:`<div class="row">
                    <div class="col-12">
                      <p>Este pedido se convertira en un credito, el sistema verificara si las llantas del apartado existen en el inventario de la sucursal y los abonos realizados se ajustaran al credito.</p>
                    </div>
                    <div class="col-12">
                        <label>Seleccione un plazo de credito</label>
                        <select id="plazo-credito" class="form-control">
                            <option value="6">1 día</option>
                            <option value="1">1 Semana</option>
                            <option value="2">15 días</option>
                            <option value="3">1 mes</option>
                            <option value="5">Sin definir</option>
                        </select>
                    </div>
              </div>`,
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Cancelar',
      }).then((r)=>{

        if(r.isConfirmed){
          let timerInterval
          var plazo_credito = $("#plazo-credito").val();
          Swal.fire({
            title: 'Procesando',
            html: 'Creando credito <b></b> milisegundos transcurridos.',
            timer: 1000,
            timerProgressBar: true,
            didOpen: () => {
              Swal.showLoading()
              const b = Swal.getHtmlContainer().querySelector('b')
              timerInterval = setInterval(() => {
                
                b.textContent = Swal.getTimerLeft()
              }, 100)
            },
            willClose: () => {
          
              clearInterval(timerInterval)
              $.ajax({
                type: "post",
                url: "./modelo/pedidos/transformar-credito.php",
                data: {"id": id_apartado, 'plazo': plazo_credito},
                dataType: "JSON",
                success: function (responseCred) {
                  if(responseCred){
                    if(responseCred.estatus){
                     icon = 'success';
                  }else{
                   icon = 'error';
                  }
                  Swal.fire({
                    icon: icon,
                    text: responseCred.mensaje,
                    
                    showCancelButton: true,
                    confirmButtonText: 'Entendido',
                    cancelButtonText: 'Cancelar'
                  })
                }
                }
              });
            }
          }).then((result) => {
            /* Read more about handling dismissals below */
            if (result.dismiss === Swal.DismissReason.timer) {
              console.log('I was closed by the timer')
            }
          })
        }
      })
      
    }else if(respuesta.isDenied){
      abonarPedido(id_apartado);
    }
    
  })
}

function calcularMontosApartado(importe, restante){
  let button_confirm = document.querySelector('.swal2-confirm');
  var inputs = document.querySelectorAll("#contenedor-metodos input[type=number]");  // Obtener todos los inputs
  var suma = 0;
  var resta = 0;
  let abonado = $("#abonado_venta").val();
  inputs.forEach(function(input) {
    var valor = parseFloat(input.value);
    if (!isNaN(valor)) {
      suma += valor;
    }
  });
  resta = restante - suma;

  suma = parseFloat(suma) + parseFloat(abonado);
  const formatoMonedaSumatoria = suma.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
  const formatoMonedaResta = resta.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
  
  $("#badge-sumatoria").text(formatoMonedaSumatoria);
  $("#badge-restante").text(formatoMonedaResta);
  // Verificar si la suma es igual al precio_llanta y actualizar el badge
  var badgePrecio = document.getElementById("badge-precio-total");
  var badgeSumatoria = document.getElementById("badge-sumatoria");
  var text_message = document.getElementById("text-message");
  if (suma === importe) {
    badgePrecio.classList.remove("badge-secondary");
    badgePrecio.classList.remove("badge-danger");
    badgePrecio.classList.add("badge-success");
    badgeSumatoria.classList.remove("badge-secondary");
    badgeSumatoria.classList.remove("badge-danger");
    badgeSumatoria.classList.remove("badge-warning");
    badgeSumatoria.classList.add("badge-success");
    button_confirm.style.backgroundColor = '#1cc88a';
    button_confirm.style.borderColor = '#1cc88a';
    text_message.classList.remove("text-secondary");
    text_message.classList.remove("text-danger");
    text_message.classList.add("text-success");
    text_message.textContent = '¡Listo!';
    $("#total_venta").attr("is-valid", "true")
    audio_2.play();      
  }else if(suma > importe){
    badgePrecio.classList.remove("badge-success");
    badgePrecio.classList.remove("badge-secondary");
    badgeSumatoria.classList.remove("badge-success");
    badgeSumatoria.classList.add("badge-danger");
    badgePrecio.classList.add("badge-danger");

    button_confirm.style.backgroundColor = '#dc3545';
    button_confirm.style.borderColor = '#dc3545';
    text_message.classList.remove("text-success");
    text_message.classList.remove("text-secondary");
    text_message.classList.add("text-danger");
    text_message.textContent = 'El monto soprepasa la cantidad';
    $("#total_venta").attr("is-valid", "false")
  } else {
    badgePrecio.classList.remove("badge-success");
    badgePrecio.classList.remove("badge-danger");
    badgePrecio.classList.add("badge-secondary");

    button_confirm.style.backgroundColor = '#858796';
    button_confirm.style.borderColor = '#858796';
    text_message.classList.remove("text-success");
    text_message.classList.remove("text-danger");
    badgeSumatoria.classList.remove("badge-danger");
    badgeSumatoria.classList.remove("badge-success");
    text_message.classList.add("text-secondary");
    badgeSumatoria.classList.add("badge-warning");
    text_message.textContent = '';
    $("#total_venta").attr("is-valid", "false")
  }
  
}


const audio_2 = new Audio("./src/sounds/success-sound.mp3");
audio_2.volume = 0.5;

function cancelarApartado(id) { 

  Swal.fire({
      title: "Cancelar Venta",
      html: '<span>¿Estas seguro de cancelar este apartado?</span><br><br>'+
      '<div class="m-auto"><label>Motivo de la cancelación.</label><br><textarea id="motivo" name="motivo" placeholder="Escribe el motivo del porque estas cancelando esta venta." class="form-control m-auto" style="width:300px;height:80px;" ></textarea></div>',
      showCancelButton: true,
      cancelButtonText: 'Cerrar',
      cancelButtonColor: '#00e059',
      showConfirmButton: true,
      confirmButtonText: 'Cancelar', 
      cancelButtonColor:'#ff764d',
      focusConfirm: false }).then((result) => { 
      
          if(result.isConfirmed){ 
              motivo = $("#motivo").val();
             // if($("#motivo").val())
              motivo = $("#motivo").val();
              $.ajax({
                  type: "POST",
                  url: "./modelo/apartados/cancelar-apartado.php",
                  data: {"id_venta": id, "motivo_cancel": motivo},
                  success: function (response) {
                      if(response == 0){

                          Swal.fire({
                              title: 'Error',
                              html: "<span>El apartado no se pudo cancelar</span>",
                              icon: "error",
                              cancelButtonColor: '#00e059',
                              showConfirmButton: true,
                              confirmButtonText: 'Aceptar', 
                              cancelButtonColor:'#ff764d',
                          }).then((result) => {  
              
                              if(result.isConfirmed){
                                  table.ajax.reload(null, false);
                              }});
                              table.ajax.reload(null, false);
                      }else if(response == 1 || response ==11){
                          Swal.fire({ 
                              title: 'Apartado cancelado',
                              html: "<span>El apartado se a cancelado.</span>",
                              icon: "success",
                              cancelButtonColor: '#00e059',
                              showConfirmButton: true,
                              confirmButtonText: 'Aceptar', 
                              cancelButtonColor:'#ff764d',
                          }).then((result) => {  
              
                              if(result.isConfirmed){
                                  table.ajax.reload(null, false);
                              }});
                              table.ajax.reload(null, false);

                      }else if(response == 3){
                          Swal.fire({
                              title: 'Apartado ya cancelado',
                              html: "<span>Este apartado ya esta cancelado.</span>",
                              icon: "warning",
                              cancelButtonColor: '#00e059',
                              showConfirmButton: true,
                              confirmButtonText: 'Aceptar', 
                              cancelButtonColor:'#ff764d',
                          }).then((result) => {  
              
                              if(result.isConfirmed){
                                  table.ajax.reload(null, false);
                              }});
                              table.ajax.reload(null, false);
                      }
                    
                  }
              });
              

          }

      });

}

function ventaYaCancelada(){
  Swal.fire({
    title: 'Apartado ya cancelado',
    html: "<span>Este apartado ya esta cancelado.</span>",
    icon: "warning",
    cancelButtonColor: '#00e059',
    showConfirmButton: true,
    confirmButtonText: 'Aceptar', 
    cancelButtonColor:'#ff764d',
})
 }

function abonarPedido(id_apartado){
  $.ajax({
    type: "post",
    url: './modelo/pedidos/traer-data-orden.php',
    data: {"id":id_apartado},
    dataType: "JSON",
    success: function (response_ab) {
      var restante_pedido = parseFloat(response_ab.restante);
      if(restante_pedido >0){
        var label_button_abono = 'Abonar';
      }else{
        var label_button_abono = 'Procesar venta'
      }
      Swal.fire({
        title: 'Realizar abono',
        width: '1200',
        html: `
          <div class="container">

          <div id="permiso-abonar">
              <div class="row">
                  <div class="col-12">
                      <label>Metodo(s) de pago:</label><br>
                      <select id="metodos-pago-abono" class="selectpicker form-control mb-2" data-live-search="true"  multiple title="Metodos de pago"> 
                              <option value="0" selected>Efectivo</option>
                              <option value="1">Tarjeta</option>
                              <option value="2">Transferencia</option>
                              <option value="3">Cheque</option>
                         
                              <option value="4">Sin definir</option>
                      </select>
                  </div>
              </div>   
              
              <div id="contenedor-metodos">
                    <div class="row">
                        <div class="col-12">
                            <label>Selecciona el monto para cada metodo de pago:</label><br>
                            <input type="number" class="form-control" placeholder="0.00" id="monto_metodo_0_apartado" onkeyup="calcularMontosAbonosPedido(${response_ab.total}, ${response_ab.restante})">
                        </div>
                    </div>  
              </div>

              <div class="row m-4 justify-content-center">
                  <div class="col-md-3">
                       <label>Total:</label>
                       <input class="form-control disabled" placeholder="0.00" id="abono_apartado_ac" readonly>
                  </div>
              </div>

              <div class="row m-4">
                  <div class="col-12">
                    <div class="btn btn-success disabled" id="btn-realizar-abono-apartado" onclick="realizarAbonoPedido(${id_apartado}, ${response_ab.total}, ${restante_pedido})">${label_button_abono}</div><br>
                    <small style="color:red;" id="msj-alerta"></small>
                  </div>
              </div>
              </div>

              <div class="row mt-5">
                  <div class="col-12">
                    <table id="abonos_apartados_tabla" class="table table-bordered table-info">
                    </table>
                  </div>
              </div>
            
              <div id="area_totales_abonos" class="mt-4">
                  
              </div>
          </div>
        `,
        didOpen: ()=>{
             let rol_id = $("#emp-title").attr('sesion_rol');
                  let id_usuario = $("#emp-title").attr('sesion_id');
                  if(rol_id != 1 && rol_id != 2 && rol_id != 3 && id_usuario != 7) {
                      $("#permiso-abonar").addClass('d-none')
                  }
          if(restante_pedido <=0){
            $('#metodos-pago-abono').val('');
            $("#metodos-pago-abono").prop('disabled', true);
            $("#contenedor-metodos").empty();
            $("#btn-realizar-abono-apartado").removeClass('disabled');
          }
          $('#metodos-pago-abono').selectpicker('refresh');

          //Conversion de arreglo de objectos a arreglos de arrays
          response_ab.abonos = response_ab.detalles.length == 0 ? [] : response_ab.abonos;
          const data_convertida = response_ab.abonos.map(objeto => [
            objeto.id,
            objeto.fecha,
            objeto.horas,
            objeto.abono,
            objeto.pago_efectivo,
            objeto.pago_tarjeta,
            objeto.pago_transferencia,
            objeto.pago_cheque,
            objeto.pago_deposito,
            objeto.pago_sin_definir,
            objeto.sucursal
        ]);
        table_abonos = $('#abonos_apartados_tabla').DataTable({
    
            rowCallback: function(row, data, index) {
                var info = this.api().page.info();
                var page = info.page;
                var length = info.length;
                var columnIndex = 0; // Índice de la primera columna a enumerar
          
                $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
              },
             
            columns: [   
            { title: 'Folio' },
            { title: 'Fecha' },
            { title: 'Hora'},
            { title: 'Abono'},
            { title: 'Efectivo'},
            { title: 'Tarjeta'},
            { title: 'Transferencia'},
            { title: 'Cheque'},
            /*{ title: 'Deposito'},*/
            { title: 'Sin definir'},
            { title: 'Sucursal'},
            { title: 'PDF', render: function(data, type, row){
            
              return '<div style="display: flex; width: auto;">'+
              '<button onclick="traerPdfAbonoApartado('+row[0]+', '+ id_apartado+');" title="Ver reporte" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px">'+
              '<span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>'+
              '</div>';
            }}
            ],
            data: data_convertida,
          });

          let button_confirm = document.querySelector('.swal2-confirm');
              button_confirm.style.backgroundColor = '#858796';
    

                  var opciones = {
                    0: "Efectivo",
                    1: "Tarjeta",
                    2: "Transferencia",
                    3: "Cheque",
                    5: "Deposito",
                    4: "Sin definir"
                  };
    
                  var importe_total =  response_ab.total;
    
                  $("#metodos-pago-abono").change(function(){
                  $("#contenedor-metodos").empty();
                    let metodo_pago = $("#metodos-pago-abono").val();
                    var arregloMetodos= metodo_pago.reduce(function(result, key) {
                      result[key] = opciones[key];
                      return result;
                    }, {});
      
                    for(var clave in arregloMetodos) {
                      if (arregloMetodos.hasOwnProperty(clave)) {
                        var nombre_metodo = arregloMetodos[clave];
                        $("#contenedor-metodos").append(`
                          <div class="row mt-2">
                          <div class="col-md-12">
                              <label>Monto para pago ${nombre_metodo}</label>
                              <input type="number" class="form-control" id="monto_metodo_${clave}_apartado" onkeyup="calcularMontosAbonosPedido(${importe_total}, ${response_ab.restante})" placeholder="0.00">
                          </div>
                          </div>
                    `);}
                      }

                      calcularMontosAbonosPedido(importe_total, response_ab.restante);
                  });
    
                  let restante_ = parseFloat(response_ab.restante);
                  let adelanto_ = parseFloat(response_ab.primer_abono);
                  let total_ = parseFloat(response_ab.total);
                  const formatoMonedaAdelanto = adelanto_.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
                  const formatoMonedaRestante = restante_.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
                  const formatoMonedaTotal = total_.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
    
    
                  $("#area_totales_abonos").append(`
                    <div class="row mt-3">
                      <div class="col-md-4">
                          <label>Total</label>
                          <h1><span class="badge badge-info" id="badge-precio-total">${formatoMonedaTotal}</span><h1>
                          <input type="hidden" value="${total_}"class="form-control" is-valid="false" id="total_venta" disabled>
                      </div>
                      <div class="col-md-4">
                          <label>Abonado</label>
                          <h1><span class="badge badge-warning" id="badge-sumatoria">${formatoMonedaAdelanto}</span><h1>
                          <input type="hidden" value="${adelanto_}"class="form-control" is-valid="false" id="abonado_venta" disabled>
                      </div>
                      <div class="col-md-4">
                          <label>Restante</label>
                          <h1><span class="badge badge-secondary" id="badge-restante">${formatoMonedaRestante}</span><h1>
                          <input type="hidden" value="${restante_}"class="form-control" is-valid="false" id="restante_venta" disabled>
                      </div>
                      <div class="col-md-12 text-center">
                        <h2><span id="text-message" class="text-secondary mt-2 text-center"></span><h2>
                      </div>
                    </div>
                    `) 
              
        },
      }).then(()=>{
        //kuku
        table.ajax.reload(null, false);
      });
    }
  });
}


function calcularMontosAbonosPedido(importe, restante){
  let button_confirm = document.querySelector('.swal2-confirm');
  var inputs = document.querySelectorAll("#contenedor-metodos input[type=number]");  // Obtener todos los inputs
  var suma = 0;
  var resta = 0;
  let abonado = $("#abonado_venta").val();
  inputs.forEach(function(input) {
    var valor = parseFloat(input.value);
    if (!isNaN(valor)) {
      suma += valor;
    }
  });
  resta = restante - suma;
  let suma_ingresada = suma;
  suma = parseFloat(suma) + parseFloat(abonado);
  const formatoMonedaSumatoria = suma.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
  const formatoMonedaResta = resta.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
  
  $("#badge-sumatoria").text(formatoMonedaSumatoria);
  $("#badge-restante").text(formatoMonedaResta);
  // Verificar si la suma es igual al precio_llanta y actualizar el badge
  var badgePrecio = document.getElementById("badge-precio-total");
  var badgeSumatoria = document.getElementById("badge-sumatoria");
  var text_message = document.getElementById("text-message");
  var btn_abonar_apartado_ = $("#btn-realizar-abono-apartado");
  $("#abono_apartado_ac").val(suma_ingresada)
  if(suma_ingresada > 0){
    btn_abonar_apartado_.removeClass("disabled"); 
    $("#msj-alerta").text('')
  }

  if(suma_ingresada <= 0){
    btn_abonar_apartado_.addClass("disabled"); 
  }

  if (suma == importe) {
    badgePrecio.classList.remove("badge-secondary");
    badgePrecio.classList.remove("badge-danger");
    badgePrecio.classList.add("badge-success");
    badgeSumatoria.classList.remove("badge-secondary");
    badgeSumatoria.classList.remove("badge-danger");
    badgeSumatoria.classList.remove("badge-warning");
    badgeSumatoria.classList.add("badge-success");
    button_confirm.style.backgroundColor = '#1cc88a';
    button_confirm.style.borderColor = '#1cc88a';
    text_message.classList.remove("text-secondary");
    text_message.classList.remove("text-danger");
    text_message.classList.add("text-success");
    text_message.textContent = '¡Listo!';
    $("#total_venta").attr("is-valid", "true");
    audio_2.play();
    btn_abonar_apartado_.classList.removeClass('disabled'); 
  }else if(suma > importe){
    badgePrecio.classList.remove("badge-success");
    badgePrecio.classList.remove("badge-secondary");
    badgeSumatoria.classList.remove("badge-success");
    badgeSumatoria.classList.add("badge-danger");
    badgePrecio.classList.add("badge-danger");

    button_confirm.style.backgroundColor = '#dc3545';
    button_confirm.style.borderColor = '#dc3545';
    text_message.classList.remove("text-success");
    text_message.classList.remove("text-secondary");
    text_message.classList.add("text-danger");
    text_message.textContent = 'El monto soprepasa la cantidad';
    $("#total_venta").attr("is-valid", "false");
    btn_abonar_apartado_.addClass('disabled'); 

  } else {
    badgePrecio.classList.remove("badge-success");
    badgePrecio.classList.remove("badge-danger");
    badgePrecio.classList.add("badge-secondary");

    button_confirm.style.backgroundColor = '#858796';
    button_confirm.style.borderColor = '#858796';
    text_message.classList.remove("text-success");
    text_message.classList.remove("text-danger");
    badgeSumatoria.classList.remove("badge-danger");
    badgeSumatoria.classList.remove("badge-success");
    text_message.classList.add("text-secondary");
    badgeSumatoria.classList.add("badge-warning");
    text_message.textContent = '';
    $("#total_venta").attr("is-valid", "false");
   // btn_abonar_apartado_.addClass('disabled'); 

  }
  
}

function realizarAbonoPedido(id_apartado, total, restante_pedido){
  let btn = $("#btn-realizar-abono-apartado");
  const esta_desactivado = btn.hasClass("disabled"); 
  let alerta_mensaje = $("#msj-alerta");
  if(esta_desactivado){
    alerta_mensaje.text('Hay un error con el monto')
  }else{

    var opciones = {
      0: "Efectivo",
      1: "Tarjeta",
      2: "Transferencia",
      3: "Cheque",
      3: "Deposito",
      4: "Sin definir"
    };
    let metodo_pago = $("#metodos-pago-abono").val();
    var arregloMetodos= metodo_pago.reduce(function(result, key) {
      let monto = parseFloat(document.getElementById(`monto_metodo_${key}_apartado`).value);
      let monto_ = Number.isNaN(monto) ? 0 : monto;
      result[key] = {"id_metodo":key, "metodo":opciones[key], "monto": monto_};
      return result;
    }, {});
  
    if($('#abono_apartado_ac').val() <=0 && restante_pedido > 0){
    alerta_mensaje.text('El monto no puede ser igual o menor que 0')
    }else{

      $.ajax({
        type: "post",
        url: "./modelo/pedidos/realizar-abono-pedidos.php",
        data: {'id_apartado': id_apartado, 'metodos_pago': arregloMetodos, restante_pedido},
        dataType: "JSON",
        success: function (response) {
          if(response.estatus){
              alerta_mensaje.text('');
              if(response.liquidacion){
                toastr.success('Apartado liquidado con exito', 'Exito' ); 
                $("#btn-realizar-abono-apartado").prop('disabled',true)
                $("#btn-realizar-abono-apartado").addClass('disabled')
                $("#btn-realizar-abono-apartado").removeClass('btn-success')
                $("#btn-realizar-abono-apartado").addClass('btn-primary')
                $("#btn-realizar-abono-apartado").text('Pedido procesado')
                alerta_mensaje.text('Pedido procesado puedes revisarlo en el historial de venta o en el ultimo pdf generado en esta tabla')
                alerta_mensaje.css('color', 'green')
              }else{
                toastr.success('Abonado con exito', 'Exito' ); 

              }
              recargarTablaAbonosPedido(id_apartado)
            }else{
              if(response.liquidacion){
                toastr.error(response.mensaje, 'Error' ); 
              }else{
                toastr.error(response.mensaje, 'Error' ); 

                alerta_mensaje.text('Hubo un error al agregar el abono. Mensaje: ' + response.mensaje)
              }

          }
        }
      });

    }
  }
}

function recargarTablaAbonosPedido(id_apartado){
  $.ajax({
    type: "post",
    url: './modelo/pedidos/traer-data-orden.php',
    data: {"id":id_apartado},
    dataType: "JSON",
    success: function (response_ab) {
      //Conversion de arreglo de objectos a arreglos de arrays
      response_ab.abonos = response_ab.detalles.length == 0 ? [] : response_ab.abonos;
      const data_convertida = response_ab.abonos.map(objeto => [
        objeto.id,
        objeto.fecha,
        objeto.horas,
        objeto.abono,
        objeto.pago_efectivo,
        objeto.pago_tarjeta,
        objeto.pago_transferencia,
        objeto.pago_cheque,
        objeto.pago_deposito,
        objeto.pago_sin_definir,
        objeto.sucursal
    ]);
    table_abonos.clear().rows.add(data_convertida).draw();
    $('#badge-restante').text('$'+response_ab.restante)
    $('#badge-sumatoria').text('$'+response_ab.primer_abono)
    }})
}

function confirmarPedido(id_pedido, pedido_confirmado){

  if(pedido_confirmado == 1){
    Swal.fire({
      icon: 'warning',
      title: 'Pedido ya confirmado',
      html:`
      <div class="container">
          <div class="row">
              <div class="col-12">
                  <p>¿Desear desconfirmar el pedido?</p>
              </div>
          </div>
      </div>
      `,
      confirmButtonText: 'Desconfirmar',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
    }).then(function(r){
      if(r.isConfirmed){
          $.ajax({
            type: "post",
            url: "./modelo/pedidos/confirmar-pedido.php",
            data: {'id_pedido': id_pedido, 'desconfirmar': true},
            dataType: "JSON",
            success: function (response) {
              let icon = response.estatus ? 'success' : 'errror';
              Swal.fire({
                icon: icon,
                text: response.mensaje
              });
              table.ajax.reload(null, false);
            }
          });
      }
    })
  }else{
    Swal.fire({
      icon: 'question',
      title: 'Confirmar pedido',
      html:`
      <div class="container">
          <div class="row">
              <div class="col-12">
                  <p>¿Desear confirmar el pedido del producto?</p>
              </div>
          </div>
      </div>
      `,
      confirmButtonText: 'Confirmar',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
    }).then(function(r){
      if(r.isConfirmed){
          $.ajax({
            type: "post",
            url: "./modelo/pedidos/confirmar-pedido.php",
            data: {'id_pedido': id_pedido, 'desconfirmar': false},
            dataType: "JSON",
            success: function (response) {
              let icon = response.estatus ? 'success' : 'errror';
              Swal.fire({
                icon: icon,
                text: response.mensaje
              });
              table.ajax.reload(null, false);
            }
          });
      }
    })
  }
}

//Codigo de liquida
/** 
 * 
 *  Swal.fire({
            title: "Asignar montos",
            width: '800px',
            showCancelButton: true,
            cancelButtonText: 'Cerrar',
            cancelButtonColor: '#00e059',
            showConfirmButton: true,
            confirmButtonText: 'Realizar venta', 
            cancelButtonColor:'#ff764d',
            html: `
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                    <label>Selecciona los metodos de pago:</label><br>
                      <select id="metodos-pago" class="selectpicker form-control mb-2" data-live-search="true"  multiple title="Metodos de pago"> 
                              <option value="0" selected>Efectivo</option>
                              <option value="1">Tarjeta</option>
                              <option value="2">Transferencia</option>
                              <option value="3">Cheque</option>
                              <option value="4">Sin definir</option>
                      </select>
                    </div>
                </div> 
    
                <div id="contenedor-metodos">
                    <div class="row">
                        <div class="col-12">
                            <label>Selecciona el monto para cada metodo de pago:</label><br>
                            <input type="number" class="form-control" placeholder="0.00" id="monto_metodo_0_apartado" onkeyup="calcularMontosApartado(${response2.total}, ${response2.restante})">
                        </div>
                    </div>  
                </div>
    
                <div id="contenedor-montos"> 
                </div>
    
            </div>`,
            didOpen: function () { 
             
                  let button_confirm = document.querySelector('.swal2-confirm');
                  button_confirm.style.backgroundColor = '#858796';
    
                  //$("#contenedor-metodos").empty();
                  var opciones = {
                    0: "Efectivo",
                    1: "Tarjeta",
                    2: "Transferencia",
                    3: "Cheque",
                    4: "Sin definir"
                  };
    
                  var importe_total =  response2.total;
                  $('#metodos-pago').selectpicker('refresh');
    
                  $("#metodos-pago").change(function(){
                  $("#contenedor-metodos").empty();
                    let metodo_pago = $("#metodos-pago").val();
                    var arregloMetodos= metodo_pago.reduce(function(result, key) {
                      result[key] = opciones[key];
                      return result;
                    }, {});
      
                    for(var clave in arregloMetodos) {
                      if (arregloMetodos.hasOwnProperty(clave)) {
                        var nombre_metodo = arregloMetodos[clave];
                        $("#contenedor-metodos").append(`
                          <div class="row mt-2">
                          <div class="col-md-12">
                              <label>Monto para pago ${nombre_metodo}</label>
                              <input type="number" class="form-control" id="monto_metodo_${clave}_apartado" onkeyup="calcularMontosApartado(${importe_total}, ${response2.restante})" placeholder="0.00">
                          </div>
                          </div>
                    `);}
                      }

                      calcularMontosApartado(importe_total, response2.restante);
                  });
    
                  let restante_ = parseFloat(response2.restante);
                  let adelanto_ = parseFloat(response2.primer_abono);
                  let total_ = parseFloat(response2.total);
                  const formatoMonedaAdelanto = adelanto_.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
                  const formatoMonedaRestante = restante_.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
                  const formatoMonedaTotal = total_.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
    
    
                  $("#contenedor-montos").append(`
                    <div class="row mt-3">
                      <div class="col-md-4">
                          <label>Total</label>
                          <h1><span class="badge badge-info" id="badge-precio-total">${formatoMonedaTotal}</span><h1>
                          <input type="hidden" value="${total_}"class="form-control" is-valid="false" id="total_venta" disabled>
                      </div>
                      <div class="col-md-4">
                          <label>Abonado</label>
                          <h1><span class="badge badge-warning" id="badge-sumatoria">${formatoMonedaAdelanto}</span><h1>
                          <input type="hidden" value="${adelanto_}"class="form-control" is-valid="false" id="abonado_venta" disabled>
                      </div>
                      <div class="col-md-4">
                          <label>Restante</label>
                          <h1><span class="badge badge-secondary" id="badge-restante">${formatoMonedaRestante}</span><h1>
                          <input type="hidden" value="${restante_}"class="form-control" is-valid="false" id="restante_venta" disabled>
                      </div>
                      <div class="col-md-12 text-center">
                        <h2><span id="text-message" class="text-secondary mt-2 text-center"></span><h2>
                      </div>
                    </div>
                    `) 
                
              
              },
              preConfirm: function(){
              if($("#metodos-pago").val() == ""){
                Swal.showValidationMessage(
                  `Elige un metodo de pago`
                )
              } else 
              if($("#total_venta").attr("is-valid") == "false"){
                Swal.showValidationMessage(
                  `Los montos no corresponde al total`
                )
              }
              }
            
          }).then(function (ress) {
            if(ress.isConfirmed){
              var opciones = {
                0: "Efectivo",
                1: "Tarjeta",
                2: "Transferencia",
                3: "Cheque",
                4: "Sin definir"
              };
              let metodo_pago = $("#metodos-pago").val();
            
              var arregloMetodos= metodo_pago.reduce(function(result, key) {
                let monto = parseFloat(document.getElementById(`monto_metodo_${key}_apartado`).value);
                result[key] = {"id_metodo":key, "metodo":opciones[key], "monto": monto};
                return result;
              }, {});
    
               realizarVentaApartado(arregloMetodos, id_apartado);
            }
          })
 */