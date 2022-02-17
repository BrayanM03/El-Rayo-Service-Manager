
 function ganancias() { 

    $.ajax({
        type: "POST",
        url: "./modelo/panel/estadisticas-generales.php",
        data: {"data": "data"},
        dataType: "JSON",
        success: function (response) {

            gananciaMes = number_format_js(response.ganancia_mes,2,'.',','); 
            gananciaAnual = number_format_js(response.ganancia_anual,2,'.',','); 
            console.log(gananciaMes);

            $("#ganancias-mes-actual").text("$ " + gananciaMes);
            $("#ganancia-anual-actual").text("$ " + gananciaAnual);
            $("#total_ventas").text( response.total_venta);
            $("#creditos_pendientes").text( response.creditos_pendientes);
        
        }
    });

    
  }
 
 ganancias();

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
  

