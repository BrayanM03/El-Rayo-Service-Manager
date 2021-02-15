
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
                               
                                tablaBusqueda.append(

                                       "<tr class='producto-individual' "+
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
                                       "<td><img class='logo-marca' marca='"+ value.Marca + "' src='./src/img/logos/" + value.Marca + ".jpg'></td>" +
                                       "<td>" + value.Sucursal + "</td>" +
                                       "<td>" + value.Stock + "</td></tr>");

                                       contenedorTabla.removeClass("oculto");
                                       
                          }); 
                          
                          //Obtener datos de fila clickeada

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

                             
                               
                                tablaBusqueda.append(
                                        "<tr class='producto-individual' "+
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
                                       "<td><img class='logo-marca' marca='"+ value2.Marca + "' src='./src/img/logos/" + value2.Marca + ".jpg'></td>" +
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

    

        