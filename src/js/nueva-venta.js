
const inputs = document.querySelectorAll(".input-group");

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

    
$(document).ready(function() {
        $('#pre-venta').DataTable({
            responsive: true,
            language: {
            
                emptyTable: "No hay registros",
                infoEmpty: "Ups!, no hay registros aun en esta categoria."
              }
        });
    } );
  
