function MostrarClientes() {  
    $.fn.dataTable.ext.errMode = 'none';

table = $('#ventas').DataTable({
      
    serverSide: false,
    ajax: {
        method: "POST",
        url: "./modelo/clientes/traer-clientes.php",
        dataType: "json"
 
    },  

  columns: [   
    { title: "#",              data: null             },
    { title: "Codigo",         data: "id", render: function(data,type,row) {
        return '<span>R'+ data +'</span>';
        }},
    { title: "Nombre",         data: "nombre"         },
    { title: "Telefono",       data: "telefono"       },
    { title: "Direccion",      data: "direccion"      },
    { title: "Correo",         data: "correo"         },
    { title: "Credito",        data: "credito", render: function (data) {  
            return '<span class="badge badge-info">'+ data +'</span>';
     }},
    { title: "RFC",            data: "rfc"            },
    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
        return '<div style="display: flex"><button onclick="traerPdf(' +row.folio+ ');" type="button" class="buttonPDF btn btn-danger" style="margin-right: 8px"><span class="fa fa-file-pdf"></span><span class="hidden-xs"></span></button><br>'+
        '<button type="button" onclick="borrarVenta('+ row.folio +');" class="buttonBorrar btn btn-warning"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
      },
    },
  ],
  paging: true,
  searching: true,
  scrollY: "50vh",
  info: false,
  responsive: false,
  order: [2, "desc"],
 
  
});

$("table.dataTable thead").addClass("table-info")

 //Enumerar las filas "index column"
 table.on( 'order.dt search.dt', function () {
    table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
        cell.innerHTML = i+1;
       
    } );
} ).draw();

}

MostrarClientes();


function borrarVenta(id) {

    Swal.fire({
        title: "Eliminar Venta",
        html: '<span>¿Estas seguro de eliminar esta cliente?</span>',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        cancelButtonColor: '#00e059',
        showConfirmButton: true,
        confirmButtonText: 'Borrar', 
        cancelButtonColor:'#ff764d',
        focusConfirm: false }).then((result) => { 
        
            if(result.isConfirmed){    

    $.ajax({
        type: "post",
        url: "./modelo/ventas/editar-cliente.php",
        data: {"folio": id},
        success: function (response) {
           if (response==1) {
              
            Swal.fire({
                title: 'Cliente eliminado',
                html: "<span>El cliente se eliminó con exito</span>",
                icon: "success",
                cancelButtonColor: '#00e059',
                showConfirmButton: true,
                confirmButtonText: 'Aceptar', 
                cancelButtonColor:'#ff764d',
            }).then((result) => {  

                if(result.isConfirmed){
                    location.reload();
                }});

           
           }else{
            Swal.fire({
                title: 'Cliente no eliminado',
                html: "<span>El cliente no se pudo eliminar, dedido a algun error inesperado</span>",
                icon: "warning",
                cancelButtonColor: '#00e059',
                showConfirmButton: true,
                confirmButtonText: 'Aceptar', 
                cancelButtonColor:'#ff764d',
                showDenyButton: true,
                
            }).then((result) => {  

                if(result.isConfirmed){
                    location.reload();
                }});
           }
        }
    });
}

        });
  }


  function traerPdf(folio){
    window.open('./modelo/ventas/generar-reporte-venta.php?id='+ folio , '_blank');
  }


  function agregarCliente(){

    Swal.fire({
        title: "Agregar cliente",
        showCancelButton: true,
            cancelButtonText: 'Cerrar',
            cancelButtonColor: '#00e059',
            showConfirmButton: true,
            confirmButtonText: 'Agregar', 
            cancelButtonColor:'#ff764d',
            focusConfirm: false,
            iconColor : "#36b9cc",
        html: '<form class="mt-4" id="agregar-cliente-form">'+
        
        '<div class="row">'+

           '<div class="col-12">'+
           '<div class="form-group">'+
           '<label><b>Nombre:</b></label></br>'+
           '<input class="form-control" type="text" id="nombre-cliente" name="nombre" placeholder="Nombre completo">'+
              '</div>'+
              '</div>'+
           '</div>'+
    
        '<div class="row">'+
            '<div class="col-6">'+
            '<div class="form-group">'+
            '<label><b>Credito</b></label>'+
            '<select class="form-control" value="" id="credito" name="credito">'+
            '<option value="sin credito">Sin credito </option>'+
            '</select>'+
          
    
    
       ' </div>'+
        '</div>'+
        
        
       '<div class="col-6">'+
        '<div class="form-group">'+
        '<label for="telefono"><b>Telefono:</b></label></br>'+
        '<input type="number" class="form-control" id="telefono" value="" name="telefono" placeholder="Telefono" autocomplete="off">'+
        '</div>'+
        '</div>'+
    
        
            '<div class="col-7">'+
            '<div class="form-group">'+
             
            '<label><b>Correo:</b></label></br>'+
              '<input type="text" name="correo" id="correo" value="" class="form-control" placeholder="Correo">'+
        '</div>'+
            '</div>'+
    
           
    
            '<div class="col-5">'+
            '<div class="form-group">'+
            '<label><b>RFC</b></label>'+
            '<input type="text" class="form-control" value="" id="rf" name="rfc" placeholder="RFC">'+
            '</div>'+
            '</div>'+
    
           
        '<div class="col-12">'+
            '<div class="form-group">'+
                '<label><b>Dirección</b></label>'+
                '<textarea type="text" class="form-control" value="" name="direccion" id="direccion" placeholder="Escribe la dirección del cliente">'+
                '</textarea>'+
            '</div>'+
        '</div>'+
        '<div class="col-12">'+
            '<div class="form-group">'+
                '<label><b>Mapear dirección</b></label>'+
                '<div id="map-id"></div>'+
                '<div class="alert alert-info" id="label-coord">Cordenadas del marcador</div>'+
                '<pre id="coordenadas"></pre>'+
            '</div>'+
        '</div>'+
    
        '</div>'+
    


            '<div>'+
    '</form>',

    });

   /* var mymap = L.map('map-id').setView([25.8587547, -97.5136721], 13);

//Creando mapa
L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
    attribution: 'Contribuidores de los datos del mapa &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>, Imagenes de © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox/streets-v11',
    tileSize: 512,
    zoomOffset: -1,
    accessToken: 'pk.eyJ1IjoiYnJheWFubTAzIiwiYSI6ImNrbnRlNTdyZzAxcXcycG84ZnRvNnJtdmoifQ.8k-_U2-Eq-CmSrH6jm8KEg'
}).addTo(mymap);

//Marca de agua
L.Control.Watermark = L.Control.extend({
    onAdd: function(mymap) {
        var img = L.DomUtil.create('img');

        img.src = 'src/img/Optimized-logo-horizintal.png';
        img.style.width = '100px';
        img.style.top = '-22px'

        return img;
    },

    onRemove: function(mymap) {
        // Nothing to do here
    }


});

L.control.watermark = function(opts) {
    return new L.Control.Watermark(opts);
}

L.control.watermark({ position: 'bottomleft' }).addTo(mymap);*/
mapboxgl.accessToken = 'pk.eyJ1IjoiYnJheWFubTAzIiwiYSI6ImNrbnRlNTdyZzAxcXcycG84ZnRvNnJtdmoifQ.8k-_U2-Eq-CmSrH6jm8KEg';

var mymap = new mapboxgl.Map({
    container: 'map-id',
    style: 'mapbox://styles/mapbox/streets-v11',
    center: [-97.51302566191197,25.860074578125104],
    zoom: 13
    }); 

    var nav = new mapboxgl.NavigationControl();

mymap.addControl(
  new MapboxGeocoder({
      accessToken: mapboxgl.accessToken,
      mapboxgl: mapboxgl,
  })
);

mymap.addControl(nav,"top-left");
mymap.addControl(new mapboxgl.FullscreenControl());

mymap.addControl(new mapboxgl.GeolocateControl({
    positionOptions: {
        enableHighAccuracy: true
    },
    trackUserLocation: true
}));

/*mymap.on('mousemove', function (e) {
    document.getElementById('coordenadas').innerHTML =
        JSON.stringify(e.lngLat);
});*/

var coordinates = document.getElementById('coordenadas');

var marker = new mapboxgl.Marker({
    draggable: true
    })
    .setLngLat([-97.51302566191197,25.860074578125104])
    
    .addTo(mymap);
     
    function onDragEnd() {
    var lngLat = marker.getLngLat();
    coordinates.style.display = 'block';
    coordinates.innerHTML =
    '<span class="badge badge-info coord"> Longitud:  </span>' + lngLat.lng + '<br /><span class="badge badge-info coord">Latitud: </span> ' + lngLat.lat;
    }
     
    marker.on('dragend', onDragEnd);

    

  };

 

 