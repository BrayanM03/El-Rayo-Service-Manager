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
        iconColor : "#36b9cc",
        html:'<div class="m-auto"><label>Agregar un comentario:</label><br><textarea id="comentario" name="motivo" placeholder="Escribe un comentario sobre la cotización..." class="form-control m-auto" style="width:300px;height:80px;" ></textarea></div>',
        }).then((result) => { 

         let comentario = $("#comentario").val();
         $("#hacer-comentario").attr("comentario", comentario);
         console.log(comentario);

        });
 })


//Generar la cotizacion

 function generarCotizacion(){
  cliente = $("#select2-clientes-container").attr("id-cliente");

  if ( !table.data().any()){

    toastr.warning('La tabla no tiene productos', 'Sin productos' ); 

}else if(cliente == ""){
  toastr.warning('Elige un cliente, porfavor', 'Sin cliente' ); 

}else{
    

    llantaData = $("#pre-cotizacion").dataTable().fnGetData();
    console.log(llantaData);
        
      
    total = $("#total-cotizacion").val();
    
    comentario = $("#hacer-comentario").attr("comentario");
    
    //Enviando data
    
    $.ajax({
        type: "POST",
        url: "./modelo/cotizaciones/insertar-cotizacion.php", 
        data: {'data': llantaData,
               'cliente': cliente,
               'total': total,
               'comentario': comentario,
              },
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            if (response) {
                Swal.fire({
                    title: 'Cotización realizada',
                    html: "<span>La cotización se generó con exito</br></span>"+
                    "ID Cotización:" + response,
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
                       

                    }else if(result.isDenied){

                        window.open('./modelo/cotizaciones/generar-reporte-cotizacion.php?id='+ response, '_blank');
                        table.ajax.reload(null,false);
                        $("#pre-cotizacion tbody tr").remove();
                        $(".pre-cotizacion-error").html("");
                        $(".products-grid-error").remove();
                        $("#pre-cotizacion tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
                        $("#pre-cotizacion_processing").css("display","none");
                        $("#total-cotizacion").val(0.00);
                             
                      
                        
                    }else{
                        table.ajax.reload(null,false);
                        $("#pre-cotizacion tbody tr").remove();
                        $(".pre-cotizacion-error").html("");
                        $(".products-grid-error").remove();
                        $("#pre-cotizacion tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
                        $("#pre-cotizacion_processing").css("display","none");
                        $("#total-cotizacion").val(0);
                    }
    
                   $("#hacer-comentario").attr("comentario", " ");
                    });

                    
            }
            
        }
    }); 

    
    
    


}


 }