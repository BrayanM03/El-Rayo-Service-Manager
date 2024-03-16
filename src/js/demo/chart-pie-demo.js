// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Pie Chart Example ``````
function totalVentas(){

  $("#myPieChart").remove();
  $("#chart-pie-container").append("<canvas id='myPieChart'></canvas>");
  $("#titulo-graf-pie").text("Total ventas por sucursal");

  $.ajax({
    type: "POST",
    url: "./modelo/metricas/grafica-pastel.php", 
    data: {"data":"data"},
    dataType: "JSON",
    success: function (response) { 
  
      nombres = [];
      ganancia = [];
      colores_back = [];
      colores_hover = [];
      $("#store-tags").empty();
      response.forEach(element => {
        nombre_sucursal = element.sucursal;
        venta_total = element.venta_total;
        color_back = element.color_back;
        color_hover = element.color_hover;

  
        $("#store-tags").append('<span class="mr-2">'+
          '<i class="fas fa-circle" style="color:'+ color_back+'"></i>' + nombre_sucursal + 
          '</span>');

        nombres.push(nombre_sucursal);
        ganancia.push(venta_total);
        colores_back.push(color_back);
        colores_hover.push(color_hover);
      });


      var ctx = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: nombres,
      datasets: [{
        data: ganancia,
        backgroundColor: colores_back,
        hoverBackgroundColor: colores_hover,
        hoverBorderColor: "rgba(234, 236, 244, 1)",
      }],
    },
    options: {
      maintainAspectRatio: false,
      tooltips: {
        backgroundColor: "rgb(255,255,255)",
        bodyFontColor: "#858796",
        borderColor: '#dddfeb',
        borderWidth: 1,
        xPadding: 15,
        yPadding: 15,
        displayColors: false,
        caretPadding: 10,
      },
      legend: {
        display: false
      },
      cutoutPercentage: 80,
    },
  });
  
  
    }
  });

}

totalVentas();



// Pie Chart Example
function numeroVentas(){

  $("#myPieChart").remove();
  $("#chart-pie-container").append("<canvas id='myPieChart'></canvas>");
  $("#titulo-graf-pie").text("Numero ventas por sucursal");

  $.ajax({
    type: "POST",
    url: "./modelo/metricas/grafica-pastel-numero-ventas.php", 
    data: {"data":"data"},
    dataType: "JSON",
    success: function (responses) {

      nombres = [];
      totales = [];
      colores_back = [];
      colores_hover = [];

      $("#store-tags").empty();
      responses.forEach(element => {
        nombre_sucursal = element.sucursal;
        venta_total = element.numero_ventas;
        color_back = element.color_back;
        color_hover = element.color_hover;

        $("#store-tags").append('<span class="mr-2">'+
          '<i class="fas fa-circle" style="color:'+ color_back+'"></i>' + nombre_sucursal + 
          '</span>');

        nombres.push(nombre_sucursal);
        totales.push(venta_total);
        colores_back.push(color_back);
        colores_hover.push(color_hover);
      });

  
      var ctx = document.getElementById("myPieChart");
  var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: nombres,
      datasets: [{
        data: totales,
        backgroundColor: colores_back,
        hoverBackgroundColor: colores_hover,
        hoverBorderColor: "rgba(234, 236, 244, 1)",
      }],
    },
    options: {
      maintainAspectRatio: false,
      tooltips: {
        backgroundColor: "rgb(255,255,255)",
        bodyFontColor: "#858796",
        borderColor: '#dddfeb',
        borderWidth: 1,
        xPadding: 15,
        yPadding: 15,
        displayColors: false,
        caretPadding: 10,
      },
      legend: {
        display: false
      },
      cutoutPercentage: 80,
    },
  });
  
  
    }
  });

}



// Pie Chart Example
function totalCreditos(){

  
  $("#myPieChart").remove();
  $("#chart-pie-container").append("<canvas id='myPieChart'></canvas>");
  $("#titulo-graf-pie").text("Numero ventas por sucursal");

  $.ajax({
    type: "POST",
    url: "./modelo/metricas/grafica-pastel-creditos.php", 
    data: {"data":"data"},
    dataType: "JSON",
    success: function (response) {

      nombres = [];
      totales = [];
      colores_back = [];
      colores_hover = [];

      $("#store-tags").empty();
      response.forEach(element => {
        nombre_sucursal = element.sucursal;
        cred_total = element.total_cred;
        color_back = element.color_back;
        color_hover = element.color_hover;

        $("#store-tags").append('<span class="mr-2">'+
          '<i class="fas fa-circle" style="color:'+ color_back+'"></i>' + nombre_sucursal + 
          '</span>');

        nombres.push(nombre_sucursal);
        totales.push(cred_total);
        colores_back.push(color_back);
        colores_hover.push(color_hover);
      });

  
      var ctx = document.getElementById("myPieChart");
  var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: nombres,
      datasets: [{
        data: totales,
        backgroundColor: colores_back,
        hoverBackgroundColor: colores_hover,
        hoverBorderColor: "rgba(234, 236, 244, 1)",
      }],
    },
    options: {
      maintainAspectRatio: false,
      tooltips: {
        backgroundColor: "rgb(255,255,255)",
        bodyFontColor: "#858796",
        borderColor: '#dddfeb',
        borderWidth: 1,
        xPadding: 15,
        yPadding: 15,
        displayColors: false,
        caretPadding: 10,
      },
      legend: {
        display: false
      },
      cutoutPercentage: 80,
    },
  });
  
  
    }
  });

}



