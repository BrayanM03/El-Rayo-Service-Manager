//Traer llantas

$(document).ready(function() {
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
            "<div class='select2-result-repository clearfix' id='"+repo.id+"'>" +
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

        $(".select2-result-repository clearfix").on("click", function() { 
          alert("Llanta " + repo.marca);
         })  
        //
      
        return $container;
      }

      function formatRepoSelection (repo) {
        //A partir de aqui puedes agregar las llantas Brayan
        ruta = "./src/img/logos/" + repo.marca + ".jpg";
        
        $("#select2-busquedaLlantas-container").attr("codigo", repo.id);
        $("#ancho-agregado").text(repo.ancho);
        $("#alto-agregado").text(repo.alto);
        $("#rin-agregado").text(repo.rin);
        $("#modelo-agregado").text(repo.modelo);
        $("#marca-agregado").text(repo.marca);
        $(".logo-marca-agregada").attr("src", ruta);

        $("#costo-agregado").text(repo.costo);
        $("#precio-agregado").text(repo.precio);
        $("#mayoreo-agregado").text(repo.mayoreo);
        $("#mayoreo-agregado").fadeIn(400)
       

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

    $("#metodos-pago").select2({
        placeholder: "Metodo de pago",
        theme: "bootstrap",
        templateResult: formatState,
    });


    function formatState (state) {
        if (!state.id) {
          return state.text;
        }

        switch (state.text) { 
            case "Efectivo":
                var $state = $(
                    '<span><i class="fas fa-money-bill-wave"></i> '+state.text+'</span>'
                    
                  );
                
                break;
            case "Tarjeta":
                var $state = $(
                    '<span><i class="fas fa-money-check"></i> '+state.text+'</span>'
                    
                  );
                
                break;
            case "Transferencia":
                var $state = $(
                    '<span><i class="fas fa-university"></i> '+state.text+'</span>'
                        
                );
                    
                    break;           
            case "Cheque":
                var $state = $(
                    '<span><i class="fas fa-money-check-alt"></i> '+state.text+'</span>'
                );
                            
            break; 
            
            case "Sin definir":
                var $state = $(
                    '<span><i class="fas fa-question"></i> '+state.text+'</span>'
                );
                            
            break; 

            default:
                break;
        }

        
        return $state;
      };

   

    
});