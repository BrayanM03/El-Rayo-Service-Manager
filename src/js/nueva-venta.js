
const inputs = document.querySelectorAll(".input-group");
const selects = document.querySelectorAll(".select-group");


inputs.forEach( input => {  

    input.onfocus = function(){
        input.previousElementSibling.classList.add('top');
        input.previousElementSibling.classList.add('focus');
        input.parentNode.classList.add('focus');  
    }


    input.onblur = function(){
        input.value = input.value.trim();
        if (input.value.trim().length == 0) {
            input.previousElementSibling.classList.remove('top');
        }
        
        input.previousElementSibling.classList.remove('focus');
        input.parentNode.classList.remove('focus');
    }
});


selects.forEach( select => {

    select.onfocus = function(){
        select.previousElementSibling.classList.add('top');
        select.previousElementSibling.classList.add('focus');
        select.parentNode.classList.add('focus');  
    }


    select.onblur = function(){
        
      var indice = select.selectedIndex;
      
        if (indice == 0) {
            select.previousElementSibling.classList.remove('top');
            select.previousElementSibling.classList.remove('focus');
            select.parentNode.classList.remove('focus');
        }else{
            select.previousElementSibling.classList.remove('focus');
        select.parentNode.classList.remove('focus');
       
        }
    }
        
        
});


  
    function buscar() {
        var inAncho = $("#search");
            inAncho.keyup(function () { 
            var Anchovalor = $(this).val();
            
            entrada="et"

            inputSearch = $("#search").val();
            $(".tbody").empty();

            $.ajax({
                type: "post",
                url: "./modelo/buscar_llantas_pedro.php",
                async: true,
                data: {entrada: entrada, ancho: Anchovalor},
                success: function (response) {

                    

                    try {
                        var jsonObject = JSON.parse(response);
                        var Anchos = jsonObject;
                   

                    if(inputSearch.length == 0){
                        $(".tbody").empty();
                        contenedorLista = $(".contenedor-tabla");
                        contenedorLista.addClass("oculto");

                    }else{
                        $.each(Anchos, function(key, value) { 
                           
                            contenedorTabla = $(".contenedor-tabla");
                        
                             tablaBusqueda = $(".tbody");

                             if(value.Stock == 0){
                                color = "table-danger";
                             }else{
                                color = "";
                             }
                               
                                tablaBusqueda.append(

                                       "<tr class='producto-individual "+ color +"' "+
                                       "id='"+value.id + "' "+
                                       "cod='"+value.Codigo + "' "+
                                       "descripcion='"+value.Descripcion + "' " +
                                       "modelo='"+value.Modelo + "'"  +
                                       "precio-venta='"+value.precio_Venta + "' " +
                                       "precio-mayoreo='"+value.precio_Mayoreo + "' "  +
                                       "marca='"+value.Marca + "'"  +
                                       "sucursal='"+value.Sucursal + "' "  +
                                       "stock='"+value.Stock +
                                       "'>"+
                                       "<td>" + value.Codigo + "</td>" +
                                       "<td>" + value.Descripcion + "</td>" +
                                       "<td>" + value.Modelo + "</td>" +
                                       "<td>$" + value.precio_Venta + "</td>" +
                                       "<td>$" + value.precio_Mayoreo + "</td>" +
                                       "<td class='cont-marca'><img class='logo-marca' marca='"+ value.Marca + "' src='./src/img/logos/" + value.Marca + ".jpg'>"+
                                       "<span>"+ value.Marca+"<span></td>" +
                                       "<td>" + value.Sucursal + "</td>" +
                                       "<td>" + value.Stock + "</td></tr>");

                                       contenedorTabla.removeClass("oculto");
                                       
                          }); 




                         
                          
                          //Obtener datos de fila clickeada

                          $(".producto-individual").on("click", function () {

                            id1              = $(this).attr("id");
                            cod1             = $(this).attr("cod");
                            descripcion1     = $(this).attr("descripcion");
                            modelo1          = $(this).attr("modelo");
                            precio_Venta1    = $(this).attr("precio-venta");
                            precio_Mayoreo1  = $(this).attr("precio-mayoreo");
                            marca1           = $(this).attr("marca");
                            sucursal1        = $(this).attr("sucursal");
                            stock1           = $(this).attr("stock");
                            
                            if (stock1 == 0) {

                                Swal.fire({
                                    title: 'Ya no quedan llantas',
                                    html: "<span>La llanta: </br>"+
                                    "Codigo: <strong>"+ cod1+"</strong></br>"+
                                    "Marca: <strong>"+ marca1 +"</strong></br>"+
                                    "Descripcion: <strong>"+ descripcion1 +"</strong></br>"+
                                    "Se agoto del inventario, contacta a un administrador para que modifique el inventario</span>",
                                    icon: "warning",
                                    cancelButtonColor: '#00e059',
                                    showConfirmButton: true,
                                    confirmButtonText: 'Aceptar', 
                                    cancelButtonColor:'#ff764d'
                                });

                            }else{

                                $("#description").focus().val(descripcion1);
                                $("#modelo").focus().val(modelo1);
                                $("#precio").focus().val(precio_Venta1);
    
                                $("#agregar-producto").attr("idLlanta", id1);
                                $("#agregar-producto").attr("stock", stock1); 
                                $("#agregar-producto").attr("codigo", cod1);

                                contenedorTabla.addClass("oculto");
        
                               
                                if(sucursal1 == "Sendero"){
                                    select = $("#sucursal");
                                    select.focus().val(1).blur();
        
                                }else{
                                
                                    select = $("#sucursal");
                                    select.focus().val(0).blur();
                                }
    
                                
        
                                var cuadro = document.getElementsByClassName("logo-marca-grande")[0];
                                
                                cuadro.style.backgroundImage = "url('src/img/logos/"+ marca1 +".jpg')";
        
                                inAncho.focus().val("");
                                inAncho.blur();
                                $("#modelo").blur();
                                $("#description").blur();
                                $("#precio").blur();
                            }
                           
                        });
                    }
   

                   

                    } catch (error) {
                        $(".tbody").empty();
                        contenedorLista = $(".contenedor-tabla");
                        contenedorLista.addClass("oculto");
                        console.log("No se encontro llanta en el inventario de la Pedro Cardenas");
                    }    

                    
                    
                    
                   
                }
            }); //Termina la llamada AJAX para la sucursal 1
        
            $.ajax({
                type: "post",
                url: "./modelo/buscar_llantas_sendero.php",
                async: true,
                data: {entrada: entrada, ancho: Anchovalor},
                success: function (response) {

                    

                    try {
                        var jsonObject = JSON.parse(response);
                        var Anchos = jsonObject;
                        

                    if(inputSearch.length == 0){
                        $(".tbody").empty();
                        contenedorLista = $(".contenedor-tabla");
                        contenedorLista.addClass("oculto");

                    }else{
                        $.each(Anchos, function(key, value2) { 
                           
                            contenedorTabla = $(".contenedor-tabla");
                        
                             tablaBusqueda = $(".tbody");

                             if(value2.Stock == 0){
                                color = "table-danger";
                             }else{
                                color = "";
                             }
                               
                                tablaBusqueda.append(
                                        "<tr class='producto-individual "+ color +"' "+
                                        "id='"+value2.id + "' "+
                                        "cod='"+value2.Codigo + "' "+
                                        "descripcion='"+value2.Descripcion + "' "  +
                                        "modelo='"+value2.Modelo + "'"  +
                                        "precio-venta='"+value2.precio_Venta + "' "  +
                                        "precio-mayoreo='"+value2.precio_Mayoreo + "' "  +
                                        "marca='"+value2.Marca + "' "  +
                                        "sucursal='"+value2.Sucursal + "' "  +
                                        "stock='"+value2.Stock +
                                        "'>"+
                                       "<td>" + value2.Codigo + "</td>" +
                                       "<td>" + value2.Descripcion + "</td>" +
                                       "<td>" + value2.Modelo + "</td>" +
                                       "<td>$" + value2.precio_Venta + "</td>" +
                                       "<td>$" + value2.precio_Mayoreo + "</td>" +
                                       "<td><img class='logo-marca' marca='"+ value2.Marca + "' src='./src/img/logos/" + value2.Marca + ".jpg'>"+
                                       "<span>"+ value2.Marca+"<span></td>" +
                                       "<td>" + value2.Sucursal + "</td>" +
                                       "<td>" + value2.Stock + "</td></tr>");
 
                                       contenedorTabla.removeClass("oculto");

                                     

                          });  

                          $(".producto-individual").on("click", function () {
                       
                            id1              = $(this).attr("id");
                            cod1              = $(this).attr("cod");
                            descripcion1     = $(this).attr("descripcion");
                            modelo1          = $(this).attr("modelo");
                            precio_Venta1    = $(this).attr("precio-venta");
                            precio_Mayoreo1  = $(this).attr("precio-mayoreo");
                            marca1           = $(this).attr("marca");
                            sucursal1        = $(this).attr("sucursal");
                            stock1           = $(this).attr("stock"); 
    
                           

                            if (stock1==0) {
                                
                                Swal.fire({
                                    title: 'Ya no quedan llantas',
                                    html: "<span>La llanta: </br>"+
                                    "Codigo: <strong>"+ cod1+"</strong></br>"+
                                    "Marca: <strong>"+ marca1 +"</strong></br>"+
                                    "Descripcion: <strong>"+ descripcion1 +"</strong></br>"+
                                    "Se agoto del inventario, contacta a un administrador para que modifique el inventario</span>",
                                    icon: "warning",
                                    cancelButtonColor: '#00e059',
                                    showConfirmButton: true,
                                    confirmButtonText: 'Aceptar', 
                                    cancelButtonColor:'#ff764d'
                                })

                            }else{

                            $("#description").focus().val(descripcion1);
                            $("#modelo").focus().val(modelo1);
                            $("#precio").focus().val(precio_Venta1);

                            $("#agregar-producto").attr("stock", stock1);
                            $("#agregar-producto").attr("idLlanta", id1);
                            $("#agregar-producto").attr("codigo", cod1);
    
                            contenedorTabla.addClass("oculto");
                           
                            if(sucursal1 == "Sendero"){
                                select = $("#sucursal").focus().val(1).blur();
                                
    
                            }else{
                                select = $("#sucursal").focus().val(0).blur();
                                
                            }
                            
                            var cuadro = document.getElementsByClassName("logo-marca-grande")[0];
                            
                            cuadro.style.backgroundImage = "url('src/img/logos/"+ marca1 +".jpg')";
    
                            inAncho.focus().val("");
                            inAncho.blur();
                            
                            $("#modelo").blur();
                            $("#description").blur();
                            $("#precio").blur();

                            

                            }

                            
                            
                            
                        });
                    }
   

                   

                    } catch (error) {
                       /* $(".tbody").empty();
                        contenedorLista = $(".contenedor-tabla");
                        contenedorLista.addClass("oculto");*/
                        console.log("No encontro llantas de la sucursal Sendero");
                    }        
                   

                   


                }
            }); //Termina la llamada AJAX para sucursal 2
        
        
        });
      }

      buscar();

     



      function realizarVenta(){


        
        if ( !table.data().any()){

            toastr.warning('La tabla no tiene productos', 'Sin productos' ); 

        }else{
            

            llantaData = $("#pre-venta").dataTable().fnGetData();
            console.log(llantaData);
                
              
            total = $("#total").val();
            fecha = $("#fecha").val(); 
            cliente = $("#select2-clientes-container").attr("id-cliente");
            metodo_pago = $("#metodos-pago").val();  
            

            //Enviando data


            
            $.ajax({
                type: "POST",
                url: "./modelo/ventas/insertar-venta.php",
                data: {'data': llantaData,
                       'cliente': cliente,
                       'metodo_pago': metodo_pago,
                       'fecha': fecha,
                       'total': total},
                dataType: "JSON",
                success: function (response) {
                    console.log(response);
                    if (response) {
                        Swal.fire({
                            title: 'Venta realizada',
                            html: "<span>La venta se realizo con exito</br></span>"+
                            "ID Venta: RAY" + response,
                            icon: "success",
                            cancelButtonColor: '#00e059',
                            showConfirmButton: true,
                            confirmButtonText: 'Aceptar', 
                            cancelButtonColor:'#ff764d',
                            showDenyButton: true,
                            denyButtonText: 'Reporte'
                        },
                           
                          ).then((result) =>{
              
                            if(result.isConfirmed){
                               //location.reload();
                               table.ajax.reload();
                                $("#pre-venta tbody tr").remove();
                                $(".pre-venta-error").html("");
                                $(".products-grid-error").remove();
                                $("#pre-venta tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
                                $("#pre-venta_processing").css("display","none");
                                $("#total").val(0);
                               

                            }else if(result.isDenied){
    
                                window.open('./modelo/ventas/generar-reporte-venta.php?id='+ response, '_blank');
                                table.ajax.reload();
                                $("#pre-venta tbody tr").remove();
                                $(".pre-venta-error").html("");
                                $(".products-grid-error").remove();
                                $("#pre-venta tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
                                $("#pre-venta_processing").css("display","none");
                                $("#total").val(0);
                                     
                              
                                
                            }else{
                                table.ajax.reload();
                                $("#pre-venta tbody tr").remove();
                                $(".pre-venta-error").html("");
                                $(".products-grid-error").remove();
                                $("#pre-venta tbody").append('<tr><th id="empty-table" style="text-align: center;" style="width: 100%" colspan="8">Preventa vacia</th></tr>');
                                $("#pre-venta_processing").css("display","none");
                                $("#total").val(0);
                            }
            
                           
                            });

                            
                    }
                    
                }
            }); 

            
            
            


        }
        //$(".tbody").empty();
      }


      $(document).on("click",function(e) {

        var container = $("#table-llantas-mostradas");

            if (!container.is(e.target) && container.has(e.target).length === 0) { 
                contenedorLista = $(".contenedor-tabla");
                contenedorLista.addClass("oculto");

            }
   });


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
            default:
                break;
        }

        
        return $state;
      };

   

    
});



    

        