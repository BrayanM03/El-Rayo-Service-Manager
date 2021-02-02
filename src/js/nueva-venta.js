
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

            if(inputSearch.length == 0){
                contenedorLista = $(".contenedor-lista");
                contenedorLista.empty();
                contenedorLista.addClass("oculto");
            }

            $.ajax({
                type: "post",
                url: "./modelo/buscar-llanta.php",
                async: true,
                data: {entrada: entrada, ancho: Anchovalor},
                success: function (response) {

                    $(".contenedor-lista").empty();

                    try {
                        var jsonObject = JSON.parse(response);
                        var Anchos = jsonObject;
                   

                    if(inputSearch.length == 0){
                        contenedorLista = $(".contenedor-lista");
                        contenedorLista.empty();
                        contenedorLista.addClass("oculto");

                    }else{
                        $.each(Anchos, function(key, value) { 
                           // console.log("Ancho: " + value.Descripcion);
                            

                            contenedorLista = $(".contenedor-lista");
                            contenedorLista.removeClass("oculto");
                            contenedorLista.append("<ul id='lista-busqueda'></ul>");
                            listaBusqueda = $("#lista-busqueda");
                            listaBusqueda.append("<li class='producto-individual'>"+ 
                            "<span>LLanta: <b style='color: black;'>"+value.Ancho+"/"+value.Proporcion+value.Diametro+"</b> Modelo: "+value.Modelo+" Stock: "+value.Stock+"</span></li>");
                            
                          
                         });  
                    }

                    } catch (error) {
                        contenedorLista = $(".contenedor-lista");
                        contenedorLista.addClass("oculto");
                    }        
                   
                    $(".producto-individual").on("click", function () {
                        alert("Click");
                    }); 
                }
            });
        });
      }

      buscar();