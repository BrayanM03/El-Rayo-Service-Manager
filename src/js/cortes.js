//Traer información 

$.ajax({
    type: "POST",
    url: "./modelo/cortes/ventas-sucursal-hoy.php",
    data: "data",
    dataType: "JSON",
    success: function (response) {
      $("#ganancia-sendero").text(response.ganancia_sendero);
      $("#ganancia-pedro").text(response.ganancia_pedro); 
      console.log(response.ganancia_hoy);
    }
}); 

