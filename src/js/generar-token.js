function generarToken(){
   /* token = random() + random();
    alert(token);*/

    Swal.fire({
        title: "Ingrese el token",
        icon: 'info',
        html: 
        '<label >Ingrese el token de acceso para poder cambiar el precio de la llanta</span><br><br>'+
        '<input id="token-validar" class="form-control" placeholder="Codigo">',
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
                  nuevoToken = Math.floor((Math.random() * (9999 - 1000) + 1000)); // Eliminar `0.`

                   
                                        $.ajax({
                                        type: "post",
                                        url: "./modelo/token.php",
                                        data: {"comprobar-token" : token_validar, "nuevo-token": nuevoToken, 'tipo-token' : 1},
                                        dataType: "json",
                                        success: function (response) {

                                            if (response == 3) {
                                                Swal.fire({
                                                    title: 'Token correcto',
                                                    html: "<span>Ahora puedes cambiar el precio de la llanta</br></span>",   
                                                    icon: "success",
                                                    cancelButtonColor: '#00e059',
                                                    showConfirmButton: true,
                                                    confirmButtonText: 'Aceptar', 
                                                    cancelButtonColor:'#ff764d',
                                                    showDenyButton: false,
                                                    denyButtonText: 'Reporte'
                                                });
                                                    document.getElementById('precio').disabled = false;
                                                    $("#precio-tok").attr("onclick", ""); 

                                                }else if(response == 4){

                                                    Swal.fire({
                                                        title: 'Token incorrecto',
                                                        html: "<span>El token que ingresaste es incorrecto.</br></span>",
                                                        icon: "error",
                                                        cancelButtonColor: '#00e059',
                                                        showConfirmButton: true,
                                                        confirmButtonText: 'Aceptar', 
                                                        cancelButtonColor:'#ff764d',
                                                        showDenyButton: false,
                                                        denyButtonText: 'Reporte'
                                                    });
                                                    
                                                    
                                            }
                                    
                                        
                                        }
                                    }); 
                                 

                   
                
              }
              }); 
}

function random(tipo_token) {
    if(tipo_token==1){
        token = Math.floor((Math.random() * (9999 - 1000) + 1000)); // Eliminar `0.`
        $("#token-actual").empty().append(`
        <img src="src/img/preload.gif" style="width:80px;">
        `)
    }else if (tipo_token==2){
        token = generarCodigoAlfanumerico();

        $("#token-administrativo").empty().append(`
        <img src="src/img/preload.gif" style="width:80px;">
        `)
    }
    setTimeout(function(){
        $.ajax({
            type: "post",
            url: "./modelo/token.php",
            data: {"token" : token, 'tipo_token' : tipo_token},
            dataType: "json",
            success: function (response) {
              if(response.estatus){
                console.log(tipo_token);
                if(tipo_token==1){
                    $("#token-actual").empty().text(response.token_op)
                }else if(tipo_token==2){
                    $("#token-administrativo").empty().text(response.token_admin)
                }else{
                    alert('Ocurrio un error, contacta al administrador de sistemas')
                }
              }
               
            }
        });
    },1300)
};

function generarCodigoAlfanumerico() {
    // Crear un conjunto de caracteres permitidos (letras y números)
    const caracteresPermitidos = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  
    let codigo = '';
    for (let i = 0; i < 5; i++) {
      // Elegir un carácter aleatorio del conjunto
      const caracterAleatorio = caracteresPermitidos.charAt(Math.floor(Math.random() * caracteresPermitidos.length));
  
      // Agregar el carácter al código
      codigo += caracterAleatorio;
    }
  
    return codigo;
  }




