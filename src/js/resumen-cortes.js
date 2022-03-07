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
        
        //Ventas
              $("#venta_total").text("$"+ response.venta_total);
              $("#venta_efectivo").text("$"+ response.venta_total_efectivo);
              $("#venta_tarjeta").text("$"+ response.venta_total_tarjeta);
           $("#venta_cheque").text("$"+ response.venta_total_cheque);
            $("#venta_transferencia").text("$"+ response.venta_total_transferencia);
              $("#venta_sin_definir").text("$"+ response.venta_total_sin_definir);
           $("#ventas_realizadas").text(response.numero_ventas);

           //Ganancia
            $("#ganancia_dia").text("$"+ response.ganancia_total);
         $("#ganancia_efectivo").text("$"+ response.ganancia_efectivo);
             $("#ganancia_tarjeta").text("$"+ response.ganancia_tarjeta);
          $("#ganancia_cheque").text("$"+ response.ganancia_cheque);
           $("#ganancia_transferencia").text("$"+ response.ganancia_transferencia);
             $("#ganancia_sin_definir").text("$"+ response.ganancia_sin_definir);

            //Creditos

            $("#creditos_realizados").text(  response.creditos_realizados);
            $("#creditos_pagados").text(response.creditos_pagados);
            $("#abonos_realizados").text(response.abonos_realizados);

            abonos = response.abonos;
            abonos.forEach(function( item, value){  
                index = value + 1;
                $("#lista_creditos").append('<li class="list-group-item">'+ index  +".- "+ item['cliente'] +': <span class="badge badge-primary badge-pill">'+ item['abono'] +'</span></li>');
             
            });

    }
});