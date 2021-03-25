
    function iniciarSesion(){
    
        user = $("#user").val();
        pass = $("#password").val();

        if(pass == "" || user == ""){
            alert("No hay contraseña");
        }else{

            $.ajax({
                type: "POST",
                url: "./modelo/login/iniciar-sesion.php",
                data: {"user": user, "pass": pass},
                dataType: "dataType",
                success: function (response) {
                    if(response == 0){
                        alert("Contraseña incorrecta");
                    }else if(response == 1){
                        alert("login correcto");
                    }
                }
            });

        }
    
       // console.log("Usuario: " + user +"   COntraseña: " + pass );
    
       
    }
    