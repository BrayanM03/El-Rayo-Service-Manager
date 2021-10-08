
$.ajax({
    type: "POST",
    url: "./modelo/creditos/traer-creditos-vencidos.php",
    data: "data",
    dataType: "JSON",
    success: function (response) {
/*       
      response.forEach(element => {
        
        element.forEach(function (value, index) { 

            cliente = value["cliente"];
            pagado = value["pagado"];
            restante = value["restante"]
            fecha_inicio = value["fecha_inicio"];
            fecha_final = value["fecha_final"];

            $("#lista_splides").append('<li class="splide__slide">'+
            '<div class="slide_credito">'+
                '<div class="row mt-2">'+
                '<div class="col-12 col-md-12 text-center">'+
                 '<span style="font-size: 18px;">Cliente: <b>' + cliente + '</b></span>'+
                '</div>'+
                '</div>'+
               '<div class="row mt-2 p-2">'+
               '<div class="col-12 col-md-3 text-center">'+
                'pagado: </br><b>$' + pagado + '</b>'+
                '</div> '+
                '<div class="col-12 col-md-3 text-center">'+
                'restante: </br><b>$' + restante + '</b>'+
                '</div>'+
                '<div class="col-12 col-md-3 text-center">'+
                'fecha inicial:</br> <b>' + fecha_inicio + '</b>'+
                '</div>'+ 
                '<div class="col-12 col-md-3 text-center">'+
                 'fecha final:</br> <b>' + fecha_inicio + '</b>'+
                '</div>'+ 
               '</div>'+
            '</div>'+
        '</li>')

         });
        
      });
 */
      for (element of response) {
       
        cliente = element.cliente; //= value["cliente"];
        pagado = element.pagado; //= value["pagado"];
        restante = element.restante; //= value["restante"]
        fecha_inicio = element.fecha_inicio; //= value["fecha_inicio"];
        fecha_inicio = element.fecha_final; //= value["fecha_final"];
        id_cred = element.id; 

        $("#lista_splides").append('<li class="splide__slide">'+
        '<div class="slide_credito">'+
            '<div class="row mt-2">'+
            '<div class="col-12 col-md-12 text-center">'+
             '<span style="font-size: 18px;">Cliente: <b>' + cliente + ' CRED ' + id_cred + '</b></span>'+
            '</div>'+
            '</div>'+
           '<div class="row mt-2 p-2">'+
           '<div class="col-12 col-md-3 text-center">'+
            'pagado: </br><b>$' + pagado + '</b>'+
            '</div> '+
            '<div class="col-12 col-md-3 text-center">'+
            'restante: </br><b>$' + restante + '</b>'+
            '</div>'+
            '<div class="col-12 col-md-3 text-center">'+
            'fecha inicial:</br> <b>' + fecha_inicio + '</b>'+
            '</div>'+ 
            '<div class="col-12 col-md-3 text-center">'+
             'fecha final:</br> <b>' + fecha_inicio + '</b>'+
            '</div>'+ 
           '</div>'+
        '</div>'+
    '</li>')

      }

      
var splide = new Splide( '.splide', {
 // direction: 'ttb',
  height   : '10rem',
  wheel    : true,
  pagination: false,
});

splide.on( 'autoplay:playing', function ( rate ) {
  console.log( rate ); // 0-1
} );

splide.mount();

    }
});



