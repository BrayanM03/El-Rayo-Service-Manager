// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Pie Chart Example
function totalVentas(){

  $("#myPieChart").remove();
  $("#chart-pie-container").append("<canvas id='myPieChart'></canvas>");
  $("#titulo-graf-pie").text("Total ventas por sucursal");

  $.ajax({
    type: "POST",
    url: "./modelo/panel/grafica-pastel.php", 
    data: {"data":"data"},
    dataType: "JSON",
    success: function (response) {
  
      var ctx = document.getElementById("myPieChart");
  var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ["Pedro Cardenas", "Sendero"],
      datasets: [{
        data: [response.ganancia_pedro,response.ganancia_sendero],
        backgroundColor: ['#4e73df', '#1cc88a'],
        hoverBackgroundColor: ['#2e59d9', '#17a673'],
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
    url: "./modelo/panel/grafica-pastel-numero-ventas.php", 
    data: {"data":"data"},
    dataType: "JSON",
    success: function (response) {
  
      var ctx = document.getElementById("myPieChart");
  var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ["Pedro Cardenas", "Sendero"],
      datasets: [{
        data: [response.numero_ventas_pedro,response.numero_ventas_sendero],
        backgroundColor: ['#4e73df', '#1cc88a'],
        hoverBackgroundColor: ['#2e59d9', '#17a673'],
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
    url: "./modelo/panel/grafica-pastel-creditos.php", 
    data: {"data":"data"},
    dataType: "JSON",
    success: function (response) {
  
      var ctx = document.getElementById("myPieChart");
  var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ["Pedro Cardenas", "Sendero"],
      datasets: [{
        data: [response.creditos_pedro,response.creditos_sendero],
        backgroundColor: ['#4e73df', '#1cc88a'],
        hoverBackgroundColor: ['#2e59d9', '#17a673'],
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



