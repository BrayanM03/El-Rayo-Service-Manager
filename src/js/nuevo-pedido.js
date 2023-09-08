//Traer llantas

$(document).ready(function() {

    toastr.options = {
      "closeButton": false,
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
  
  
    $('#busquedaLlantas').select2({
          placeholder: "Selecciona una llanta",
          theme: "bootstrap",
          minimumInputLength: 1,
          ajax: {
              url: "./modelo/traer_stock_llantas_totales.php",
              type: "post",
              dataType: 'json',
              delay: 250,
              data: function (params) {
               return {
                 searchTerm: params.term, // search term
                 page: params.page || 1,
                 rol: params.rol
                 
               };
              },
             
              cache: true
  
          },
          processResults: function (data, params) {
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
      });
  
  
    function formatRepo (repo) {
          
        if (repo.loading) {
          return repo.text;
        }
        
          var $container = $(
              "<div class='select2-result-repository clearfix' desc='"+repo.descripcion+" marca='"+repo.marca +
              " id='"+repo.marca+" costo='"+repo.costo +" id='tyre' precio='"+repo.precio+" idcode='"+repo.id+"'>" +
              "<div class='select2-contenedor-principal row' syle='display:flex;'>" +
              "<div class='col-md-2 justify-content-center'><img class='' style='width: 50px; border-radius: 6px;' src='./src/img/logos/" + repo.marca + ".jpg' /></div>" +
                "<div class='col-md-10 select2-contenedor'>" +
                "<div class='select2_modelo'>Modelo "+ repo.modelo +"</div>" +
                "<div class='select2_description'>" + repo.descripcion + "</div>" +
                "</div>" +
                "</div>" +
                "<div class='select2_statistics' style='display:flex; border-top: 1px solid whitesmoke; padding-top:8px; justify-content:space-around; margin-top:5px;'>" +
                "<div class='select2_marca'><i class='fa fa-star'></i> "+ repo.marca+"</div>" +
                  "<div class='select2_costo'><i class='fa fa-dollar-sign'></i> "+repo.costo+" (Costo) </div>" +
                  "<div class='select2_precio_venta'><i class='fa fa-tag'></i> "+ repo.precio +" (precio)</div>" + 
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
          
          $("#btn-agregar").attr("idcode", repo.id);
          $("#btn-agregar").attr("descripcion", repo.descripcion);
          $("#btn-agregar").attr("modelo", repo.modelo);
          $("#btn-agregar").attr("marca", repo.marca);
          $("#btn-agregar").attr("costo", repo.costo);
          $("#btn-agregar").attr("precio", repo.precio);
          $("#precio").val(repo.precio);
          $("#precio").addClass('disabled');
          $('#precio').prop('disabled', true);
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
         
  
          return repo.text || repo.descripcion;
        }
  });
  
  
  
  //traer clientes
  
  $(document).ready(function() {
  
      $("#clientes").select2({
          placeholder: "Clientes",
          theme: "bootstrap",
          ajax: {
              url: "./modelo/ventas/traer_clientes.php",
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
  
            templateResult: formatResultClientes,
            templateSelection: formatSelection
  
      });
  
      function formatResultClientes(repo){
  
  
          if (repo.loading) {
              return repo.text;
            }
            
            if (repo.credito == 0) {
                cred = "Sin credito"
                badge="badge-info";
            }else if (repo.credito == 1){
                cred= "Con credito";
                badge = "badge-warning";
            }
  
              var $container = $(
                  "<span id='"+repo.id+"'>"+ repo.nombre +" <span class='badge " + badge +"'>"+ cred +"</span></span>"
              );
            
             
              //
            
              return $container;
  
      }
  
      function formatSelection (repo) {
          //A partir de aqui puedes agregar los clientes
          
          $("#select2-clientes-container").attr("id-cliente", repo.id);
       
         
  
          return repo.text || repo.nombre;
        }
  
  //Select2 para los metodos de pago:
  
      
  });
  
  
  $("#hacer-comentario").on("click", function () { 
    Swal.fire({
      title: "Comentario",
      showCancelButton: true,
          cancelButtonText: 'Cerrar',
          cancelButtonColor: '#00e059',
          showConfirmButton: true,
          confirmButtonText: 'Agregar', 
          cancelButtonColor:'#ff764d',
          focusConfirm: false,
          didOpen:()=>{
            let comentario = $("#hacer-comentario").attr("comentario");
            $("#comentario").val(comentario);
          },
          iconColor : "#36b9cc",
          html:'<div class="m-auto"><label>Agregar un comentario:</label><br><textarea id="comentario" name="motivo" placeholder="Escribe un comentario sobre el pedido..." class="form-control m-auto" style="width:300px;height:80px;" ></textarea></div>',
          }).then((result) => { 
           if(result.isConfirmed) {
            let comentario = $("#comentario").val();
            if(comentario.trim() != ''){
                $("#hacer-comentario").attr("comentario", comentario);
                toastr.success('Comentario agregado correctamente', 'Exito');
            }else{
                toastr.success('Agregaste un comentario vacio, pero bueno 🤷🏻‍♂️', 'Exito');
            }
           }
          });
   })
  
  
  //Generar la cotizacion

  function designarAdelanto(){

    let metodo_pago = $("#metodos-pago").val();  

    if ( !table.data().any()){
      
      toastr.warning('La tabla no tiene productos', 'Sin productos' ); 
    }else{
      if(metodo_pago.length == 0){
          toastr.warning('Agrega un metodo de pago', 'Sin metodo pago' ); 
      }else{
        llantaData = $("#pre-cotizacion").dataTable().fnGetData();
        
        Swal.fire({
          title: "Monto del adelanto",
          background: "#dcdcdc" ,
          width: '800px',
          showCancelButton: true,
          cancelButtonText: 'Cerrar',
          cancelButtonColor: '#00e059',
          showConfirmButton: true,
          confirmButtonText: 'Realizar pedido', 
          cancelButtonColor:'#ff764d',
          html: `
          <div class="container">
              <div id="contenedor-metodos"></div>
              <div id="contenedor-token" class="mt-4 mb-3"></div>
          </div>`,
          didOpen: function () { 
            let button_confirm = document.querySelector('.swal2-confirm');
             button_confirm.style.backgroundColor = '#858796';  
            $("#contenedor-metodos").empty();
            var opciones = {
              0: "Efectivo",
              1: "Tarjeta",
              2: "Transferencia",
              3: "Cheque",
              4: "Sin definir"
            };
    
              var importe_total_actual =  llantaData.reduce(function(total, element) {
              let total_importe = parseFloat(element.importe) + parseFloat(total);
              return  total_importe;
            }, 0);
    
            var arregloMetodos= metodo_pago.reduce(function(result, key) {
              result[key] = opciones[key];
              return result;
            }, {});
         
            for(var clave in arregloMetodos) {
              if (arregloMetodos.hasOwnProperty(clave)) {
                var nombre_metodo = arregloMetodos[clave];
                $("#contenedor-metodos").append(`
                  <div class="row mt-2">
                  <div class="col-md-12">
                      <label>Monto para pago ${nombre_metodo}</label>
                      <input type="number" class="form-control" id="monto_metodo_${clave}" onkeyup="calcularMontosAdelanto(${importe_total_actual})" placeholder="0.00">
                  </div>
                  </div>
              `);}
              }
    
              $("#contenedor-metodos").append(`
              <div class="row mt-3">
              <div class="col-md-6">
                  <label>Importe total</label>
                  <h1><span class="badge badge-info" id="badge-total">$${importe_total_actual}</span><h1>
                  <input type="hidden" value="${importe_total_actual}" class="form-control" id="total_importe" disabled>
              </div>
              <div class="col-md-6">
                  <label>Restante</label>
                  <h1><span class="badge badge-secondary" id="badge-restante">$${importe_total_actual}</span><h1>
                  <input type="hidden" value="${importe_total_actual}" class="form-control" is-valid="false" id="total_restante" disabled>
              </div>
              <div class="col-md-12">
                  <h4 id="validador-adelanto"><span id="text-message" class="text-secondary"></span><h4>
              </div>
              </div>
              `) 
              calcularMontosAdelanto(importe_total_actual)
            },
            preConfirm: function(){
              let con_token = $("#validador-adelanto").attr("token");
              let valid_token = $("#validador-adelanto").attr("valid-token");
              let token = $("#token").val() === undefined ? 0 : $("#token").val();
             

              if(con_token === 'true' && (token == undefined || token == '' || token == null || token == 0)){
                Swal.showValidationMessage(
                  `Ingresa el token:`
                )
                $('#contenedor-token').empty().append(`
                    <div class="row">
                        <div class="col-12">
                            <input type="number" id="token" class="form-control" placeholder="Ingresa aqui el token">
                        </div>
                    </div>  
                    <div class="row mt-4 justify-content-center">
                        <div class="col-4 text-center" id="area-loader">
                        
                        </div>
                    </div>       
                `);
              }else if(con_token && token > 0){
                //swal.showLoading();
                $('#area-loader').append(`
                <div class="loader"></div>
                `);
                return new Promise((resolve) =>{
                  $.ajax({
                    type: "POST",
                    url: "./modelo/token.php",
                    data: {"traer-token": true},
                    dataType: "JSON",
                    success: function (response) {
                      if(response.codigo== token){
                        resolve();
                      }else{
                        setTimeout(()=>{
                          $('#area-loader').empty();
                          document.querySelector('.swal2-confirm').removeAttribute('disabled');
                          Swal.showValidationMessage(
                            `El Token ingresado es incorrecto`
                          )
                        }, 1500)
                       
                      };
                    }
                  });
                })
              }else{
                if($("#validador-adelanto").attr("is-valid") == "false"){
                  Swal.showValidationMessage(
                    `No se puede apartar, corrija los montos`
                  )
                }
              }
            }
          
        }).then(function (ress) {
          if(ress.isConfirmed){
            var opciones = {
              0: "Efectivo",
              1: "Tarjeta",
              2: "Transferencia",
              3: "Cheque",
              4: "Sin definir"
            };
    
            var arregloMetodos= metodo_pago.reduce(function(result, key) {
              let monto = parseFloat(document.getElementById(`monto_metodo_${key}`).value);
              let monto_ = Number.isNaN(monto) ? 0 : monto;
              result[key] = {"id_metodo":key, "metodo":opciones[key], "monto": monto_};
              return result;
            }, {});
             generarPedido(arregloMetodos);
          }
        })
      }
    }
   }  
  
   function generarPedido(arregloMetodos){
    cliente = $("#select2-clientes-container").attr("id-cliente");
  
    if ( !table.data().any()){
  
      toastr.warning('La tabla no tiene productos', 'Sin productos' ); 
  
  }else if(cliente == ""){
    toastr.warning('Elige un cliente, porfavor', 'Sin cliente' ); 
  
  }else{
      
  
      llantaData = $("#pre-cotizacion").dataTable().fnGetData();  
      total = $("#total-cotizacion").val(); 
      tipo_cotizacion = $("#btn-agregar").attr('cotizacion');
      comentario = $("#hacer-comentario").attr("comentario");
      
      //Enviando data
      $.ajax({
          type: "POST",
          url: "./modelo/cotizaciones/insertar-cotizacion.php", 
          data: {'data': llantaData,
                'metodos_pago': arregloMetodos,
                 'cliente': cliente,
                 'total': total,
                 'comentario': comentario,
                 'tipo_cotizacion': tipo_cotizacion
                },
          dataType: "JSON",
          success: function (response) {
              if (response) {
                  Swal.fire({
                      title: 'Pedido realizada',
                      html: "<span>La cotización se generó con exito</br></span>"+
                      "Folio Pedido:" + response,
                      icon: "success",
                      cancelButtonColor: '#00e059',
                      showConfirmButton: true,
                      confirmButtonText: 'Aceptar', 
                      cancelButtonColor:'#ff764d',
                      showDenyButton: true,
                      denyButtonText: 'Ver'
                  },
                     
                    ).then((result) =>{
        
                      if(result.isConfirmed){
                         //location.reload();
                         table.ajax.reload(null,false);
                          $("#pre-cotizacion tbody tr").remove();
                          $(".pre-cotizacion-error").html("");
                          $(".products-grid-error").remove();
                          $("#pre-cotizacion tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
                          $("#pre-cotizacion_processing").css("display","none");
                          $("#total-cotizacion").val(0);
                          table.clear().draw();
                         
  
                      }else if(result.isDenied){
  
                          window.open('./modelo/pedidos/generar-reporte-pedido.php?id='+ response, '_blank');
                          table.ajax.reload(null,false);
                          $("#pre-cotizacion tbody tr").remove();
                          $(".pre-cotizacion-error").html("");
                          $(".products-grid-error").remove();
                          $("#pre-cotizacion tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
                          $("#pre-cotizacion_processing").css("display","none");
                          $("#total-cotizacion").val(0.00);
                          table.clear().draw();
                               
                        
                          
                      }else{
                          table.ajax.reload(null,false);
                          $("#pre-cotizacion tbody tr").remove();
                          $(".pre-cotizacion-error").html("");
                          $(".products-grid-error").remove();
                          $("#pre-cotizacion tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
                          $("#pre-cotizacion_processing").css("display","none");
                          $("#total-cotizacion").val(0);
                          table.clear().draw();
                      }
      
                     $("#hacer-comentario").attr("comentario", " ");
                      });
  
                      
              }
              
          }
      }); 
  
      
      
      
  
  
  }
  
  
   }

   function calcularMontosAdelanto(importe){
   
    let button_confirm = document.querySelector('.swal2-confirm');
      var inputs = document.querySelectorAll("#contenedor-metodos input[type=number]");  // Obtener todos los inputs
      var resta = 0;
      var sumatoria_monto = 0;
      inputs.forEach(function(input) {
        var valor = parseFloat(input.value);
        if (isNaN(valor)) {
          //valor = valor == '' ? 0 : valor
         valor = 0;
        }
        sumatoria_monto += valor; 
        resta = importe - sumatoria_monto;
      });
      
  
      // Verificar si la suma es igual al precio_llanta y actualizar el badge
      var badgeRestante = document.getElementById("badge-restante");
      var text_message = document.getElementById("text-message");
  
      // Calculamos los montos minimos del adelanto
      if(importe < 3000){
        var porcentaje = 0.10;
      }else if(importe >= 3000 && importe < 10000){
        var porcentaje = 0.15;
      }else if(importe >= 10000 && importe < 20000){
        var porcentaje = 0.20;
      }else if(importe >= 20000){
        var porcentaje = 0.25;
      }
  
      const porcentajeFormateado = (porcentaje * 100).toFixed(0) + '%';
      const monto_minimo = importe * porcentaje;
  
      //Codigo para obtener el monto minimo del 24% del monto total
      const redondeado = Math.round(monto_minimo + 0.001);
      const resta_redondeada = Math.round(resta + 0.001);
      const formatter = new Intl.NumberFormat('es-MX', {
          style: 'currency',
          currency: 'MXN',
        });
      const monto_formateado = formatter.format(redondeado);
      const resta_formateada = formatter.format(resta_redondeada);
  
    
    
    if(sumatoria_monto == 0){
      $("#validador-adelanto").attr("token", "true");
     // text_message.textContent = `Ingrese un token:`;
    }else if (sumatoria_monto < redondeado) {
        badgeRestante.classList.remove("badge-success");
        badgeRestante.classList.remove("badge-danger");
        badgeRestante.classList.add("badge-secondary");
  
        button_confirm.style.backgroundColor = '#858796';
        button_confirm.style.borderColor = '#858796';
        text_message.classList.remove("text-success");
        text_message.classList.remove("text-danger");
        text_message.classList.add("text-secondary");
        $("#validador-adelanto").attr("token", "false");
        
        text_message.textContent = `Agregue un monto minimo del ${porcentajeFormateado} = ${monto_formateado}`;
        $("#validador-adelanto").attr("is-valid", "false")
            
      }else if(resta < 0){
        
        badgeRestante.classList.remove("badge-success");
        badgeRestante.classList.remove("badge-secondary");
        badgeRestante.classList.add("badge-danger");
        $("#validador-adelanto").attr("token", "false");
        button_confirm.style.backgroundColor = '#dc3545';
        button_confirm.style.borderColor = '#dc3545';
        text_message.classList.remove("text-success");
        text_message.classList.remove("text-secondary");
        text_message.classList.add("text-danger");
        text_message.textContent = 'El resta es menor que el total';
        $("#validador-adelanto").attr("is-valid", "false")
      }else if(sumatoria_monto >= redondeado){
  
        badgeRestante.classList.remove("badge-secondary");
        badgeRestante.classList.remove("badge-danger");
        badgeRestante.classList.add("badge-success");
        $("#validador-adelanto").attr("token", "false");
        button_confirm.style.backgroundColor = '#1cc88a';
        button_confirm.style.borderColor = '#1cc88a';
        text_message.classList.remove("text-success");
        text_message.classList.remove("text-danger");
        text_message.classList.add("text-secondary");
  
        text_message.textContent = '';
        $("#validador-adelanto").attr("is-valid", "true")
        //audio_2.play();  
      }
      $("#badge-restante").empty().append(`${resta_formateada}`);
      $("#total_restante").val(resta_redondeada);
  }