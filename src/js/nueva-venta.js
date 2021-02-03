
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
                url: "./modelo/buscar-llanta.php",
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

                                       "<tr class='producto-individual'>"+
                                       "<td>" + value.Descripcion + "</td>" +
                                       "<td>$" + value.precio_Venta + "</td>" +
                                       "<td><img class='logo-marca' src='./src/img/logos/" + value.Marca + ".jpg'></td>" +
                                       "<td>" + value.id_Sucursal + "</td></tr>");

                                       contenedorTabla.removeClass("oculto");

                          });  
                    }
                 
                    

                   

                    } catch (error) {
                        $(".tbody").empty();
                        contenedorLista = $(".contenedor-tabla");
                        contenedorLista.addClass("oculto");
                        console.log("No se pudo usar la funcion");
                    }        
                   
                    $(".producto-individual").on("click", function () {
                        alert("Click");
                    }); 
                }
            });
        });
      }

      buscar();