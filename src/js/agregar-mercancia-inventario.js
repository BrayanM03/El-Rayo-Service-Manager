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
  
  
      $("#proveedor").on("change", function (e) { 
  
           if($(this).val() == 0){
              $("#buscador").empty();
              $('#buscador').prop("disabled", true);
              $("#btn-mover").attr("id_item", "");
              $("#stock").removeClass("is-invalid").prop("disabled", true).val("");
              validador();
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
          
        
            if($("#"+inputx).val() == 0){
              $("#"+inputx).empty().append("<option value='0'>Selecciona una sucursal</option>");
  
              response.forEach(element => {
                $("#"+inputx).append(`
                  <option value="${element.id}">${element.nombre}</option>
                `);
              });
            }else{
  
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
        if($("#proveedor").val() == 0){
          $('#btn-mover').removeClass();
          $('#btn-mover').addClass("btn btn-primary disabled");
        }
        }
  
  
      $("#destino").on("change", function (e) {
        
        if($(this).val() ==0){
  
          validador();
  
        }else{
          if($("#destino").val() == $("#ubicacion")){
            $("#buscador").val('').trigger('change');
            $("#stock").removeClass().addClass("form-control").val(0).prop("disabled", true);
            //comprobarStock();
          }else{
            if($("#btn-mover").attr("id_item") !== ""){
           // comprobarStock();
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
            url: "./modelo/cambios/buscar-llanta-existencia.php",
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
            },
            cache: true,

        }, processResults: function (data, params) {
          params.page = params.page || 1;
            return {
               results: data.results,
               pagination: {
                more: (params.page * 10) < data.total_count // Verificar si hay más resultados para cargar
              }
            };
          },
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
    })//.maximizeSelect2Height();
      
      $('#estado-movimientos').on('change', function(e){
        if($(this).val() ==1){
          $('#folio-factura').prop('disabled',true);
          $("#area-adjuntar-archivo").css('display', 'none');
        }else{
          $('#folio-factura').prop('disabled',false);
          $("#area-adjuntar-archivo").css('display', '');

        }
      })
  
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
                "</div>" +
                "</div>" +
                "<span>Cod: "+repo.id+"</span>" +
                "<div class='select2_statistics' style='display:flex; border-top: 1px solid whitesmoke; padding-top:8px; justify-content:space-around; margin-top:5px;'>" +
                "<div class='select2_marca'><i class='fa fa-star'></i> "+ repo.Marca+"</div>" +
                  "<div class='select2_costo'><i class='fa fa-dollar-sign'></i> "+repo.precio_Inicial+" (Costo) </div>" +
                  "<div class='select2_precio_venta'><i class='fa fa-tag'></i> "+ repo.precio_Venta +" (precio)</div>" + 
                "</div>" +
              "</div>" +
            "</div>"
          );
  
          return $container;
          
        }
  
      function formatRepoSelection (repo) {
          
          $("#btn-mover").attr("id_item", repo.id);
          if(repo.id !== ""){
            $("#stock").prop("disabled", false);
            $("#importe").prop("disabled", false);
            $("#tasa").prop("disabled", false);
            $("#importe_total").prop("disabled", false);
          }
          $("#btn-mover").removeClass('disabled');

          validador();
          traerStockSucursales(repo.id);
  
          $("#costo-actual").val(repo.precio_Inicial);
          $("#precio-actual").val(repo.precio_Venta);
          $("#mayoreo-actual").val(repo.precio_Mayoreo);
          $("#costo-actual").attr('costo',repo.precio_Inicial);
          $("#precio-actual").attr('precio',repo.precio_Venta);
          $("#mayoreo-actual").attr('mayoreo',repo.precio_Mayoreo);
  
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
    
    if(hasDisabled == false){
      
      let costo_actual = $("#costo-actual").attr('costo');
      let precio_actual = $("#precio-actual").attr('precio');
      let mayoreo_actual = $("#mayoreo-actual").attr('mayoreo');
      let costo_= $("#costo-actual").val();
      let precio_= $("#precio-actual").val();
      let mayoreo_= $("#mayoreo-actual").val();
      let id_llanta =  $("#btn-mover").attr("id_item")
      if(costo_actual != costo_ || precio_actual != precio_ ||  mayoreo_actual != mayoreo_) {
        Swal.fire({
          icon: 'question',
          html: `¿Quieres actualizar los precios de esta llanta en el catalogo?`,
          showCancelButton: true,
          confirmButtonText: 'Actualizar',
          cancelButtonText: 'No',
        }).then((r)=>{
          if(r.isConfirmed) {
            $.ajax({
              type: "post",
              url: "./modelo/inventarios/actualizar-precios.php",
              data: {'costo':costo_, 'precio': precio_, 'mayoreo': mayoreo_, 'id_llanta': id_llanta},
              dataType: "JSON",
              success: function (response) {
                if(response.estatus){
                   $("#costo-actual").attr('costo', response.costo);
                   $("#precio-actual").attr('precio', response.precio);
                   $("#mayoreo-actual").attr('mayoreo', response.mayoreo);
                  agregarLlantasTablaPremovimiento(response.mensaje)
                }}
            });
          }else{
           $("#costo-actual").val(costo_actual);
           $("#precio-actual").val(precio_actual);
           $("#mayoreo-actual").val(mayoreo_actual);
           agregarLlantasTablaPremovimiento()
          }
        })
      }else{
        agregarLlantasTablaPremovimiento()
      }
     
    }else{
      toastr.error("Completa el formulario o revisa los campos en rojo", 'Respuesta' );
    }
  
  }   
  
  function agregarLlantasTablaPremovimiento(mensaje=''){
    let sucursal_remitente = $("#btn-mover").attr('id_sucursal');
    let sucursal_destino = $("#btn-mover").attr('id_sucursal');
    let cantidad = $("#stock").val();
    let id_llanta = $("#btn-mover").attr("id_item");
    let id_usuario = $("#btn-mover").attr("id_usuario");
    let costo_= $("#costo-actual").val();
    if(cantidad > 0){
      $.ajax({
        type: "POST",
        url: "./modelo/inventarios/agregar-mercancia-detalle.php",
        data: {"sucursal_remitente": sucursal_remitente,
               "cantidad": cantidad, "sucursal_destino": sucursal_destino, "id_llanta": id_llanta, "id_usuario": id_usuario, 'costo': costo_},
        dataType: "JSON",
        success: function (response) {
         
           estatus = response.estatus;
           switch (estatus) {
             case "success":
              toastr.success(response.mensaje + ' ' + mensaje, 'Respuesta' );
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

      toastr.error("La cantidad no puede ser igual o menor que 0", 'Error' );
    }
  }
  
  /*function comprobarStock(){
    let stock = $("#stock").val();
    let id_sucursal = $("#ubicacion").val();
    let id_sucursal_destino = $("#destino").val();
    let id_llanta = $("#btn-mover").attr("id_item");
    let id_usuario = $("#btn-mover").attr("id_usuario");
    let code_llanta = $("#btn-mover").attr("id_llanta");
    
    stock = parseInt(stock);
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
    
  }*/
  
  function traerDetalleCambio(){
    var cantidad_piezas=0;
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
          $('#sumatoria-mercancia').empty().text('$0.00')
          $('#cantidad_piezas').val(0);
        }else{
          $contador = 1;
          let importe_total = 0;
          $("#cuerpo_detalle_cambio").empty();  
          response.forEach(element => {
          cantidad_piezas += parseInt(element.cantidad)
          $("#cuerpo_detalle_cambio").append(`
  
          <a href="#" class="list-group-item list-group-item-action">
              <div class="row">
                <div class="col-12 col-md-1">${$contador}</div>
                <div class="col-12 col-md-3">${element.descripcion}</div>
                <div class="col-12 col-md-1">${element.costo}</div>
                <div class="col-12 col-md-2">${element.sucursal_remitente}</div>
                <div class="col-12 col-md-2">${element.sucursal_destino}</div>
                <div class="col-12 col-md-1">${element.cantidad}</div>
                <div class="col-12 col-md-1">${element.importe_ft}</div>
                <div class="col-12 col-md-1"><div class="btn btn-danger" onclick="eliminarLlanta(${element.id})" id="${element.id}"><i class="fas fa-trash"></i></div></div>    
              </div>
          </a>
          `);
          importe_total += parseFloat(element.importe);
          importe_total_ft = new Intl.NumberFormat('en-IN').format(importe_total)
          });
          $("#btn-mov").removeClass().addClass("btn btn-success");
          $("#cantidad_piezas").val(cantidad_piezas);
          $('#sumatoria-mercancia').empty().text('$'+(importe_total_ft))
        }
  
      }
    });
  }
  
    
  function todas(){
    agregarLlantas();
    //comprobarStock();
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
  
  function realizarIngreso(id_user){
    
  
    let ishasDisabled = $("#btn-mov").hasClass("disabled");
  
    if(ishasDisabled == false){

      let folio_factura = $('#folio-factura').val();
      let proveedor = $('#proveedor').val();
      let id_sucursal = $('#btn-mover').attr('id_sucursal');
      let estado_movimiento = $('#estado-movimientos').val();
      if(estado_movimiento == null ||  estado_movimiento == '' || estado_movimiento == undefined){
        toastr.error('Selecciona un tipo de factura', 'Respuesta' );
          return false;
      }
      if(estado_movimiento!=1){
        if(folio_factura.trim() == ''){
          toastr.error('Ingresa una factura', 'Respuesta' );
          return false;
        }
      }
      
      if(proveedor == 0){
        toastr.error('Selecciona un proveedor', 'Respuesta' );
        return false;
      }


      //Forma data
      let documento_adjunto = document.getElementById('factura-documento');
      var file =  documento_adjunto.files[0];
      var formData = new FormData();
      formData.append('id_usuario', id_user);
      formData.append('documento_adjunto', file);
      formData.append('id_proveedor', proveedor);
      formData.append('folio_factura', folio_factura);
      formData.append('id_sucursal', id_sucursal);
      formData.append('estado_movimiento', estado_movimiento);
      $.ajax({
          type: "POST",
          url: "./modelo/inventarios/ingresar-mercancia.php",
          data: formData,
          contentType: false,
          processData: false,
          dataType: "JSON",
          success: function (response) {
  
            estatus = response.estatus;
            switch (estatus) {
              case true:
               toastr.success(response.mensaje, 'Respuesta' );
              break;
              case false:
               toastr.error(response.mensaje, 'Respuesta' );
              break;
              case "warning":
               toastr.warning(response.mensaje, 'Respuesta' );
              break;
              case "info":
               toastr.info(response.mensaje, 'Respuesta' );
              break;
            } 
  
            if(response.estatus == true){
              Swal.fire({
                icon: 'success',
                confirmButtonText: 'Entendido',
                denyButtonText: 'Reporte de ingreso',
                showDenyButton: true,
                showCancelButton: false,
                html: `<b>${response.mensaje}</b>
                `,
              }).then((r)=>{
                  if(r.isDenied){
                    window.open('./modelo/movimientos/remision-ingreso.php?id=' + response.id_entrada , '_blank');
                  }
              });
  
  
              $("#buscador").val('').trigger('change'); 
              //$('#buscador').prop("disabled", true);
              $("#btn-mover").attr("id_item", "");
              $("#btn-mover").attr("id_llanta", "");
              //$("#stock_actual").prop("disabled", true).val("");
              $("#stock").removeClass().addClass("form-control").prop("disabled", true).val("");
              $('#ubicacion').prop('selectedIndex',0);
              $('#destino').prop('selectedIndex',0);
              depurarTabla();
            }
  
            
        }
      });
  
    }
  }


  //Funcion para agregar llanta al catalogo en caso de no existir
  function agregarLLanta() {

    Swal.fire({
      title: "Agregar llanta nueva",
      html: '<form class="mt-4" id="agregar-llanta-inv-total">'+
  
      '<div class="row">'+
      
         '<div class="col-12">'+
         '<div class="form-group">'+
         '<label><b>Marca:</b></label></br>'+
         '<select class="form-control" id="marca" name="marca"></select>'+
            '</div>'+
            '</div>'+
         '</div>'+
  
      '<div class="row">'+
          '<div class="col-4">'+
          '<div class="form-group">'+
          '<label for="ancho"><b>Ancho:</b></label></br>'+
          '<input type="number" class="form-control" id="ancho"  name="ancho" placeholder="Ancho" autocomplete="off" step="0.1">'+
  
  
     ' </div>'+
      '</div>'+
      
      
     '<div class="col-4">'+
      '<div class="form-group">'+
      '<label><b>Alto:</b></label></br>'+
      '<input type="number" name="alto" id="alto" class="form-control" placeholder="Proporcion" step="0.1">'+
      '</div>'+
      '</div>'+
  
      
          '<div class="col-4">'+
          '<div class="form-group">'+
          '<label><b>Rin</b></label>'+
          '<input type="number" class="form-control"  id="rin" name="rin" placeholder="Diametro" step="0.1">'+
      '</div>'+
          '</div>'+
  
         
  
          '<div class="col-8 ">'+
          '<div class="form-group">'+
          '<label><b>Modelo</b></label>'+
          '<input type="text" class="form-control" id="modelo" name="modelo" placeholder="Modelo">'+
          '</div>'+
          '</div>'+
  
         
      /*'<div class="col-6">'+
          '<div class="form-group">'+
              '<label><b>Fecha</b></label>'+
              '<input type="date" class="form-control" value="" name="fecha" id="fecha" >'+
          '</div>'+
      '</div>'+*/
      
      
     
         
  
  
      '</div>'+
  
      '<div class="row">'+
          '<div class="col-4">'+
              '<div class="form-group">'+
                  '<label><b>Costo</b></label>'+
                  '<input type="number" class="form-control" id="costo" value=""name="costo" placeholder="0.00">'+
              '</div>'+
          '</div>'+
          '<div class="col-4">'+
          '<div class="form-group">'+
          '<label><b>Precio</b></label>'+
          '<input type="number" class="form-control" value="" name="precio" id="precio" placeholder="0.00">'+
      '</div>'+
  '</div>'+
  '<div class="col-4">'+
          '<div class="form-group">'+
          '<label><b>Mayorista</b></label>'+
          '<input type="number" class="form-control" value="" name="mayorista" id="mayorista" placeholder="0.00">'+
      '</div>'+
  '</div>'+
          '</div>'+
      '</div>'+
  
      '<div class="row  mt-1">'+
      '<div class="col-12">'+
      '<div class="form-group" id="area-solucion">'+
      '<label><b>Descripción</b></label>'+
      '<textarea class="form-control" style="height:100px" name="descripcion" id="descripcion" form="formulario-editar-registro" placeholder="Escriba la descripcion del producto"></textarea>'+
      '</div>'+
      '</div>'+
      '</div>'+
              '</div>'+
  '</form>',
      showCancelButton: true,
      cancelButtonText: 'Cerrar',
      cancelButtonColor: '#00e059',
      showConfirmButton: true,
      confirmButtonText: 'Actualizar', 
      cancelButtonColor:'#ff764d',
      focusConfirm: false,
      iconColor : "#36b9cc",
      didOpen: function () {
       
          $(document).ready(function() { 
              
  
              $('#marca').select2({
                  placeholder: "Selecciona una marca",
                  theme: "bootstrap",
                  minimumInputLength: 1,
                  ajax: {
                      url: "./modelo/traer-marca.php",
                      type: "post",
                      dataType: 'json',
                      delay: 250,
  
                      data: function (params) {
                       return {
                         searchTerm: params.term // search term
                         
                       };
                      },
                      processResults: function (data) {
                          return {
                             results: data
                          };
                        },
                     
                      cache: true
  
                  },
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
              });
  
  
              function formatRepo (repo) {
                  
                if (repo.loading) {
                  return repo.text;
                }
                
                  var $container = $(
                      "<div class='select2-result-repository clearfix'>" +
                      "<div class='select2-contenedor-principal'>" +
                      "<div class='select2-result-repository__avatar'><img style='width: 50px; border-radius: 6px' src='./src/img/logos/" + repo.imagen + ".jpg' /></div>" +
                        "<div class='select2-contenedor'>" +
                        "<div class='select2_marca' marca='"+ repo.imagen +"'></div>" +
                        "</div>" +
                        "</div>" +
                        "</div>" 
                  );
                
                  $container.find(".select2_marca").text(repo.nombre);
  
                  
                
                  return $container;
                }
  
               
  
                function formatRepoSelection (repo) {
                  return repo.imagen || repo.text;
                }
  
  
          });
      } ,
      showLoaderOnConfirm: true,
      preConfirm: (respuesta) =>{
  
        data = {
          "marca":          $("#select2-marca-container").text(),  
          "ancho":          $("#ancho").val(),
          "alto":           $("#alto").val(),
          "rin":            $("#rin").val(),
          "costo":          $("#costo").val(),
          "precio":         $("#precio").val(),
          "mayorista":      $("#mayorista").val(),
          "modelo":         $("#modelo").val(),
          "descripcion":    $("#descripcion").val()
        };
  
        if(data["marca"] == "Selecciona una marca"){
          /*const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
              toast.addEventListener('mouseenter', Swal.stopTimer)
              toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
          })
          
          Toast.fire({
            icon: 'error',
            title: 'Falta poner la marca'
          })*/
          $(".datoVacio").removeClass("datoVacio");
          $(".select2-container").addClass("datoVacio");
          Swal.showValidationMessage(
            `Selecciona una marca`
          )
        }else if( data["ancho"] == ""){
          $(".datoVacio").removeClass("datoVacio");
          $(".border-danger").removeClass("border-danger");
          $("#ancho").addClass("border-danger");
          Swal.showValidationMessage(
            `Establece un ancho`
          )
        }else if(data["alto"] == ""){
          $(".datoVacio").removeClass("datoVacio");      
          $(".border-danger").removeClass("border-danger");
          $("#alto").addClass("border-danger");
          Swal.showValidationMessage(
            `Establece un alto`
          )
        }else if( data["rin"] == ""){
          $(".datoVacio").removeClass("datoVacio");      
          $(".border-danger").removeClass("border-danger");
          $("#rin").addClass("border-danger");
          Swal.showValidationMessage(
            `Establece el rin`
          )
        }else if( data["modelo"] == ""){
          $(".datoVacio").removeClass("datoVacio");      
          $(".border-danger").removeClass("border-danger");
          $("#modelo").addClass("border-danger");
          Swal.showValidationMessage(
            `Establece un modelo`
          )
        }else if(data["costo"] == ""){
          $(".datoVacio").removeClass("datoVacio");      
          $(".border-danger").removeClass("border-danger");
          $("#costo").addClass("border-danger");
          Swal.showValidationMessage(
            `Establece el precio que te costó la llanta`
          )
        }else if( data["precio"] == ""){
          $(".datoVacio").removeClass("datoVacio");      
          $(".border-danger").removeClass("border-danger");
          $("#precio").addClass("border-danger");
          Swal.showValidationMessage(
            `Establece un precio`
          )
        }else if(data["mayorista"] == ""){
          $(".datoVacio").removeClass("datoVacio");      
          $(".border-danger").removeClass("border-danger");
          $("#mayorista").addClass("border-danger");
          Swal.showValidationMessage(
            `Establece un precio de mayorista o descuento`
          )
        }else if( data["cantidad"] == ""){
          $(".datoVacio").removeClass("datoVacio");      
          $(".border-danger").removeClass("border-danger");
          $("#cantidad").addClass("border-danger");
          Swal.showValidationMessage(
            `Establece una descripcion`
          )
        }else if( data["descripcion"] == ""){
          $(".datoVacio").removeClass("datoVacio");      
          $(".border-danger").removeClass("border-danger");
          $("#descripcion").addClass("border-danger");
          Swal.showValidationMessage(
            `Establece una descripcion`
          )
        }
      }
      //Si el resultado es OK tons:
    }).then((result) => {  
  
     if(result.isConfirmed){
  
      data = {
        "marca":          $("#select2-marca-container").text(),  
        "ancho":          $("#ancho").val(),
        "alto":           $("#alto").val(),
        "rin":            $("#rin").val(),
        "costo":          $("#costo").val(),
        "precio":         $("#precio").val(),
        "mayorista":      $("#mayorista").val(),
        "modelo":         $("#modelo").val(),
        "descripcion":    $("#descripcion").val()
      };
   
  
      $.ajax({
        type: "POST",
        url: "./modelo/agregar-llanta-inv-total.php",
        data:data,
        cache: false,
        success: function(response) {
          if (response==1) {
            Swal.fire(
              "¡Correcto!",
              "Se agrego la llanta",
              "success"
              ).then((result) =>{

                });
             
          }else{
            Swal.fire(
              "¡Erro!",
              "No se agrego la llanta",
              "error"
              )
              table.draw(false);
          }
            
  
            
        },
        failure: function (response) {
            Swal.fire(
            "Error",
            "La llanta no fue agregada.", // had a missing comma
            "error"
            )
        }
    });
      
  
      
     }
  
     
  
       
     
  }, 
  function (dismiss) {
    if (dismiss === "cancel") {
      swal.fire(
        "Cancelled",
          "Se cancelo la operacion",
        "error"
      )
    };
  })
  
  
  
  }

  //Funcion que traera los stocks de las sucursales
  function traerStockSucursales(id_llanta){
    $.ajax({
      type: "post",
      url: "./modelo/inventarios/traer-stock-sucursales.php",
      data: {id_llanta},
      dataType: "JSON",
      success: function (response) {
        $("#stock_actual").empty();
        if(response.data.length >0){
          response.data.forEach(element => {
            $("#stock_actual").append(`
                  <option>${element.nombre}: ${element.stock}</option>
              `);
          });
        }else{

        }
      }
    });
    
  }

  function cargarComprobanteRegistro(){
    let input_comprobante = document.getElementById('factura-documento');
    let file = input_comprobante.files[0];
    let area_canvas = $("#area-canvas");

    if (file && file.type === 'application/pdf') {
    // Ruta del PDF desde donde se obtendrá la miniatura
    var pdfURL = URL.createObjectURL(file);//'./src/docs/gastos/Folio RAY440.pdf'; // Reemplaza con la ruta correcta a tu archivo PDF
    var pdfjsLib = window['pdfjs-dist/build/pdf'];
    // Obtiene el elemento canvas
    area_canvas.empty().append(`
    <span aria-hidden="true" class="btn-x-documento" onclick="deleteThumb()">×</span>
    <canvas id="thumbnailCanvas" width="100" height="150"></canvas>`)
    var canvas = document.getElementById('thumbnailCanvas');

    // Carga el PDF
    pdfjsLib.getDocument(pdfURL).promise.then(function(pdfDoc) {
      // Obtiene la primera página del PDF (página 1)
      var pageNumber = 1;

      // Renderiza la página en el canvas
      pdfDoc.getPage(pageNumber).then(function(page) {
        var viewport = page.getViewport({ scale: 0.2 });
        var context = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;

        var renderContext = {
          canvasContext: context,
          viewport: viewport,
        };
        
        
        page.render(renderContext).promise.then(function() {
     
        });
      });
    });
   // reader.readAsDataURL(file);
  }else if (file.type.startsWith('image/')){
    // Si es una imagen (cualquier tipo de imagen)
    var reader = new FileReader();
    area_canvas.empty().append(`
    <span aria-hidden="true" class="btn-x-documento" onclick="deleteThumb()">×</span>
    <img src="" height="200" id="gasto-imagen">`)
    let img_preview = document.getElementById('gasto-imagen')
  
      reader.onloadend = function () {
        img_preview.src = reader.result;
      }
    
      if (file) {
        reader.readAsDataURL(file);
       // clearCanvas()
      } else {
        img_previewpreview.src = "";
      }

      Swal.resetValidationMessage()
      toastr.success('Documento adjunto agregado con exito' ); 
  }else{
    area_canvas.empty()
    Swal.showValidationMessage(
      `Tipo de archivo no admitido`
    );
  }
  }

  function clearCanvas() {
    var canvas = document.getElementById('thumbnailCanvas');
    var context = canvas.getContext('2d');
    context.clearRect(0, 0, canvas.width, canvas.height);
  }

  function deleteThumb(){
    $("#factura-documento").val('');
    let area_canvas = $("#area-canvas");
    area_canvas.empty().append(`<canvas id="thumbnailCanvas" width="100" height="150"></canvas>
    <img src="" height="200" id="gasto-imagen">`)
    toastr.success('Documento adjunto eliminado con exito' ); 
    eliminar_comprobante = true;
  }

  function recalculoDeMontos(tipo){
    let costo_input = $("#costo-actual");
    let stock_input = $("#stock");
    let importe_input = $("#importe");
    let tasa_select = $("#tasa");
    let importe_total_input = $("#importe_total");
    if(tipo == 1) //Recalcular costo
    {
      let importe_actual = parseFloat(importe_input.val());
      let cantidad = parseInt(stock_input.val());
      let division = (importe_actual / cantidad);
      let costo_x_unidad = division * parseFloat(tasa_select.val());
      let nuevo_importe = importe_actual * parseFloat(tasa_select.val());
      costo_input.val(costo_x_unidad)
      importe_total_input.val(nuevo_importe)
    }
  }