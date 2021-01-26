
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

console.log(selects);
selects.forEach( select => {

    select.onfocus = function(){
        alert("EN foco");
       /* select.previousElementSibling.classList.add('top');
        select.previousElementSibling.classList.add('focus');
        select.parentNode.classList.add('focus');  */
    }


    select.onblur = function(){
        
      /*  var indice = select.selectedIndex;
        if (indice == null) {
            select.previousElementSibling.classList.remove('top');
        }else{
            select.previousElementSibling.classList.remove('focus');
        select.parentNode.classList.remove('focus');*/
        alert("Sin");
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
  
