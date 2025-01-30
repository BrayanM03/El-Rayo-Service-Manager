
 let contenedor_opciones_inferiores = $("#opciones_inferiores");
 
 function estadisticasGenerales() { 

    $.ajax({
        type: "POST", 
        url: "./modelo/metricas/estadisticas-generales.php",
        data: {"data": "data"},
        dataType: "JSON",
        success: function (response) {

            gananciaMes = number_format_js(response.ganancia_mes,2,'.',','); 
            gananciaAnual = number_format_js(response.ganancia_anual,2,'.',','); 

            $("#ganancias-mes-actual").text("$ " + gananciaMes);
            $("#ganancia-anual-actual").text("$ " + gananciaAnual);
            $("#total_ventas").text( response.total_venta);
            $("#creditos_pendientes").text( response.creditos_pendientes);
        
        }
    });

    
  }
 
 estadisticasGenerales();

 function number_format_js(number, decimals, dec_point, thousands_point) {

    if (number == null || !isFinite(number)) {
        throw new TypeError("number is not valid");
    }

    if (!decimals) {
        var len = number.toString().split('.').length;
        decimals = len > 1 ? len : 0;
    }

    if (!dec_point) {
        dec_point = '.';
    }

    if (!thousands_point) {
        thousands_point = ',';
    }

    number = parseFloat(number).toFixed(decimals);

    number = number.replace(".", dec_point);

    var splitNum = number.split(dec_point);
    splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
    number = splitNum.join(dec_point);

    return number;
}

//Funciones para el menu vertical de las metricas generales

function sucursalSeleccionadaMetricaGeneral(element){
    elements_a= document.querySelectorAll('.a-element-vertical-tab')
    elements_a.forEach(function(item) {
        item.classList.remove('selected-a');
        item.classList.add('my-menu-a');
    });
    element.classList.remove('my-menu-a');
    element.classList.add('selected-a');
    let id_sucursal = element.getAttribute('id_sucursal');
    let nombre_sucursal = element.getAttribute('nombre_sucursal');
    metricasSucursales(id_sucursal, nombre_sucursal);
}

metricasSucursales();
function metricasSucursales(id_sucursal=0, nombre_sucursal='generales'){
    let tipo_filtro = $("#datos-personalizar").attr('tipo_filtro');
    let mes = $("#datos-personalizar").attr('mes');
    let fecha_inicial = $("#datos-personalizar").attr('fecha_inicial');
    let fecha_final = $("#datos-personalizar").attr('fecha_final');
    let year = $("#datos-personalizar").attr('year');
    let semana = $("#datos-personalizar").attr('semana');
    $.ajax({
        type: "post",
        url: "./modelo/metricas/traer-metricas-generales.php",
        data: {id_sucursal, tipo_filtro, mes, fecha_inicial, fecha_final, year, semana},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                $("#metricas-generales-titulo").empty().append(nombre_sucursal)
                $("#contenedor-datos-circulos").remove();
                $("#vertical-menu-main-container").append(`
                <div style="display: flex; flex-direction: row;" id="contenedor-datos-circulos" class="animate__animated animate__fadeInDown">
                `)
                let utilidadMoneda = response.utilidad.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let entradaMoneda = response.entrada.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let creditoMoneda = response.creditos_por_cobrar.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let dineroMercanciaMoneda = response.dinero_mercancia.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                $("#contenedor-datos-circulos").empty().append(`
                <div class="basis-1/4" style="flex-basis: 25%; display:flex; flex-direction:column; align-items:center">
                    <div class="dato-circulo">
                        <span>${utilidadMoneda}</span>
                    </div>
                    <b class="mt-2">Utilidad</b>
                    </div>
                    <div class="basis-1/4" style="flex-basis: 25%; display:flex; flex-direction:column; align-items:center">
                        <div class="dato-circulo">
                            <span>${entradaMoneda}</span>
                        </div>
                        <b class="mt-2">Entrada</b>
                    </div>
                    <div class="basis-1/4" style="flex-basis: 25%; display:flex; flex-direction:column; align-items:center">
                        <div class="dato-circulo">
                            <span>${creditoMoneda}</span>
                        </div>
                        <b class="mt-2 text-center">Creditos por</br> cobrar</b>
                    </div>
                    <div class="basis-1/4" style="flex-basis: 25%; display:flex; flex-direction:column; align-items:center">
                        <div class="dato-circulo">
                            <span>${dineroMercanciaMoneda}</span>
                        </div>
                        <b class="mt-2 text-center">Dinero en mercancia</b>
                    </div>
                        `)
            }
        }
    });
}

function modalPersonalizarFechaMetricasSucursales(){
    Swal.fire({
        title: 'Personalizar filtros de busqueda',
        html: `
        <small>No aplica en el <b>dinero en mercancia actual</b></small>
        <div class="container m-3">
            <div class="row">
                <div class="col-12">
                    <label>Tipo de filtro</label>
                    <select class="form-control" id="tipo-filtro" onchange="cambiarOpcionFiltroMetricasSucursales()">
                        <option value="1">Año</option>
                        <option value="2">Semana</option>
                        <option value="3" selected>Mes</option>
                        <option value="4">Dia</option>
                        <option value="5">Rango</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3" id="area-opciones-metrica">
            <div class="col-6">
                <label for="año-filtro-metricas">Año</label>
                <select class="form-control" id="año-filtro-metricas">
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
                </select>
            </div>
            <div class="col-6">
                <label for="mes-filtro-metricas">Mes</label>
                <select class="form-control" id="mes-filtro-metricas">
                    <option value="1">Enero</option>
                    <option value="2">Febrero</option>
                    <option value="3">Marzo</option>
                    <option value="4">Abril</option>
                    <option value="5">Mayo</option>
                    <option value="6">Junio</option>
                    <option value="7">Julio</option>
                    <option value="8">Agosto</option>
                    <option value="9">Septiembre</option>
                    <option value="10">octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>
            </div>
        </div>
        </div>`,
        didOpen: function(){
            var fecha_actual = new Date();
            $("#mes-filtro-metricas").val((fecha_actual.getMonth())+1);
            $("#año-filtro-metricas").val(fecha_actual.getFullYear());
        },
        confirmButtonText: 'Establecer filtro'
    }).then((r)=>{
        if(r.isConfirmed){
            let tipo_filtro = $("#tipo-filtro").val();
            let boton_contenedor_filtros = $("#datos-personalizar");
            boton_contenedor_filtros.attr('tipo_filtro', '')
            boton_contenedor_filtros.attr('mes', '')
            boton_contenedor_filtros.attr('fecha_inicial', '')
            boton_contenedor_filtros.attr('fecha_final', '')
            boton_contenedor_filtros.attr('year', '')
            boton_contenedor_filtros.attr('semana', '')
            let metricas_generales_texto_filtro = $("#metricas-generales-texto-filtro");
            metricas_generales_texto_filtro.empty();
            switch (tipo_filtro) {
                case '1':
                    boton_contenedor_filtros.attr('tipo_filtro', 1)
                    let year_ = $("#año-filtro-metricas").val()
                    boton_contenedor_filtros.attr('year', year_)
                    metricas_generales_texto_filtro.text('en el año: ' + year_)
                    break;
                case '2':
                    boton_contenedor_filtros.attr('tipo_filtro', 2)
                    let semana_ = $("#semana-filtro-metricas").val()
                    boton_contenedor_filtros.attr('semana', semana_)
                    let year_2 = $("#año-filtro-metricas").val()
                    boton_contenedor_filtros.attr('year', year_2)
                    metricas_generales_texto_filtro.text('en la semana: ' + semana_)
                    break;
                case '3':
                    boton_contenedor_filtros.attr('tipo_filtro', 3)
                    let mes_ = $("#mes-filtro-metricas").val()
                    boton_contenedor_filtros.attr('mes', mes_)
                    let meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                    let mes__ =parseInt(mes_);
                    metricas_generales_texto_filtro.text('en el mes: ' + meses[mes__-1])
                    let year_3 = $("#año-filtro-metricas").val()
                    boton_contenedor_filtros.attr('year', year_3)
                    break;
                case '4':
                    boton_contenedor_filtros.attr('tipo_filtro', 4)
                    let dia_ = $("#dia-filtro-metricas").val()
                    boton_contenedor_filtros.attr('fecha_inicial', dia_)
                    metricas_generales_texto_filtro.text('en el día: ' + dia_)
                    break;
                case '5':
                    boton_contenedor_filtros.attr('tipo_filtro', 5)
                    let fecha_inicial_ = $("#fecha-inicial-filtro-metricas").val()
                    let fecha_final_ = $("#fecha-final-filtro-metricas").val()
                    boton_contenedor_filtros.attr('fecha_inicial', fecha_inicial_)
                    boton_contenedor_filtros.attr('fecha_final', fecha_final_)

                    metricas_generales_texto_filtro.text('en el rango del ' + fecha_inicial_ + ' al ' + fecha_final_)
                    break;

               default:
                break;     
            }
            boton_contenedor_filtros.removeClass();
            boton_contenedor_filtros.addClass('btn btn-success');

            elements_a= document.querySelectorAll('.a-element-vertical-tab')
            elements_a.forEach(function(item) {
                let hasClassSelected = item.classList.contains('selected-a');
                if(hasClassSelected){
                    let id_sucursal = item.getAttribute('id_sucursal');
                    let nombre_sucursal = item.getAttribute('nombre_sucursal');
                    console.log(id_sucursal + ' '+ nombre_sucursal);
                    metricasSucursales(id_sucursal, nombre_sucursal)
                }
            });
        }
    })
}

function cambiarOpcionFiltroMetricasSucursales(){
    let tipo_filtro = $("#tipo-filtro").val();
    let area_opciones_filtro = $("#area-opciones-metrica");
    switch (tipo_filtro) {
        case '1':
            area_opciones_filtro.empty().append(`
            <div class="col-12">
                <label id="mes-filtro-metricas">Año</label>
                <select class="form-control" id="año-filtro-metricas">
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
            </div>
            `);
            break;
        case '2':
            area_opciones_filtro.empty().append(`
            <div class="col-6">
                <label id="mes-filtro-metricas">Año</label>
                <select class="form-control" id="año-filtro-metricas">
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
            </div>
            <div class="col-6">
                <label for="semana-filtro-metricas">Semana</label>
                <select class="form-control" id="semana-filtro-metricas">
                </select>
            </div>
            `);

            for (let index = 0; index < 52; index++) {
                const element =index+1;
                $("#semana-filtro-metricas").append(`
                    <option value="${element}">Semana ${element}</option>
                `)
            }
        break;
        case '3':
            area_opciones_filtro.empty().append(`
            <div class="col-6">
                <label for="año-filtro-metricas">Año</label>
                <select class="form-control" id="año-filtro-metricas">
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
            </select>
            </div>
            <div class="col-6">
                <label for="mes-filtro-metricas">Mes</label>
                <select class="form-control" id="mes-filtro-metricas">
                    <option value="1">Enero</option>
                    <option value="2">Febrero</option>
                    <option value="3">Marzo</option>
                    <option value="4">Abril</option>
                    <option value="5">Mayo</option>
                    <option value="6">Junio</option>
                    <option value="7">Julio</option>
                    <option value="8">Agosto</option>
                    <option value="9">Septiembre</option>
                    <option value="10">octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>
            </div>
            `);
        break;
        case '4':
            area_opciones_filtro.empty().append(`
            <div class="col-12">
                <label for="dia-filtro-metricas">Día</label>
                <input class="form-control" type="date" id="dia-filtro-metricas">
            </div>
            `);
        break;
        case '5':
            area_opciones_filtro.empty().append(`
            <div class="col-6">
                <label for="fecha-inicial-filtro-metricas">Fecha inicial</label>
                <input class="form-control" type="date" id="fecha-inicial-filtro-metricas">
            </div>
            <div class="col-6">
                <label for="fecha-final-filtro-metricas">Fecha final</label>
                <input class="form-control" type="date" id="fecha-final-filtro-metricas">
            </div>
            `);
        break;
    
        default:
            break;
    }

    
}
ventasHoy();
function ventasHoy(id_sucursal = '%'){
    contenedor_opciones_inferiores.attr('tipo_opcion', 5);
    $.ajax({
        type: "post",
        url: "./modelo/metricas/ventas-hoy.php",
        data: {id_sucursal, 'tipo_accion':1},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                  //Validacion de clases en ingresos
                if(response.data.ingreso_efectivo>0){
                   
                    $("#entrada-hoy-efectivo").removeClass('text-gray-500').addClass('text-gray-800')
                }
                if(response.data.ingreso_transferencia>0){
                    $("#entrada-hoy-transferencia").removeClass('text-gray-500').addClass('text-gray-800')
                }
                if(response.data.ingreso_tarjeta>0){
                    $("#entrada-hoy-tarjeta").removeClass('text-gray-500').addClass('text-gray-800')
                }
                if(response.data.ingreso_cheque>0){
                    $("#entrada-hoy-cheque").removeClass('text-gray-500').addClass('text-gray-800')
                }
                if(response.data.ingreso_sin_definir>0){
                    $("#entrada-hoy-sin-definir").removeClass('text-gray-500').addClass('text-gray-800')
                }
                if(response.data.total_ingreso>0){
                    $("#entrada-hoy-total").removeClass('text-gray-500').addClass('text-gray-800')
                }

                //Validacion de clases en gastos

                if(response.data.gasto_efectivo>0){
                    $("#gasto-hoy-efectivo").removeClass('text-gray-500').addClass('text-gray-800')
                }
                if(response.data.gasto_transferencia>0){
                    $("#gasto-hoy-transferencia").removeClass('text-gray-500').addClass('text-gray-800')
                }
                if(response.data.gasto_tarjeta>0){
                    $("#gasto-hoy-tarjeta").removeClass('text-gray-500').addClass('text-gray-800')
                }
                if(response.data.gasto_cheque>0){
                    $("#gasto-hoy-cheque").removeClass('text-gray-500').addClass('text-gray-800')
                }
                if(response.data.gasto_sin_definir>0){
                    $("#gasto-hoy-sin-definir").removeClass('text-gray-500').addClass('text-gray-800')
                }

                if(response.data.total_gasto>0){
                    $("#gasto-hoy-total").removeClass('text-gray-500').addClass('text-gray-800')
                }



                let efectivoMoneda = response.data.ingreso_efectivo.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let tarjetaMoneda = response.data.ingreso_tarjeta.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let transferenciaMoneda = response.data.ingreso_transferencia.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let chequeMoneda = response.data.ingreso_cheque.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                /* let depositoMoneda = response.data.ingreso_deposito.toLocaleString('en-US', { style: 'currency', currency: 'USD' }); */
                let sinDefinirMoneda = response.data.ingreso_sin_definir.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let entradaTotalMoneda = response.data.total_ingreso.toLocaleString('en-US', { style: 'currency', currency: 'USD' });

                let efectivoMonedaGasto = response.data.gasto_efectivo.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let tarjetaMonedaGasto = response.data.gasto_tarjeta.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let transferenciaMonedaGasto = response.data.gasto_transferencia.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let chequeMonedaGasto = response.data.gasto_cheque.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                /* let depositoMoneda = response.data.ingreso_deposito.toLocaleString('en-US', { style: 'currency', currency: 'USD' }); */
                let sinDefinirMonedaGasto = response.data.gasto_sin_definir.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let gastoTotalMoneda = response.data.total_gasto.toLocaleString('en-US', { style: 'currency', currency: 'USD' });

                let importe_efectivo = response.data.ingreso_efectivo - response.data.gasto_efectivo;
                let importe_tarjeta = response.data.ingreso_tarjeta - response.data.gasto_tarjeta;
                let importe_transferencia = response.data.ingreso_transferencia - response.data.gasto_transferencia;
                let importe_cheque = response.data.ingreso_cheque - response.data.gasto_cheque;
                let importe_sin_definir = response.data.ingreso_sin_definir - response.data.gasto_sin_definir;
                let importe_total = response.data.total_ingreso - response.data.total_gasto;

                 //Validacion de clases en gastos

                 if(importe_efectivo>0){
                    $("#importe-hoy-efectivo").removeClass('text-gray-500').addClass('text-gray-800')
                }
                if(importe_tarjeta>0){
                    $("#importe-hoy-tarjeta").removeClass('text-gray-500').addClass('text-gray-800')
                }
                if(importe_transferencia>0){
                    $("#importe-hoy-transferencia").removeClass('text-gray-500').addClass('text-gray-800')
                }
                if(importe_cheque>0){
                    $("#importe-hoy-cheque").removeClass('text-gray-500').addClass('text-gray-800')
                }
                if(importe_sin_definir>0){
                    $("#importe-hoy-sin-definir").removeClass('text-gray-500').addClass('text-gray-800')
                }

                if(importe_total>0){
                    $("#importe-hoy-total").removeClass('text-gray-500').addClass('text-gray-800')
                }


                $("#entrada-hoy-efectivo").text(efectivoMoneda)
                $("#entrada-hoy-tarjeta").text(tarjetaMoneda)
                $("#entrada-hoy-transferencia").text(transferenciaMoneda)
                $("#entrada-hoy-cheque").text(chequeMoneda)
                /* $("#entrada-hoy-deposito").text(depositoMoneda) */
                $("#entrada-hoy-sin-definir").text(sinDefinirMoneda)
                $("#entrada-hoy-total").text(entradaTotalMoneda)

                $("#gasto-hoy-efectivo").text(efectivoMonedaGasto)
                $("#gasto-hoy-tarjeta").text(tarjetaMonedaGasto)
                $("#gasto-hoy-transferencia").text(transferenciaMonedaGasto)
                $("#gasto-hoy-cheque").text(chequeMonedaGasto)
                /* $("#entrada-hoy-deposito").text(depositoMoneda) */
                $("#gasto-hoy-sin-definir").text(sinDefinirMonedaGasto)
                $("#gasto-hoy-total").text(gastoTotalMoneda)

                let importe_efectivo_moneda = importe_efectivo.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let importe_tarjeta_moneda = importe_tarjeta.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let importe_transferencia_moneda = importe_transferencia.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let importe_cheque_moneda = importe_cheque.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let importe_sin_definir_moneda = importe_sin_definir.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
                let importe_total_moneda = importe_total.toLocaleString('en-US', { style: 'currency', currency: 'USD' });

                $("#importe-hoy-efectivo").text(importe_efectivo_moneda)
                $("#importe-hoy-tarjeta").text(importe_tarjeta_moneda)
                $("#importe-hoy-transferencia").text(importe_transferencia_moneda)
                $("#importe-hoy-cheque").text(importe_cheque_moneda)
                /* $("#entrada-hoy-deposito").text(depositoMoneda) */
                $("#importe-hoy-sin-definir").text(importe_sin_definir_moneda)
                $("#importe-hoy-total").text(importe_total_moneda)
            }else{

            }
        }
    });
};

function ventasRelizadasHoy(){
   let id_sucursal = $('#sucursales-contenedor-ventas-hoy').attr('id_sucursal_seleccionada');
   let nombre_sucursal = $('#sucursales-contenedor-ventas-hoy').attr('nombre_sucursal_seleccionada');
   $("#titulo-ventas-hoy").text('Ventas realizadas - Sucursal: ' + nombre_sucursal)
   contenedor_opciones_inferiores.attr('tipo_opcion', 1);
   setearLoader('card-body-ventas-hoy',1);
   setTimeout(function(){$.ajax({
    type: "post",
    url: "url",
    url: "./modelo/metricas/ventas-hoy.php",
    data: {id_sucursal, 'tipo_accion':2},
    dataType: "json",
    success: function (response) {
        if(response.estatus){
            if(response.datos.length==0){
                setearLoader('card-body-ventas-hoy',2);
                $("#list-group-container").empty().append(`
                <li class="list-group-item text-center">${response.mensaje}</li>
                `)
            }else{
                setearLoader('card-body-ventas-hoy',2);
                $("#list-group-container").empty()
                let contador =0;
                response.datos.forEach(element => {
                    contador++;
                    if(element.tipo =='Normal'){
                        var celda_pdf = `traerPdf(${element.id});`;
                     }else if(element.tipo =='Apartado'){
                        var celda_pdf = `traerPdfApartado(${element.id});`;
                     }if(element.tipo =='Pedido'){
                        var celda_pdf = `traerPdfPedido(${element.id});`;
                     }
                    $("#list-group-container").append(`
                        <li class="list-group-item">
                            <div class="row">
                                    <div class="col-md-1">${contador}</div>
                                    <div class="col-md-3">${element.Nombre_Cliente}</div>
                                    <div class="col-md-1">${element.id}</div>
                                    <div class="col-md-2">${element.Total}</div>
                                    <div class="col-md-2">${element.sucursal}</div>
                                    <div class="col-md-2">${element.nombre}</div>
                                    <div class="col-md-1">
                                    <div style="display: flex"><button onclick="${celda_pdf}" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px"><span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>
                                    </div>
                            </div>
                        </li>
                        `)
                });

                $("#list-group-container").append(`
                 <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-3"></div>
                        <div class="col-md-1"><b>Total</b></div>
                        <div class="col-md-2" id="monto-total-ventas-realizar"></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-1"></div>
                    </div>
                </li>
                 `)
                $("#monto-total-ventas-realizar").text(response.total)
            }
        }
    }
   });}, 1335)

}
function creditosAbiertosHoy(){
    let id_sucursal = $('#sucursales-contenedor-ventas-hoy').attr('id_sucursal_seleccionada');
    let nombre_sucursal = $('#sucursales-contenedor-ventas-hoy').attr('nombre_sucursal_seleccionada');
    $("#titulo-ventas-hoy").text('Creditos abiertos - Sucursal: ' + nombre_sucursal)
    contenedor_opciones_inferiores.attr('tipo_opcion', 2);
    setearLoader('card-body-ventas-hoy',1);
    setTimeout(function(){$.ajax({
     type: "post",
     url: "url",
     url: "./modelo/metricas/ventas-hoy.php",
     data: {id_sucursal, 'tipo_accion':3},
     dataType: "json",
     success: function (response) {
         if(response.estatus){
             if(response.datos.length==0){
                 setearLoader('card-body-ventas-hoy',2);
                 $("#list-group-container").empty().append(`
                 <li class="list-group-item text-center">${response.mensaje}</li>
                 `)
             }else{
                 setearLoader('card-body-ventas-hoy',2);
                 $("#list-group-container").empty()
                 let contador =0;
                 response.datos.forEach(element => {
                     contador++;
                    
                     $("#list-group-container").append(`
                 <li class="list-group-item">
                     <div class="row">
                             <div class="col-md-1">${contador}</div>
                             <div class="col-md-3">${element.Nombre_Cliente}</div>
                             <div class="col-md-1">${element.folio}</div>
                             <div class="col-md-2">${element.Total}</div>
                             <div class="col-md-2">${element.sucursal}</div>
                             <div class="col-md-2">${element.nombre}</div>
                             <div class="col-md-1">
                             <button type="button" onclick="traerPdfCredito(${element.id})" class="btn ml-2 btn-danger"><span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button>   
                             </div>
                     </div>
                 </li>
                 `)
                 });

                 $("#list-group-container").append(`
                 <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-3"></div>
                        <div class="col-md-1"><b>Total</b></div>
                        <div class="col-md-2" id="monto-total-ventas-realizar"></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-1"></div>
                    </div>
                </li>
                 `)
                
                 $("#monto-total-ventas-realizar").text(response.total)
             }
         }
     }
    });}, 1335)
 
}
function abonosRealizados(){
    let id_sucursal = $('#sucursales-contenedor-ventas-hoy').attr('id_sucursal_seleccionada');
    let nombre_sucursal = $('#sucursales-contenedor-ventas-hoy').attr('nombre_sucursal_seleccionada');
    $("#titulo-ventas-hoy").text('Abonos realizados - Sucursal: ' + nombre_sucursal)
    contenedor_opciones_inferiores.attr('tipo_opcion', 3);
    setearLoader('card-body-ventas-hoy',1);
    setTimeout(function(){$.ajax({
     type: "post",
     url: "url",
     url: "./modelo/metricas/ventas-hoy.php",
     data: {id_sucursal, 'tipo_accion':4},
     dataType: "json",
     success: function (response) {
         if(response.estatus){
             if(response.datos.length==0){
                 setearLoader('card-body-ventas-hoy',2);
                 $("#list-group-container").empty().append(`
                 <li class="list-group-item text-center">${response.mensaje}</li>
                 `)
             }else{
                 setearLoader('card-body-ventas-hoy',2);
                 $("#list-group-container").empty()
                 let contador =0;
                 response.datos.forEach(element => {
                     contador++;
                    
                     $("#list-group-container").append(`
                 <li class="list-group-item">
                     <div class="row">
                             <div class="col-md-1">${contador}</div>
                             <div class="col-md-3">${element.cliente}</div>
                             <div class="col-md-1">${element.id_credito}</div>
                             <div class="col-md-2">${element.abono}</div>
                             <div class="col-md-2">${element.sucursal}</div>
                             <div class="col-md-2">${element.usuario}</div>
                             <div class="col-md-1">
                             <button type="button" onclick="pdfAbono(${element.id}, ${element.id_credito}, ${element.id_venta})" class="btn ml-2 btn-danger"><span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button>   
                             </div>
                     </div>
                 </li>
                 `)
                 });

                 $("#list-group-container").append(`
                 <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-3"></div>
                        <div class="col-md-1"><b>Total</b></div>
                        <div class="col-md-2" id="monto-total-ventas-realizar"></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-1"></div>
                    </div>
                </li>
                 `)
                
                 $("#monto-total-ventas-realizar").text(response.total)
             }
         }
     }
    });}, 1335)
}
function gastosRealizados(){
    let id_sucursal = $('#sucursales-contenedor-ventas-hoy').attr('id_sucursal_seleccionada');
    let nombre_sucursal = $('#sucursales-contenedor-ventas-hoy').attr('nombre_sucursal_seleccionada');
    $("#titulo-ventas-hoy").text('Gastos realizados - Sucursal: ' + nombre_sucursal)
    contenedor_opciones_inferiores.attr('tipo_opcion', 4);
    setearLoader('card-body-ventas-hoy',1);
    setTimeout(function(){$.ajax({
     type: "post",
     url: "url",
     url: "./modelo/metricas/ventas-hoy.php",
     data: {id_sucursal, 'tipo_accion':5},
     dataType: "json",
     success: function (response) {
         if(response.estatus){
             if(response.datos.length==0){
                 setearLoader('card-body-ventas-hoy',2);
                 $("#list-group-container").empty().append(`
                 <li class="list-group-item text-center">${response.mensaje}</li>
                 `)
             }else{
                 setearLoader('card-body-ventas-hoy',3);
                 $("#list-group-container").empty()
                 let contador =0;
                 response.datos.forEach(element => {
                     contador++;
                    
                     $("#list-group-container").append(`
                 <li class="list-group-item">
                     <div class="row">
                             <div class="col-md-1">${contador}</div>
                             <div class="col-md-3">${element.descripcion}</div>
                             <div class="col-md-2">${element.categoria}</div>
                             <div class="col-md-2">${element.monto}</div>
                             <div class="col-md-2">${element.sucursal}</div>
                             <div class="col-md-2">${element.usuario}</div>
                             
                     </div>
                 </li>
                 `)
                 });

                 $("#list-group-container").append(`
                 <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-3"></div>
                        <div class="col-md-2"><b>Total</b></div>
                        <div class="col-md-2" id="monto-total-ventas-realizar"></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                    </div>
                </li>
                 `)
                
                 $("#monto-total-ventas-realizar").text(response.total)
             }
         }
     }
    });}, 1335)
}


function setearLoader(id_contenedor, tipo_){
    if(tipo_==1){
     var html_contenedor = `
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
            <dotlottie-player src="https://lottie.host/b456286d-19b4-4303-be60-5eaea6bdacca/0V7LI9PuA7.json" background="transparent" speed="1" style="width: 120px; height: 120px;" loop autoplay></dotlottie-player>
            </div>
            <div class="col-12 d-flex justify-content-center">
            <span><b>Cargando ...</b></span>
            </div>
        </div>    
    `
    }else if(tipo_==2){
        var html_contenedor = `

        <ul class="list-group">
        <li class="list-group-item active">
        <div class="row">
                <div class="col-md-1">#</div>
                <div class="col-md-3">Cliente</div>
                <div class="col-md-1">Folio</div>
                <div class="col-md-2">Total</div>
                <div class="col-md-2">Sucursal</div>
                <div class="col-md-2">Vendedor</div>
                <div class="col-md-1">PDF</div>
            </div>
        </li>
        <div id="list-group-container"></div>
        </ul>
        `
    }else if(tipo_==3){
        var html_contenedor = `

        <ul class="list-group">
        <li class="list-group-item active">
        <div class="row">
                <div class="col-md-1">#</div>
                <div class="col-md-3">Descripción</div>
                <div class="col-md-2">Categoria</div>
                <div class="col-md-2"><b>Total</b></div>
                <div class="col-md-2">Sucursal</div>
                <div class="col-md-2">Vendedor</div>
            </div>
        </li>
        <div id="list-group-container"></div>
        </ul>
        `
    }else if(tipo_==4){
        var html_contenedor = `
        <div class="row no-gutters align-items-center">
        <div class="col-md-5">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Descripción</div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="text-sm mb-1 font-weight-bold text-gray-800">Efectivo</div>
                </div>
                <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                    
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="text-sm mb-1 font-weight-bold text-gray-800">Tarjeta</div>
                </div>
                <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                    
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="text-sm mb-1 font-weight-bold text-gray-800">Transferencia</div>
                </div>
                <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                    
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="text-sm mb-1 font-weight-bold text-gray-800">Cheque</div>
                </div>
                <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                    
                </div>
            </div>
            <!-- <div class="row">
                <div class="col-12 col-md-6">
                    <div class="text-sm mb-1 font-weight-bold text-gray-800">Deposito</div>
                </div>
                <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                    
                </div>
            </div> -->
            <div class="row mb-2">
                <div class="col-12 col-md-6">
                    <div class="text-sm mb-1 font-weight-bold text-gray-800">Sin definir</div>
                </div>
                <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                    
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="h5 mb-1 font-weight-bold text-gray-800">Total</div>
                </div>
                <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                    
                </div>
            </div>
        </div>
        <div class="col-md-3 text-center">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Ingreso</div>
            <div class="text-sm mb-1 font-weight-bold text-gray-500" id="entrada-hoy-efectivo">$0,00</div>
            <div class="text-sm mb-1 font-weight-bold text-gray-500" id="entrada-hoy-tarjeta">$0,00</div>
            <div class="text-sm mb-1 font-weight-bold text-gray-500" id="entrada-hoy-transferencia">$0,00</div>
            <div class="text-sm mb-1 font-weight-bold text-gray-500" id="entrada-hoy-cheque">$0,00</div>
            <!-- <div class="text-sm mb-1 font-weight-bold text-gray-800" id="entrada-hoy-deposito">$0,00</div> -->
            <div class="text-sm mb-3 font-weight-bold text-gray-500" id="entrada-hoy-sin-definir">$0,00</div>
            <div class="h5 mb-1 font-weight-bold text-gray-500" id="entrada-hoy-total">$0,00</div>
        </div>
        <div class="col-md-1 text-center">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                Gastos</div>
            <div class="text-sm mb-1 font-weight-bold text-gray-500" id="gasto-hoy-efectivo">$0,00</div>
            <div class="text-sm mb-1 font-weight-bold text-gray-500" id="gasto-hoy-tarjeta">$0,00</div>
            <div class="text-sm mb-1 font-weight-bold text-gray-500" id="gasto-hoy-transferencia">$0,00</div>
            <div class="text-sm mb-1 font-weight-bold text-gray-500" id="gasto-hoy-cheque">$0,00</div>
            <!-- <div class="text-sm mb-1 font-weight-bold text-gray-800" id="gasto-hoy-deposito">$0,00</div> -->
            <div class="text-sm mb-3 font-weight-bold text-gray-500" id="gasto-hoy-sin-definir">$0,00</div>
            <div class="h5 mb-1 font-weight-bold text-gray-500" id="gasto-hoy-total">$0,00</div>
        </div>
        <div class="col-md-3 text-center">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                Importe</div>
            <div class="text-sm mb-1 font-weight-bold text-gray-500" id="importe-hoy-efectivo">$0,00</div>
            <div class="text-sm mb-1 font-weight-bold text-gray-500" id="importe-hoy-tarjeta">$0,00</div>
            <div class="text-sm mb-1 font-weight-bold text-gray-500" id="importe-hoy-transferencia">$0,00</div>
            <div class="text-sm mb-1 font-weight-bold text-gray-500" id="importe-hoy-cheque">$0,00</div>
            <!-- <div class="text-sm mb-1 font-weight-bold text-gray-800" id="importe-hoy-deposito">$0,00</div> -->
            <div class="text-sm mb-3 font-weight-bold text-gray-500" id="importe-hoy-sin-definir">$0,00</div>
            <div class="h5 mb-1 font-weight-bold text-gray-500" id="importe-hoy-total">$0,00</div>
        </div>
    </div>
        `
    }
    $("#"+id_contenedor).empty().append(html_contenedor)
    
};

function traerPdfCredito(id) {
    window.open(
      "./modelo/creditos/generar-reporte-credito.php?id=" + id,
      "_blank"
    );
  }

  function traerPdfApartado(id){
    window.open("./modelo/apartados/reporte-venta-apartado.php?id="+id);
  }
  function traerPdfPedido(id){
    window.open("./modelo/pedidos/reporte-venta-pedido.php?id="+id);
  }


  function traerPdf(id){
    window.open('./modelo/ventas/reporte-venta.php?id='+ id , '_blank');
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


  function montosTotales(){
    let id_sucursal = $('#sucursales-contenedor-ventas-hoy').attr('id_sucursal_seleccionada');
    let nombre_sucursal = $('#sucursales-contenedor-ventas-hoy').attr('nombre_sucursal_seleccionada');
    $("#titulo-ventas-hoy").text('Gastos realizados - Sucursal: ' + nombre_sucursal)
    setearLoader('card-body-ventas-hoy',1);
    setTimeout(function(){
        
        setearLoader('card-body-ventas-hoy', 4);
        ventasHoy(id_sucursal)}, 1335);
  }


  function cambiarSucursalVentasHoy(id_sucursal, nombre_sucursal){
    let opcion_actual = contenedor_opciones_inferiores.attr('tipo_opcion');
    $('#sucursales-contenedor-ventas-hoy').attr('id_sucursal_seleccionada',id_sucursal);
    $('#sucursales-contenedor-ventas-hoy').attr('nombre_sucursal_seleccionada',nombre_sucursal);
  
    if(opcion_actual ==1){
        $("#titulo-ventas-hoy").text('Ventas realizadas - Sucursal: ' + nombre_sucursal)
        ventasRelizadasHoy()
    }else if(opcion_actual ==2){
        $("#titulo-ventas-hoy").text('Creditos abiertos - Sucursal: ' + nombre_sucursal)
        creditosAbiertosHoy()
    }else if(opcion_actual ==3){
        $("#titulo-ventas-hoy").text('Abonos realizados - Sucursal: ' + nombre_sucursal)
        abonosRealizados()
    }else if(opcion_actual ==4){ 
        $("#titulo-ventas-hoy").text('Gastos realizados - Sucursal: ' + nombre_sucursal)
        gastosRealizados()
     }else if(opcion_actual ==5){
        $("#titulo-ventas-hoy").text('Montos totales - Sucursal: ' + nombre_sucursal)
        montosTotales();
     }
  }