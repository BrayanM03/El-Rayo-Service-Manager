function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

sucursal = getParameterByName('sucursal');

$.ajax({
    type: "POST",
    url: "./modelo/cortes/resumen-corte.php",
    data: {"sucursal": sucursal},
    dataType: "JSON",
    success: function (response) { 

        r_venta_total = round(response.venta_total);
        r_venta_total_efectivo = round(response.venta_total_efectivo);
        r_venta_total_tarjeta = round(response.venta_total_tarjeta);
        r_venta_total_cheque = round(response.venta_total_cheque);
        r_venta_total_transferencia = round(response.venta_total_transferencia);
        r_venta_total_sin_definir = round(response.venta_total_sin_definir);

        r_ganancia_total = round(response.ganancia_total);
        r_ganancia_efectivo = round(response.ganancia_efectivo);
        r_ganancia_tarjeta = round(response.ganancia_tarjeta);
        r_ganancia_cheque = round(response.ganancia_cheque);
        r_ganancia_transferencia = round(response.ganancia_transferencia);
        r_ganancia_sin_definir = round(response.ganancia_sin_definir);

        
        //Ventas
              $("#venta_total").text("$"+ r_venta_total);
              $("#venta_efectivo").text("$"+ r_venta_total_efectivo);
              $("#venta_tarjeta").text("$"+ r_venta_total_tarjeta);
           $("#venta_cheque").text("$"+ r_venta_total_cheque);
            $("#venta_transferencia").text("$"+ r_venta_total_transferencia);
              $("#venta_sin_definir").text("$"+ r_venta_total_sin_definir);
           $("#ventas_realizadas").text(response.numero_ventas);

           //Ganancia
            $("#ganancia_dia").text("$"+ r_ganancia_total);
         $("#ganancia_efectivo").text("$"+ r_ganancia_efectivo);
             $("#ganancia_tarjeta").text("$"+ r_ganancia_tarjeta);
          $("#ganancia_cheque").text("$"+ r_ganancia_cheque);
           $("#ganancia_transferencia").text("$"+ r_ganancia_transferencia);
             $("#ganancia_sin_definir").text("$"+ r_ganancia_sin_definir);

            //Creditos

            $("#creditos_realizados").text( response.creditos_realizados);
            $("#creditos_pagados").text(response.creditos_pagados);
            $("#abonos_realizados").text(response.abonos_realizados);

            abonos = response.abonos;
            abonos.forEach(function( item, value){  
                index = value + 1;
                $("#lista_creditos").append('<li class="list-group-item">'+ index  +".- "+ item['cliente'] +': <span class="badge badge-primary badge-pill">'+ item['abono'] +'</span></li>');
             
            });

    }
});


function round(num) {
    var m = Number((Math.abs(num) * 100).toPrecision(15));
    return Math.round(m) / 100 * Math.sign(num);
  }