function MostrarClientes() {  
    $.fn.dataTable.ext.errMode = 'none';
    id_sesion = $("#emp-title").attr("sesion_id");
    rol_sesion = $("#emp-title").attr("sesion_rol");

table = $('#ventas').DataTable({
      
    serverSide: false,
    processing: true,
    serverSide: true,
    ajax:'./modelo/clientes/traer-clientes.php',
    rowCallback: function(row, data, index) {
        var info = this.api().page.info();
        var page = info.page;
        var length = info.length;
        var columnIndex = 0; // Índice de la primera columna a enumerar
  
        $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
      },
  columns: [   
    { title: "#",              data: null             },
    { title: "Codigo",         data: 0, render: function(data,type,row) {
        return '<span>R'+ data +'</span>';
        },
        orderData: [1]},
        
    { title: "id",            data: "id"         },
    { title: "Nombre",         data: 1         },
    { title: "Telefono",       data: 2       },
    { title: "Direccion",      data: 3 , width: "20%"  },
    { title: "Correo",         data: 4         },
    { title: 'Asesor',          data: 7 },
    { title: "Credito",        data: 5, render: function (data) {  
        if (data == 1) {
            return '<span class="badge badge-warning">Con credito</span>';
        }else if(data == 0){
            return '<span class="badge badge-info">Sin credito</span>'; 
        }
            
     }},
    { title: "RFC",            data: 6       },
    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
        
        if(rol_sesion == 1){ //Esta configuracion es especifica para el usuario de Mario y Amita se debe en un furturo hacer mas dinamico
            return '<div style="display: flex"><button onclick="editarCliente(' +row[0]+ ');" type="button" class="buttonPDF btn btn-success" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button><br>';
                  // '<div style="display: flex"><button onclick="editarCliente(' +row[0]+ ');" type="button" class="buttonPDF btn btn-success" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button><br>';
                   // '<button type="button" onclick="borrarCliente('+ row[0] +');" class="buttonBorrar btn btn-warning"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
        }else{
          return '';
        }
    },
    },
  ],
  paging: true,
  searching: true,
  scrollY: "50vh",
  info: false,
  responsive: false,
  order: [1, "desc"],
 
  
});

$("table.dataTable thead").addClass("table-info")
table.columns( [2] ).visible( false );

}

MostrarClientes();


function borrarCliente(id) {

    Swal.fire({
        title: "Eliminar Cliente",
        html: '<span>¿Estas seguro de eliminar este cliente?</span>',
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
        url: "./modelo/clientes/borrar-cliente.php",
        data: {"id": id},
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
                    table.ajax.reload(null,false);
                }});

                table.ajax.reload(null,false);
           
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

  function editarCliente(id){

    $.ajax({
        type: "POST",
        url: "./modelo/clientes/traer-pa-editar-cliente.php",
        data: {
            "id": id},
        dataType: "JSON",    
        
        success: function (response) {

            $(document).ready(function() { 
               /*  $("#latitud-editar").text(response.latitud);
                $("#longitud-editar").text(response.longitud);
                //console.log(response.longitud)

                //Codigo que genera mapa
                mapboxgl.accessToken = 'pk.eyJ1IjoiYnJheWFubTAzIiwiYSI6ImNrbnRlNTdyZzAxcXcycG84ZnRvNnJtdmoifQ.8k-_U2-Eq-CmSrH6jm8KEg';

                var mymaps = new mapboxgl.Map({
                    container: 'map-edit',
                    style: 'mapbox://styles/mapbox/streets-v11',
                    center: [response.longitud,response.latitud],
                    zoom: 13
                    }); 

                    var nav = new mapboxgl.NavigationControl();
                    
                    

                mymaps.addControl(
                new MapboxGeocoder({
                    accessToken: mapboxgl.accessToken,
                    mapboxgl: mapboxgl,
                })
                );

                mymaps.addControl(nav,"top-left");
                mymaps.addControl(new mapboxgl.FullscreenControl());

                mymaps.addControl(new mapboxgl.GeolocateControl({
                    positionOptions: {
                        enableHighAccuracy: true
                    },
                    trackUserLocation: true
                }));

                var mark = document.createElement("img");
                        mark.id = "marker";
                        mark.src = "./src/img/marker.svg";

                        lngLat = {
                            "lng": response.longitud,
                            "lat": response.latitud
                        }

                    
                        // make a marker for each feature and add it to the map
                        marker = new mapboxgl.Marker({
                            element: mark,
                            draggable: true
                        }).setLngLat(lngLat).addTo(mymaps);


                //Click para establecer un marcador    
                mymaps.on('click', function (e) {

                    $("#marker").remove();  
                        latitud = JSON.stringify(e.lngLat);
                        console.log(latitud);

                        var el = document.createElement("img");
                        el.id = "marker";
                        el.src = "./src/img/marker.svg";

                    
                        // make a marker for each feature and add it to the map
                        marker = new mapboxgl.Marker({
                            element: el,
                            draggable: true
                        }).setLngLat(e.lngLat).addTo(mymaps);

                        $("#latitud-editar").text(e.lngLat.lat);
                        $("#longitud-editar").text(e.lngLat.lng);
                        

                }); */
            
            })


            Swal.fire({
                title: "Editar cliente",
                showCancelButton: true,
                    cancelButtonText: 'Cerrar',
                    cancelButtonColor: '#00e059',
                    showConfirmButton: true,
                    confirmButtonText: 'Actualizar', 
                    cancelButtonColor:'#ff764d',
                    focusConfirm: false,
                    iconColor : "#36b9cc",
                    html: '<form class="mt-4" id="agregar-cliente-form">'+
                
                '<div class="row">'+
        
                   '<div class="col-12">'+
                   '<div class="form-group">'+
                   '<label><b>Nombre:</b></label></br>'+
                   '<input class="form-control" value="'+ response.nombre + '" type="text" id="nombre-cliente" name="nombre" placeholder="Nombre completo">'+
                      '</div>'+
                      '</div>'+
                   '</div>'+
            
                '<div class="row">'+
                    '<div class="col-6">'+
                    '<div class="form-group">'+
                    '<label><b>Credito</b></label>'+
                    '<select class="form-control" value="1" id="credito" name="credito">'+
                    '<option value="0">Sin credito </option>'+
                    '<option value="1">Con credito </option>'+
                    '</select>'+
                  
            
            
               ' </div>'+
                '</div>'+
                
                
               '<div class="col-6">'+
                '<div class="form-group">'+
                '<label for="telefono"><b>Telefono:</b></label></br>'+
                '<input type="number" class="form-control" id="telefono" value="'+ response.telefono +'" name="telefono" placeholder="Telefono" autocomplete="off">'+
                '</div>'+
                '</div>'+
            
                
                    '<div class="col-7">'+
                    '<div class="form-group">'+
                     
                    '<label><b>Correo:</b></label></br>'+
                      '<input type="text" name="correo" value="'+ response.correo +'" id="correo" class="form-control" placeholder="Correo">'+
                '</div>'+
                    '</div>'+
            
                   
            
                    '<div class="col-5">'+
                    '<div class="form-group">'+
                    '<label><b>RFC</b></label>'+
                    '<input type="text" class="form-control" value="'+ response.rfc +'" id="rfc" name="rfc" placeholder="RFC">'+
                    '</div>'+
                    '</div>'+

                    '<div class="col-12">'+
                        '<div class="form-group">'+
                        '<label><b>Asesor</b></label>'+
                        '<select class="form-control" id="asesor" name="asesor">'+
                        '</select>'+
                        '</div>'+
                    '</div>'+

                    '<div class="col-6 mt-3">'+
                        '<div class="form-group">'+
                        '<label><b>Tipo de cliente</b></label>'+
                        '<select class="form-control" id="tipo-cliente" name="tipo-cliente">'+
                            '<option value="">Seleccione un tipo de cliente</option>'+
                            '<option value="1">Mayorista</option>'+
                        '</select>'+
                        '</div>'+
                    '</div>'+
            
                   
                '<div class="col-12">'+
                    '<div class="form-group">'+
                        '<label><b>Dirección</b></label>'+
                        '<textarea type="text" class="form-control" name="direccion" id="direccion" placeholder="Escribe la dirección del cliente">'+ response.direccion +
                        '</textarea>'+
                    '</div>'+
                '</div>'+
               /*  '<div class="col-12">'+
                    '<div class="form-group">'+
                        '<label><b>Mapear dirección</b></label>'+
                        '<div id="map-edit"></div>'+
                        '<div class="alert alert-info coordenadas-agregar" id="label-coord"><strong>Cordenadas del marcador:</strong>'+
                        ' </br>Latitud: <span id="latitud-editar"></span> </br>longitud: <span id="longitud-editar"></span></div>'+
                       // "<img id='marker' class='marker' src='./src/img/marker.svg' alt='insertar SVG con la etiqueta image'>"+
                        
                    '</div>'+
                '</div>'+ */
            
                '</div>'+
                    '<div>'+
            '</form>',
            didOpen: function(){
                $("#asesor").empty().append("<option value='nulo'>Selecciona una vendedor</option>");
        
                    $.ajax({
                        type: "POST",
                        url: "./modelo/busqueda/traer-usuarios.php",
                        data: "data",
                        dataType: "JSON",
                        success: function (respuesta) {
                            respuesta.forEach(element => {
                               
                            $("#asesor").append(`
                            <option value="${element.id}">${element.nombre}</option>
                            `); 
                            });


                      id_ases = parseInt(response.asesor);
                      document.getElementById("asesor").value = id_ases;
                        }
                        });
               $("#tipo-cliente").val(response.tipo_cliente)         

            },
            preConfirm: function(){
                let asesor =  $("#asesor").val();
                let nombre_cliente = $("#nombre-cliente").val();
                if(asesor == '' || asesor == 'nulo'){
                    Swal.showValidationMessage('Selecciona un asesor');
                }
                if(nombre_cliente == '' || nombre_cliente == undefined || nombre_cliente == null){
                    Swal.showValidationMessage('Escribe el nombre del cliente');
                }
            }
        
            }).then((result) =>{
                //Agregando cliente
                if(result.isConfirmed){
        
                    nombre = $("#nombre-cliente").val();
                    credito = $("#credito").val();
                    telefono = $("#telefono").val();
                    correo = $("#correo").val()
                    rfc = $("#rfc").val();
                    direccion = $("#direccion").val();
                    latitud = $("#latitud-editar").text();
                    longitud = $("#longitud-editar").text();
                    asesor = $("#asesor").val();
                    let tipo_cliente = $("#tipo-cliente").val();
        
                    
                    $.ajax({
                        type: "POST",
                        url: "./modelo/clientes/actualizar-cliente.php",
                        data: {
                            "id": id,
                            "nombre": nombre,
                            "credito": credito,
                            "telefono": telefono,
                            "correo": correo,
                            "rfc": rfc,
                            "direccion": direccion,
                            "latitud": latitud,
                            "longitud": longitud,
                            "asesor": asesor,
                        tipo_cliente},
                        
                        success: function (response) {
                           if (response == 1) {
                            Swal.fire(
                                "¡Actualizado!",
                                "Se actualizó el cliente correctamente",
                                "success"
                                ).then((result) => { 
                                    if(result.isConfirmed){
                                        table.ajax.reload(null,false);
                                    }
                                    table.ajax.reload(null,false);
                                    });
                               
                           }else if(response == 0){
                            Swal.fire(
                                "¡Ups!",
                                "No se pudo actualizar el cliente",
                                "error"
                                )
                           }
                        }
                    });
        
                }
            });

            

            if (response.credito == 0) {
                $('#credito option:eq(0)').prop('selected', true);
            }else if(response.credito == 1){
                $('#credito option:eq(1)').prop('selected', true)
            }           
                   
        }

        
    });
  
  }


   
   
 

 