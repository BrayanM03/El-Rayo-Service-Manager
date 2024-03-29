// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

function number_format(number, decimals, dec_point, thousands_sep) {
  // *     example: number_format(1234.56, 2, ',', ' ');
  // *     return: '1 234,56'
  number = (number + '').replace(',', '').replace(' ', '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

function graficaBarGeneral(){
  $("#myBarChart").remove();
  $("#chart-bar-container").append(" <canvas id='myBarChart'></canvas>");
  $("#titulo-graf").text("");
  //Ganancia semanal
$.ajax({
  type: "POST",
  url: "./modelo/metricas/grafica-barra-ganancia-semanal.php",
  data: "data",
  dataType : "JSON",
  success: function (response) {
   ganancia_semanal = number_format(response.ganancia_semanal, 2, '.', ','); 
   ganancia_hoy = number_format(response.ganancia_hoy, 2, '.', ','); 
  $("#ganancia_semana").text("$" + ganancia_semanal);
  $("#ganancia_hoy").text("$" + ganancia_hoy);
  $("#ventas_hoy").text(response.ventas_hoy);
    
    // Bar Chart Example
var ctx = document.getElementById("myBarChart");
var myBarChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado", "Domingo"],
    datasets: [{
      label: "Ganancia:",
      backgroundColor: "rgb(222, 208, 11)",
      hoverBackgroundColor: "#ffcf4d",
      borderColor: "#4e73df",
      data: [response.ganancia_lunes, response.ganancia_martes, response.ganancia_miercoles, response.ganancia_jueves, response.ganancia_viernes, response.ganancia_sabado, response.ganancia_domingo],
    }],
  },
  options: {
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: 10,
        right: 25,
        top: 25,
        bottom: 0
      }
    },
    scales: {
      xAxes: [{
        time: {
          unit: 'month'
        },
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          maxTicksLimit: 6
        },
        maxBarThickness: 25,
      }],
      yAxes: [{
        ticks: {
          min: 0,
        
          maxTicksLimit: 5,
          padding: 10,
          // Include a dollar sign in the ticks
          callback: function(value, index, values) {
            return '$' + number_format(value);
          }
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }],
    },
    legend: {
      display: false
    },
    tooltips: {
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
      callbacks: {
        label: function(tooltipItem, chart) {
          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
          return datasetLabel + ': $' + number_format(tooltipItem.yLabel);
        }
      }
    },
  }
});



  }
});

}

graficaBarGeneral();

function graficaBarPorSucursal(id_suc, background, hover, sweet){
  $("#myBarChart").remove();
  $("#chart-bar-container").append(" <canvas id='myBarChart'></canvas>");
  $("#titulo-graf").text("(Sucursal Pedro Cardenas)");

//Ganancia semanal
$.ajax({
  type: "POST",
  url: "./modelo/metricas/grafica-barra-ganancia-semanal-sucursal.php",
  data: {"id_suc": id_suc},
  dataType : "JSON",
  success: function (response) {
   ganancia_semanal = number_format(response.ganancia_semanal, 2, '.', ','); 
   ganancia_hoy = number_format(response.ganancia_hoy, 2, '.', ','); 
  $("#ganancia_semana").text("$" + ganancia_semanal);
  $("#ganancia_hoy").text("$" + ganancia_hoy);
    
    // Bar Chart Example
var ctx = document.getElementById("myBarChart");
var myBarChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado", "Domingo"],
    datasets: [{
      label: "Ventas:",
      backgroundColor: sweet,
      hoverBackgroundColor: hover,
      borderColor: background,
      data: [response.ganancia_lunes, response.ganancia_martes, response.ganancia_miercoles, response.ganancia_jueves, response.ganancia_viernes, response.ganancia_sabado, response.ganancia_domingo],
    }],
  },
  options: {
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: 10,
        right: 25,
        top: 25,
        bottom: 0
      }
    },
    scales: {
      xAxes: [{
        time: {
          unit: 'month'
        },
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          maxTicksLimit: 6
        },
        maxBarThickness: 25,
      }],
      yAxes: [{
        ticks: {
          min: 0,
          maxTicksLimit: 5,
          padding: 10,
          // Include a dollar sign in the ticks
          callback: function(value, index, values) {
            return '$' + number_format(value);
          }
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }],
    },
    legend: {
      display: false
    },
    tooltips: {
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
      callbacks: {
        label: function(tooltipItem, chart) {
          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
          return datasetLabel + ': $' + number_format(tooltipItem.yLabel);
        }
      }
    },
  }
});

  }
});
};


function traerInfoRangoFecha(){
  fecha_inicio = $("#fecha-inicio").val();
  fecha_final = $("#fecha-final").val();
  console.log(fecha_inicio);
  console.log(fecha_final);

  if(fecha_inicio == ""){
    $("#fecha-inicio").addClass("is-invalid");
    console.log("Elige una fecha inicial.");
  }else{
    $("#fecha-inicio").removeClass("is-invalid");
  }

  if(fecha_final == ""){
    $("#fecha-final").addClass("is-invalid");
    console.log("Elige una fecha final.");
  }else{
    $("#fecha-final").removeClass("is-invalid");
  }

  if(fecha_final != "" && fecha_inicio != ""){
    $.ajax({
      type: "POST",
      url: "./modelo/panel/ganancia-rango-fechas.php",
      data: {"fecha_inicial": fecha_inicio, "fecha_final": fecha_final},
      dataType : "JSON",
      success: function (response) {

        
        ganancia_rango = number_format(response.ganancia_rango, 2, '.', ',');

        ganancias_sucursales = response.ganancia_suc;

        ganancias_sucursales.forEach(element => {
          
          console.log(element);
          let id_sucursal = element.id;
        $("#result-ganancia-rango-"+id_sucursal).text("$"  + element.ganancia);
          //ganancia_rango_pedro = number_format(response.ganancia_rango_pedro, 2, '.', ',');

        });/* 
        ganancia_rango_pedro = number_format(response.ganancia_rango_pedro, 2, '.', ',');
        ganancia_rango_sendero = number_format(response.ganancia_rango_sendero, 2, '.', ',');

        $("#result-ganancia-rango").text("$"  + ganancia_rango);
        $("#result-ganancia-rango-pedro").text("$"  + ganancia_rango_pedro);
        $("#result-ganancia-rango-sendero").text("$"  + ganancia_rango_sendero); */
      }
    });
  }
 

};

graficaBarTopMedida()
function graficaBarTopMedida(){
  $("#grafica-bar-medidas").remove();
  $("#chart-bar-medida-container").append(" <canvas id='grafica-bar-medidas'></canvas>");
  $("#titulo-graf").text("");
  //Ganancia semanal
$.ajax({
  type: "POST",
  url: "./modelo/metricas/grafica-barra-top-medida.php",
  data: "data",
  dataType : "JSON",
  success: function (response) {
  /*  ganancia_semanal = number_format(response.ganancia_semanal, 2, '.', ','); 
   ganancia_hoy = number_format(response.ganancia_hoy, 2, '.', ','); 
  $("#ganancia_semana").text("$" + ganancia_semanal);
  $("#ganancia_hoy").text("$" + ganancia_hoy);
  $("#ventas_hoy").text(response.ventas_hoy); */
    
    // Bar Chart Example
var ctx = document.getElementById("grafica-bar-medidas");
var myBarChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: response.medidas,
    datasets: [{
      label: "Cantidad:",
      backgroundColor: "rgb(152, 47, 47)",
      hoverBackgroundColor: "#f50b0b",
      borderColor: "#4e73df",
      data: response.cantidades,
    }],
  },
  options: {
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: 10,
        right: 25,
        top: 25,
        bottom: 0
      }
    },
    scales: {
      xAxes: [{
        time: {
          unit: 'month'
        },
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          maxTicksLimit: 6
        },
        maxBarThickness: 25,
      }],
      yAxes: [{
        ticks: {
          min: 0,
        
          maxTicksLimit: 5,
          padding: 10,
          userCallback: function(label, index, labels) {
            // when the floored value is the same as the value we have a whole number
            if (Math.floor(label) === label) {
                return label;
            }

        },
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }],
    },
    legend: {
      display: false
    },
    tooltips: {
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
      /* callbacks: {
        label: function(tooltipItem, chart) {
          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
          return datasetLabel + ': $' + number_format(tooltipItem.yLabel);
        }
      } */
    },
  }
});



  }
});

}

// Función para marcar la opción seleccionada con una palomita
function marcarSeleccionado(element, id_identificador, origen_ventas_hoy=false, id_sucursal=false) {
  console.log(origen_ventas_hoy);
  console.log(id_identificador);
  if(origen_ventas_hoy){
    $("#sucursales-contenedor-ventas-hoy").attr('id_sucursal_seleccionada', id_sucursal);
  }
  // Remover la clase de marca de todas las opciones
  var dropdownItems = document.querySelectorAll(`#${id_identificador} .dropdown-item`);
  dropdownItems.forEach(function(item) {
      item.classList.remove('opcion-seleccionada');
  });
  // Agregar la clase de marca a la opción seleccionada
  element.classList.add('opcion-seleccionada');
  bandera_grafica_area_fusionar = element.getAttribute('bandera_fusionar')
}


