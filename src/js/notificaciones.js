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


     /*' <h6 class="dropdown-header">
     Notificaciones
  </h6>
  <a class="dropdown-item d-flex align-items-center" href="#">
      <div class="mr-3">
          <div class="icon-circle bg-primary">
              <i class="fas fa-file-alt text-white"></i>
          </div>
      </div>
      <div>
          <div class="small text-gray-500">December 12, 2019</div>
          <span class="font-weight-bold">A new monthly report is ready to download!</span>
      </div>
  </a>
  <a class="dropdown-item d-flex align-items-center" href="#">
      <div class="mr-3">
          <div class="icon-circle bg-success">
              <i class="fas fa-donate text-white"></i>
          </div>
      </div>
      <div>
          <div class="small text-gray-500">December 7, 2019</div>
          $290.29 has been deposited into your account!
      </div>
  </a>
  <a class="dropdown-item d-flex align-items-center" href="#">
      <div class="mr-3">
          <div class="icon-circle bg-warning">
              <i class="fas fa-exclamation-triangle text-white"></i>
          </div>
      </div>
      <div>
          <div class="small text-gray-500">December 2, 2019</div>
          Spending Alert: We've noticed unusually high spending for your account.
      </div>
  </a>
  <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>';  */

     

     function NotificationManager() {
         
        $.ajax({
            type: "POST",
            url: "./modelo/panel/notificaciones-manager.php",
            data: "data",
            dataType: "JSON",
            success: function (response) {
                contenedor = $("#contenedor-alertas");
                noti_alert = $(".empty-notification");
                noti_alert.empty();

                response.forEach(function(value) { 
                    
                    fecha = value.fecha;
                    dia= fecha.substring(0,2);
                    mes = fecha.substring(3,5);
                    año = fecha.substring(6,10);
                    switch (mes) {
                        case '01':
                        mes = 'Enero';
                        break;

                        case '02':
                        mes = 'Febrero';
                        break;

                        case '03':
                        mes = 'Marzo';
                        break;

                        case '04':
                        mes = 'Abril';
                        break;

                        case '05':
                        mes = 'Mayo';
                        break;

                        case '06':
                        mes = 'Junio';
                        break;

                        case '07':
                        mes = 'Julio';
                        break;

                        case '08':
                        mes = 'Agosto';
                        break;

                        case '09':
                        mes = 'Septiembre';
                        break;

                        case '10':
                        mes = 'Octubre';
                        break;

                        case '11':
                        mes = 'Noviembre';
                        break;

                        case '12':
                        mes = 'Diciembre';
                        break;

                    
                        default:
                            break;
                    }
                    fecha_format = mes + " " + dia + ", " + año;

                    function appendNotification() {

                        if (value.state == 1) {     
                            contenedor.prepend('<a idnotify="'+ value.id+'" class="dropdown-item d-flex align-items-center" href="#">'+
                            '<div class="mr-3">'+
                                ' <div class="icon-circle bg-primary">'+
                                     '<i class="fas fa-hourglass-end text-white"></i>'+
                                 '</div>'+
                            ' </div>'+
                             '<div>'+
                                 '<div class="small text-gray-500">'+ fecha_format + ', ' + value.hora +'</div>'+
                               '  <span class="font-weight-bold">'+ value.descripcion +'</span>'+
                               
                           '  </div>'+
                         '</a>'); }else if(value.state ==2){
                            
                                contenedor.prepend('<a class="dropdown-item d-flex align-items-center" href="#">'+
                                '<div class="mr-3">'+
                                    ' <div class="icon-circle bg-primary">'+
                                         '<i class="fas fa-hourglass-end text-white"></i>'+
                                     '</div>'+
                                ' </div>'+
                                 '<div>'+
                                     '<div class="small text-gray-500">'+ fecha_format+ ', ' + value.hora +'</div>'+
                                   '  <span class="">'+ value.descripcion +'</span>'+
                                   
                               '  </div>'+
                             '</a>');
                            
                        }

                      }

                   
                   

                    var foo = document.getElementById("contenedor-alertas");

                    if (foo.hasChildNodes()) {
                       // console.log("Si notificaciones en la entrada");
                       flag = 0;
                        $("#contenedor-alertas a").each(function(){
                            
                            idnotify = $(this).attr('idnotify');

                            if(idnotify !== value.id){
                               // appendNotification();
                              // console.log("Es diferente id");
                               
                            }else{
                               // console.log("Es el mismo id");
                                flag = 1;
                            }
                        });

                        if(flag == 0){
                            console.log(flag);
                            appendNotification();
                        }
                      
                    }else{
                       // console.log("No notificaciones en la entrada");
                        appendNotification();
                    } 
                   
               

                 }) 

            }
        });

        $.ajax({
            type: "POST",
            url: "./modelo/panel/contador-notificaciones.php",
            data: "data",
           
            success: function (response) {
                $("#contador-notifi").text(response);
            }
        });

      }
 
      notificarCreditosVencidos();
      NotificationManager();
      setInterval(NotificationManager,3000);

 })

