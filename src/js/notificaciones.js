document.addEventListener("DOMContentLoaded", function() { 

    if (!Notification) {
        alert("Las notificaciones no son soportadas en este navegador");
        return;
    }

    if (Notification.permission !== 'granted') {
        Notification.requestPermission();
    }

    function notificarCreditosVencidos() { 
        if (Notification.permission !== 'granted') {
            Notification.requestPermission();
        }else{

            
            $.ajax({
                type: "POST",
                url: "./modelo/creditos/comprobar-creditos-vencidos.php",
                data: "data",
                dataType: "json",
                success: function (response) {
                    

                    response.forEach(function(value) { 
                       
                      

                        if (value.state == 1) {
                            var notificacion = new Notification("Credito vencido",
                        {
                            icon: "./src/img/logo.jpg",
                            body: "Se a vencido el credito del cliente " + value.cliente
                        });
            
                        notificacion.onclick = function() { 
                            window.open("http://localhost/el-rayo-service-manager/creditos.php");
                         }
                        }

                     })

                    
                    
                }
            });

           

        }   
     }

     notificarCreditosVencidos();

 })

