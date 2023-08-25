toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": false,
  "progressBar": true,
  "positionClass": "toast-bottom-right",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}


    $("#ubicacion").on("change", function (e) {

         if($(this).val() == 0){
            $("#buscador").empty();
            $('#buscador').prop("disabled", true);
            $("#btn-mover").attr("id_item", "");
            $("#stock").removeClass("is-invalid").prop("disabled", true).val("");
            validador();
            console.log(validador());
        }else{

            $("#buscador").val('').trigger('change');
            $("#stock").removeClass().addClass("form-control").val(0).prop("disabled", true);
            validador();
            $('#buscador').prop("disabled", false);
            ide_sucursal = $(this).val();

        } 

        ubi = $(this).val()
        traerSucEspecficia(ubi, "destino");

    });

    function traerSucEspecficia(ubi, inputx){
     
      //Trabajando con las lista_splides
      $.ajax({
        type: "POST",
        url: "./modelo/cambios/traer-sucursales-especificas.php",
        data: {"ubi": ubi},
        dataType: "JSON",
        success: function (response) {
        
          //console.log($("#"+inputx).val());
          if($("#"+inputx).val() == 0){
            $("#"+inputx).empty().append("<option value='0'>Selecciona una sucursal</option>");

            response.forEach(element => {
              $("#"+inputx).append(`
                <option value="${element.id}">${element.nombre}</option>
              `);
            });
          }else{
           console.log("DEBERIA MANTENERSE");

           if(ubi == $("#"+inputx).val()){
            $("#"+inputx).empty() .append("<option value='0'>Selecciona una sucursal</option>");
            response.forEach(element => {
              $("#"+inputx).append(`
                <option value="${element.id}">${element.nombre}</option>
              `);
            });
           } 
          }
         
        }
      });
    }

    function validador() {
      if($("#ubicacion").val() == 0){
        $('#btn-mover').removeClass();
        $('#btn-mover').addClass("btn btn-primary disabled");
        
      }else if($("#stock").attr("valido") == "false" || $("#stock").attr("valido")==""){
     
        $('#btn-mover').removeClass();
        $('#btn-mover').addClass("btn btn-primary disabled");
      
      }else if($("#destino").val() == 0){
        $('#btn-mover').removeClass();
        $('#btn-mover').addClass("btn btn-primary disabled");
      
      }else if($("#btn-mover").attr("id_item") == 0 || $("#btn-mover").attr("id_item") == null){
        $('#btn-mover').removeClass();
        $('#btn-mover').addClass("btn btn-primary disabled");
        
      }else{
        $('#btn-mover').removeClass();
        $('#btn-mover').addClass("btn btn-primary");
       
      }
      }


    $("#destino").on("change", function (e) {
      
      if($(this).val() ==0){

        validador();

      }else{
        if($("#destino").val() == $("#ubicacion")){
          $("#buscador").val('').trigger('change');
          $("#stock").removeClass().addClass("form-control").val(0).prop("disabled", true);
          comprobarStock();
        }else{
          if($("#btn-mover").attr("id_item") !== ""){
          comprobarStock();
        }
      }
        validador();
      }

 
      ubi = $(this).val()
      traerSucEspecficia(ubi, "ubicacion");
    });


    $('#buscador').select2({
        placeholder: "Selecciona una llanta",
        theme: "bootstrap",
        minimumInputLength: 1,
        ajax: {
            url: "./modelo/cambios/buscar-llanta-inventario.php",
            type: "post",
            dataType: 'json',
            delay: 250,

            data: function (params) {
              if(params.term == undefined){
                params.term = "";
              }
             return {
               searchTerm: params.term, // search term
               id_sucursal: ide_sucursal,
               page: params.page || 1,
             };
            }
        },
        processResults: function (data) {
          return {
             results: data
          };
        },
     
      cache: true,
        language:  {

            inputTooShort: function () {
                return "Busca la llanta...";
              },
              
            noResults: function() {
        
              return "Sin resultados";        
            },
            searching: function() {
        
              return "Buscando..";
            }
          },

          templateResult: formatRepo,
          templateSelection: formatRepoSelection
    }).maximizeSelect2Height();


    function formatRepo (repo) {
        
      if (repo.loading) {
        return repo.text;
      }
      
        var $container = $(
            "<div class='select2-result-repository clearfix' desc='"+repo.Descripcion+" marca='"+repo.Marca +
            " id='"+repo.Marca+" costo='"+repo.precio_Inicial +" id='tyre' precio='"+repo.precio_Venta+" idcode='"+repo.id+"'>" +
            "<div class='select2-contenedor-principal row' syle='display:flex;'>" +
            "<div class='col-md-2 justify-content-center'><img class='' style='width: 50px; border-radius: 6px;' src='./src/img/logos/" + repo.Marca + ".jpg' /></div>" +
              "<div class='col-md-10 select2-contenedor'>" +
              "<div class='select2_modelo'>Modelo "+ repo.Modelo +"</div>" +
              "<div class='select2_description'>" + repo.Descripcion + "</div>" +
              "<div class='select2_description'><i class='fas fa-fw fa-store'></i> " + repo.Sucursal + "</br> <b>Stock:</b> "+repo.Stock+"</div>" +
              "</div>" +
              "</div>" +
              "<div class='select2_statistics' style='display:flex; border-top: 1px solid whitesmoke; padding-top:8px; justify-content:space-around; margin-top:5px;'>" +
              "<div class='select2_marca'><i class='fa fa-star'></i> "+ repo.Marca+"</div>" +
                "<div class='select2_costo'><i class='fa fa-dollar-sign'></i> "+repo.precio_Inicial+" (Costo) </div>" +
                "<div class='select2_precio_venta'><i class='fa fa-tag'></i> "+ repo.precio_Venta +" (precio)</div>" + 
              "</div>" +
            "</div>" +
          "</div>"
        );
      
      /*  $container.find(".select2_modelo").text(repo.modelo);
        $container.find(".select2_description").text(repo.descripcion);
        $container.find(".select2_marca").append(repo.marca);
        $container.find(".select2_precio_venta").append(repo.precio);
        $container.find(".select2_costo").append(repo.costo);*/ 
        //

        return $container;
        
      }

      function formatRepoSelection (repo) {
        //A partir de aqui puedes agregar las llantas Brayan
       // ruta = "./src/img/logos/" + repo.marca + ".jpg";

       console.log(repo.Stock);
        $("#stock_actual").val(repo.Stock);
        $("#btn-mover").attr("id_item", repo.id);
        $("#btn-mover").attr("id_llanta", repo.id_Llanta);
        if(repo.id !== ""){

          $("#stock").prop("disabled", false);
        }
        validador();
      /*$("#btn-agregar").attr("descripcion", repo.descripcion);
        $("#btn-agregar").attr("modelo", repo.modelo);
        $("#btn-agregar").attr("marca", repo.marca);
        $("#btn-agregar").attr("costo", repo.costo);
        $("#btn-agregar").attr("precio", repo.precio);
        $("#precio").val(repo.precio); */
       /* $("#ancho-agregado").text(repo.ancho);
        $("#alto-agregado").text(repo.alto);
        $("#rin-agregado").text(repo.rin);
        $("#modelo-agregado").text(repo.modelo);
        $("#marca-agregado").text(repo.marca);
        $(".logo-marca-agregada").attr("src", ruta);

        $("#costo-agregado").text(repo.costo);
        $("#precio-agregado").text(repo.precio);
        $("#mayoreo-agregado").text(repo.mayoreo);*/
        //$("#mayoreo-agregado").fadeIn(400)
       

        return repo.text || repo.Descripcion;
      }

      function countItems(listID){
        var ul = document.getElementById(listID);
        if(ul == null){
          return 0;
        }else{

          var i=0, itemCount =0;
          while(ul.getElementsByTagName('li') [i++]) itemCount++;
          return itemCount;
          }
        }
     

function agregarLlantas(){

  hasDisabled = $("#btn-mover").hasClass("disabled");
console.log(hasDisabled);
  if(hasDisabled == false){

    let sucursal_remitente = $("#ubicacion").val();
    let sucursal_destino = $("#destino").val();
    let cantidad = $("#stock").val();
    let id_llanta = $("#btn-mover").attr("id_llanta");
    let id_usuario = $("#btn-mover").attr("id_usuario");


    $.ajax({
      type: "POST",
      url: "./modelo/cambios/agregar-detalle-cambio.php",
      data: {"sucursal_remitente": sucursal_remitente,
             "cantidad": cantidad, "sucursal_destino": sucursal_destino, "id_llanta": id_llanta, "id_usuario": id_usuario},
      dataType: "JSON",
      success: function (response) {
       
         estatus = response.estatus;
         switch (estatus) {
           case "success":
            toastr.success(response.mensaje, 'Respuesta' );
           break;
           case "error":
            toastr.error(response.mensaje, 'Respuesta' );
           break;
           case "warning":
            toastr.warning(response.mensaje, 'Respuesta' );
           break;
           case "info":
            toastr.info(response.mensaje, 'Respuesta' );
           break;
         } 
         traerDetalleCambio();
    
      }
      });
  }else{
    toastr.error("Completa el formulario o revisa los campos en rojo", 'Respuesta' );
  }

}   


function comprobarStock(){
  let stock = $("#stock").val();
  let id_sucursal = $("#ubicacion").val();
  let id_sucursal_destino = $("#destino").val();
  let id_llanta = $("#btn-mover").attr("id_item");
  let id_usuario = $("#btn-mover").attr("id_usuario");
  let code_llanta = $("#btn-mover").attr("id_llanta");
  
  stock = parseInt(stock);
  console.log(stock);
  esNaN = Number.isNaN(stock)
  if(esNaN == true || stock == 0){

    $("#stock").removeClass();
    $("#stock").addClass("form-control is-invalid");
    $("#label-validator").empty().text("Escribe una cantidad. 😅");
    $("#stock").attr("valido", "false");
    $('#btn-mover').removeClass();
    $('#btn-mover').addClass("btn btn-primary disabled");

  }else{
    
      $.ajax({
        type: "POST",
        url: "./modelo/cambios/comprobar-stock.php",
        data: {"stock": stock, "id_sucursal": id_sucursal,
               "id_sucursal_destino": id_sucursal_destino, 
               "id_llanta": id_llanta, "id_usuario": id_usuario, "code_llanta": code_llanta},
       // dataType: "JSON",
        success: function (response) {
          
          if(response == 2) {
            $("#stock").removeClass();
            $("#stock").addClass("form-control is-invalid");
            $("#label-validator").empty().text("Esa cantidad no es aceptable.");
            $("#stock").attr("valido", "false");
            $('#btn-mover').removeClass();
            $('#btn-mover').addClass("btn btn-primary disabled");
            validador();
          }else if(response ==0){
            $("#stock").removeClass();
            $("#stock").addClass("form-control is-invalid");
            $("#label-validator").empty().text("La cantidad sobrepasa tu stock.");
            $("#stock").attr("valido", "false");
            $('#btn-mover').removeClass();
            $('#btn-mover').addClass("btn btn-primary disabled");
            validador();
          }else if(response == 1){
            $("#stock").removeClass();
            $("#stock").addClass("form-control is-valid");
            $("#label-validator").empty().text("Perfecto.");
            $("#stock").attr("valido", "true");
            $('#btn-mover').removeClass();
            $('#btn-mover').addClass("btn btn-primary");
            validador();
          }else{
            validador();
          }
        }
      });

  }
  
}

function traerDetalleCambio(){
  let id_usuario = $("#btn-mover").attr("id_usuario");
  $.ajax({
    type: "POST",
    url: "./modelo/cambios/traer-cambios.php",
    data: {"id_usuario": id_usuario},
    dataType: "JSON",
    success: function (response) {
      
      if(response.id == false){
        $("#cuerpo_detalle_cambio").empty().append(`

        <a href="#" class="list-group-item list-group-item-action text-center">
        <div class="row">
             <div class="col-12 col-md-12">Sin datos</div>
        </div>
        </a>
        `);

        $("#btn-mov").removeClass().addClass("btn btn-success disabled");
      }else{
        $contador = 1;

        $("#cuerpo_detalle_cambio").empty();  
        response.forEach(element => {
          
        $("#cuerpo_detalle_cambio").append(`

        <a href="#" class="list-group-item list-group-item-action text-center">
            <div class="row">
              <div class="col-12 col-md-1">${$contador}</div>
              <div class="col-12 col-md-4">${element.descripcion}</div>
              <div class="col-12 col-md-2">${element.sucursal_remitente}</div>
              <div class="col-12 col-md-2">${element.sucursal_destino}</div>
              <div class="col-12 col-md-2">${element.cantidad}</div>
              <div class="col-12 col-md-1"><div class="btn btn-danger" onclick="eliminarLlanta(${element.id})" id="${element.id}"><i class="fas fa-trash"></i></div></div>    
            </div>
        </a>
        `);
        });
        $("#btn-mov").removeClass().addClass("btn btn-success");
      }

    }
  });
}

  
function todas(){
  agregarLlantas();
  comprobarStock();
}

function eliminarLlanta(id){

  let id_cambio = id;
$.ajax({
  type: "POST",
  url: "./modelo/cambios/eliminar-llanta.php",
  data: {"id_cambio": id_cambio},
  dataType: "JSON",
  success: function (response) {
    estatus = response.estatus;
    switch (estatus) {
      case "success":
       toastr.success(response.mensaje, 'Respuesta' );
      break;
      case "error":
       toastr.error(response.mensaje, 'Respuesta' );
      break;
      case "warning":
       toastr.warning(response.mensaje, 'Respuesta' );
      break;
      case "info":
       toastr.info(response.mensaje, 'Respuesta' );
      break;
    } 
    traerDetalleCambio();
  }
});  

}

depurarTabla();
function depurarTabla() {
  let id_usuario = $("#btn-mover").attr("id_usuario");
$.ajax({
  type: "POST",
  url: "./modelo/cambios/depurar-tabla.php",
  data: {"id_usuario": id_usuario},
  //dataType: "dataType",
  success: function (response) {
    
  traerDetalleCambio();
  }
});
}


//MoverLLantas

function realizarMovimiento(id_user){
  

  let ishasDisabled = $("#btn-mov").hasClass("disabled");

  if(ishasDisabled == false){

    $.ajax({
        type: "POST",
        url: "./modelo/cambios/mover-llantas.php",
        data: {"id_usuario": id_user},
        dataType: "JSON",
        success: function (response) {

          estatus = response.estatus;
          switch (estatus) {
            case "success":
             toastr.success(response.mensaje, 'Respuesta' );
            break;
            case "error":
             toastr.error(response.mensaje, 'Respuesta' );
            break;
            case "warning":
             toastr.warning(response.mensaje, 'Respuesta' );
            break;
            case "info":
             toastr.info(response.mensaje, 'Respuesta' );
            break;
          } 

          if(response){
            Swal.fire({
              icon: 'success',
              html: '<b>Movimiento realizado, se agregarón ' + response + ' item(s)</b>',
            }).then(()=>{
              window.location.reload();

            });


            $("#buscador").val('').trigger('change'); 
            $('#buscador').prop("disabled", true);
            $("#btn-mover").attr("id_item", "");
            $("#btn-mover").attr("id_llanta", "");
            $("#stock_actual").prop("disabled", true).val("");
            $("#stock").removeClass().addClass("form-control").prop("disabled", true).val("");
            $('#ubicacion').prop('selectedIndex',0);
            $('#destino').prop('selectedIndex',0);

          }

          depurarTabla(); 
      }
    });

  }
}