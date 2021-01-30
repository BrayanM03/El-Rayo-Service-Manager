
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
        var inAncho = $("#ancho");
            inAncho.keyup(function () { 
            var Anchovalor = $(this).val();
            console.log(Anchovalor);

            $.ajax({
                type: "post",
                url: "./modelo/buscar-llanta.php",
                data: {ancho: Anchovalor},
                success: function (response) {
                    console.log("correcto la respuesta fue: "+ response);                }
            });
        });
      }

      buscar();