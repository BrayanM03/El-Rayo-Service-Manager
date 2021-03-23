function generarToken(){
   /* token = random() + random();
    alert(token);*/

    Swal.fire({
        title: "Ingrese el token",
        icon: 'info',
        html: '<form>'+
        '<label >Ingrese el token de acceso para poder cambiar el precio de la llanta</span><br><br>'+
        '<input id="token-validar" class="form-control" placeholder="Codigo"></form>',
        showCancelButton: true,
        cancelButtonText: 'Cerrar',
        cancelButtonColor: '#00e059',
        showConfirmButton: true,
        confirmButtonText: 'Validar', 
        cancelButtonColor:'#ff764d',
        focusConfirm: false,
        iconColor : "#36b9cc",
        backdrop: `
                     transparent
                     no-repeat
                     blur(10px)
                `,
        preConfirm: (respuesta) =>{
                                token_validar = $("#token-validar").val();
                                if(token_validar == ""){
                                     Swal.showValidationMessage( `El valor no puede ir vacio`);
                                 }
        }

}).then((result) =>{

              if(result.isConfirmed){

                  token_validar = $("#token-validar").val();

                   
                                        $.ajax({
                                        type: "post",
                                        url: "./modelo/token.php",
                                        data: {"comprobar-token" : token_validar},
                                        dataType: "json",
                                        success: function (response) {

                                            if (response == 3) {
                                                    alert("El token es correcto");
                                                    document.getElementById('precio').disabled = false;
                                                    $("#precio-tok").attr("onclick", "");
                                                }else if(response == 4){
                                                    alert("El token es incorrecto");
                                            }
                                    
                                        
                                        }
                                    }); 
                                 

                   
                
              }
              }); 
}

function random() {
    token = Math.floor((Math.random() * (9999 - 1000) + 1000)); // Eliminar `0.`

    $.ajax({
        type: "post",
        url: "./modelo/token.php",
        data: {"token" : token},
        dataType: "json",
        success: function (response) {
          if(response == 1){
                location.reload();
          }
           
        }
    });

    
 
};



function tokenActual() {  
    $.ajax({
        type: "post",
        url: "./modelo/token.php",
        data: "traer-token",
        dataType: "json",
        success: function (response) {
            cod = response["codigo"];
            $("#token-actual").text(cod);
        }
    });
}

tokenActual();