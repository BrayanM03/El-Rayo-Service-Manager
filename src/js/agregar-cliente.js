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
            '<select class="form-control" id="credito" name="credito" disabled>'+
            '<option value="0" selected>Sin credito </option>'+
            '<option value="1">Con credito </option>'+
            '</select>'+
          
    
    
       ' </div>'+
        '</div>'+
        
        
       '<div class="col-6">'+
        '<div class="form-group">'+
        '<label for="telefono"><b>Telefono:</b></label></br>'+
        '<input type="text" class="form-control" id="telefono" name="telefono" placeholder="Telefono" autocomplete="off">'+
        '</div>'+
        '</div>'+
    
        
            '<div class="col-7">'+
            '<div class="form-group">'+
             
            '<label><b>Correo:</b></label></br>'+
              '<input type="text" name="correo" id="correo" class="form-control" placeholder="Correo">'+
            '</div>'+
            '</div>'+
    
           
    
            '<div class="col-5">'+
            '<div class="form-group">'+
            '<label><b>RFC</b></label>'+
            '<input type="text" class="form-control" id="rfc" name="rfc" placeholder="RFC">'+
            '</div>'+
            '</div>'+
    
            '<div class="col-12">'+
            '<div class="form-group">'+
            '<label><b>Asesor</b></label>'+
            '<select class="form-control" id="asesor" name="asesor">'+
            '</select>'+
            '</div>'+
            '</div>'+
            
           
        '<div class="col-12">'+
            '<div class="form-group">'+
                '<label><b>Dirección</b></label>'+
                '<textarea type="text" class="form-control" name="direccion" id="direccion" placeholder="Escribe la dirección del cliente">'+
                '</textarea>'+
            '</div>'+
        '</div>'+
        /* '<div class="col-12">'+
            '<div class="form-group">'+
                '<label><b>Mapear dirección</b></label>'+
                '<div id="map-id"></div>'+
                '<div class="alert alert-info coordenadas-agregar" id="label-coord"><strong>Cordenadas del marcador:</strong>'+
                ' </br>Latitud: <span id="lat-agregar"></span> </br>longitud: <span id="long-agregar"></span></div>'+
               // "<img id='marker' class='marker' src='./src/img/marker.svg' alt='insertar SVG con la etiqueta image'>"+
                
            '</div>'+
        '</div>'+ */
    
        '</div>'+
    


            '<div>'+
    '</form>',
    preConfirm: (respuesta) => {
        /* token_validar = $("#token-validar").val();
        if (token_validar == "") {
          Swal.showValidationMessage(`El valor no puede ir vacio`);
        } */
      },
    didOpen: function(){
        $("#asesor").empty().append("<option value='nulo'>Selecciona una vendedor</option>");

            $.ajax({
                type: "POST",
                url: "./modelo/busqueda/traer-usuarios.php",
                data: "data",
                dataType: "JSON",
                success: function (response) {
                    response.forEach(element => {
                       
                    $("#asesor").append(`
                    <option value="${element.id}">${element.nombre}</option>
                    `); 
                    });
                }
                });
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
            latitud = $("#lat-agregar").text();
            longitud = $("#long-agregar").text();
            asesor = $("#asesor").val();
            
            $.ajax({
                type: "POST",
                url: "./modelo/clientes/agregar-cliente.php",
                dataType:'JSON',
                data: {
                    "intento": 1,
                    "nombre": nombre,
                    "credito": credito,
                    "telefono": telefono,
                    "correo": correo,
                    "rfc": rfc,
                    "direccion": direccion,
                    "latitud": latitud,
                    "longitud": longitud,
                    "asesor": asesor},
                
                success: function (response) {
                   if (response.status) {
                    Swal.fire(
                        "¡Registrado!",
                        response.msj,
                        "success"
                        ).then((result) => { 
                            if(result.isConfirmed){
                                table.ajax.reload(null,false);
                            }
                            table.ajax.reload(null,false);
                            });
                   }else if(response.status == false){
                    Swal.fire({
                            icon: 'warning',
                            didOpen: () => {

                                if(Array.isArray(response.data)){
                                    let contenedor = $("#contenedor-clientes-encontrados");
                                    contenedor.empty();
                                    contenedor.append(`<table class="table table-striped table-bordered" id="tabla-clientes-encontrados">
                                    <thead class="table-info">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-clientes-encontrados" is-valid="false" mensaje_error="Escribe un token para registrar cliente repetido">
                                    </tbody>
                                    </table>`);
                                    response.data.forEach(element => {
                                        $("#tbody-clientes-encontrados").append(`
                                        <tr>
                                            <td>R${element.id}</td>
                                            <td>${element.Nombre_Cliente}</td>
                                        </tr>
                                        `);
                                    });

                                  controlCodeInputs('apartado','.form-control_code_apartado','#mensaje-error-token-apartado')

                            }
                            },
                            html: `
                            <div class=container"">
                            <div class="row">
                                <div class="col-12">
                                <h5>¡Ups!</h5>
                                <p>${response.msg}</p>
                                </div>
                                <div class="col-12" id="contenedor-clientes-encontrados">
                                </div>
                                <div class="row m-auto justify-content-center">
                                <div class="col-12 mt-3">
                                    <label>Token:</label> 
                                </div>
                                <div class="col-12 text-center mb-3">
                                    <input id="token-apartado-1" autocomplete="off" class="form-control_code_apartado" placeholder="0"></input>
                                    <input id="token-apartado-2" autocomplete="off" class="form-control_code_apartado" placeholder="0"></input>
                                    <input id="token-apartado-3" autocomplete="off" class="form-control_code_apartado" placeholder="0"></input>
                                    <input id="token-apartado-4" autocomplete="off" class="form-control_code_apartado" placeholder="0"></input>
                                </div>
                                <div class="col-12" id="mensaje-error-token-apartado">
                                  
                                </div>
                                </div>
                            </div>
                            </div>`,
                            preConfirm: ()=>{
                                    let sumatoria_forma_pago_valida = $("#tbody-clientes-encontrados").attr("is-valid");
                                    let validacion_mensaje_error = $("#tbody-clientes-encontrados").attr("mensaje_error");
                                 
                                    if(sumatoria_forma_pago_valida=='false'){
                                      return Swal.showValidationMessage(`
                                        ${validacion_mensaje_error}
                                    `);
                                    }
                            },
                            confirmButtonText: 'Registrar aun asi',
                            showCancelButton: true,
                            cancelButtonText: 'Cancelar',
                        }).then(function(re){
                            if(re.isConfirmed){
                                $.ajax({
                                    type: "POST",
                                    url: "./modelo/clientes/agregar-cliente.php",
                                    dataType:'JSON',
                                    data: {
                                        "intento": 2,
                                        "nombre": nombre,
                                        "credito": credito,
                                        "telefono": telefono,
                                        "correo": correo,
                                        "rfc": rfc,
                                        "direccion": direccion,
                                        "latitud": latitud,
                                        "longitud": longitud,
                                        "asesor": asesor},
                                    success: function (respons) {
                                        Swal.fire(
                                            "¡Registrado!",
                                            respons.msj,
                                            "success"
                                            ).then((result) => { 
                                                if(result.isConfirmed){
                                                    table.ajax.reload(null,false);
                                                }
                                                table.ajax.reload(null,false);
                                                });
                                    }    
                                    })
                            }
                        });
                   }
                }
            });

        }
    });

  };

 








  //Codigo que genera mapa
/* mapboxgl.accessToken = 'pk.eyJ1IjoiYnJheWFubTAzIiwiYSI6ImNrbnRlNTdyZzAxcXcycG84ZnRvNnJtdmoifQ.8k-_U2-Eq-CmSrH6jm8KEg';

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

mymap.on('click', function (e) {

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
        }).setLngLat(e.lngLat).addTo(mymap);
          

          $("#lat-agregar").text(e.lngLat.lat);
          $("#long-agregar").text(e.lngLat.lng);
          

}); */