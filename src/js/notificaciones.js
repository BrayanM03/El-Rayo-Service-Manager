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
                type: "method",
                url: "url",
                data: "data",
                dataType: "dataType",
                success: function (response) {
                    
                }
            });

            var notificacion = new Notification("Credito vencido",
            {
                icon: "./src/img/logo.jpg",
                body: "Se a vencido el credito del cliente Daniel Perez"
            });

            notificacion.onclick = function() { 
                window.open("http://localhost/el-rayo-service-manager/creditos.php");
             }

        }   
     }

     notificarCreditosVencidos();

 })

