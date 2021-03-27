
    function iniciarSesion(){
       

        user = $("#user").val();
        pass = $("#password").val();

       

        if(pass == "" || user == ""){
           
                        alerta = $("#alerta");
                        alerta.removeClass("mensaje-oculto");
                        alerta.addClass("mensaje-sesion");
                        label = $("#label-alert");
                        label.removeClass("alert-danger");
                        label.addClass("alert-warning");
                        label.html("<i class='fas fa-exclamation-triangle'></i> Ingrese credenciales");
        }else{

            loginform = $("#login-form");
            datos = loginform.serialize();
            console.log(datos);
            $.ajax({
                type: "POST",
                url: "./modelo/login/iniciar-sesion.php",
                data: datos,
               
                success: function (response) {
                    if(response == 0){
                        alerta = $("#alerta");
                        alerta.removeClass();
                        alerta.addClass("mensaje-sesion");
                        label = $("#label-alert");
                        label.removeClass();
                        label.addClass("alert alert-danger");
                        label.html("<i class='fas fa-exclamation-circle'></i> Contraseña incorrecta")
                        alerta = $("#mensaje-sesion");
                        alerta.removeClass("hidden");
                        
                    }else if(response == 1){
                        window.location = "./index.php";;
                    }else if(response == 2){
                        alerta = $("#alerta");
                        alerta.removeClass();
                        alerta.addClass("mensaje-sesion");
                        label = $("#label-alert");
                        label.removeClass();
                        label.addClass("alert alert-danger");
                        label.html("<i class='fas fa-exclamation-circle'></i> Usuario inexistente")
                        alerta = $("#mensaje-sesion");
                        alerta.removeClass("hidden");
                    }
                }
            });

        }
    
       // console.log("Usuario: " + user +"   COntraseña: " + pass );
    
       
    }
    