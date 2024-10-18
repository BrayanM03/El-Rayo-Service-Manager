
    function iniciarSesion(){

        $("#iniciar-sesion").empty().append(`
        <i class="fa-solid fa-spinner fa-spin"></i> Iniciando sesión
        `);

        event.preventDefault();

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
                        label.addClass('')
                        setTimeout(function(){
                            $("#iniciar-sesion").empty().append(`
                            Iniciar sesión
                            `);
                        },1000)
                        
        }else{
 
            loginform = $("#login-form");
            datos = loginform.serialize();
            $.ajax({
                type: "POST",
                url: "./modelo/login/iniciar-sesion.php",
                data: datos,
               
                success: function (response) {
                    if(response.estatus){
                        insertarMensaje(response)
                        window.location = "./index.php?id=0&nav=inicio";
                    }else{
                        insertarMensaje(response)
                    }
                   
                }
            });

        }
    }


 
    $("#user").keypress(function(e) {
        //no recuerdo la fuente pero lo recomiendan para
        //mayor compatibilidad entre navegadores.
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){
            iniciarSesion();
        }
    });

      $("#password").keypress(function(e) {
        //no recuerdo la fuente pero lo recomiendan para
        //mayor compatibilidad entre navegadores.
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){
            iniciarSesion();
        }
    });

function insertarMensaje(response){
    console.log(response);
    alerta = $("#alerta");
        alerta.removeClass("mensaje-oculto");
        alerta.addClass("mensaje-sesion");
        label = $("#label-alert");
        label.removeClass();
    if(response.tipo=='warning'){
        label.addClass("alert alert-warning");
    }else if(response.tipo=='danger'){
        label.addClass("alert alert-danger");
    }
    else if(response.tipo == 'success'){
        label.addClass("alert alert-success");
    }

    label.html(`<i class='fas fa-exclamation-triangle'></i> ${response.mensaje}`);
    label.addClass('')
    
    setTimeout(function(){
        $("#iniciar-sesion").empty().append(`
        Iniciar sesión
        `);
    },1000)
}