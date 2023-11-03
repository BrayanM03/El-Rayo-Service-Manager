<?php


date_default_timezone_set("America/Matamoros");
$fecha = date("Y-m-d"); 
$dia_de_la_semana = date("l");

?>
<!-- Topbar -->
<nav class="navbar navbar-expand navbar-info bg-info topbar mb-4 static-top shadow bg-warning">

<!-- Sidebar Toggle (Topbar) -->
<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
    <i class="fa fa-bars"></i>
</button>

<!-- Topbar Search -->
<form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
    <div class="input-group">
        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
        <div class="input-group-append">
            <button class="btn btn-primary" type="button">
                <i class="fas fa-search fa-sm"></i>
            </button>
        </div>
    </div>
</form>

<!-- Topbar Navbar -->
<ul class="navbar-nav ml-auto">

    <!-- Nav Item - Search Dropdown (Visible Only XS) -->
    <li class="nav-item dropdown no-arrow d-sm-none">
        <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-search fa-fw"></i>
        </a>
        <!-- Dropdown - Messages -->
        <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
            <form class="form-inline mr-auto w-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </li>

    <div class="mt-3">
           <form class="form-inline navbar-search">
                <div class="input-group">
                    <?php 
                    $select = "SELECT hora_corte_normal, hora_corte_sabado FROM sucursal where id =?";
                    $r=$con->prepare($select);
                    $r->bind_param('s',$_SESSION['id_sucursal']);
                    $r->execute();
                    $r->bind_result($hora_corte_normal, $hora_corte_sabado);
                    $r->fetch();
                    $r->close();
                    $hora_actual = date("H:i:s");
                    $hora_corte_normal_f = date("g:i A", strtotime($hora_corte_normal));
                    $hora_corte_sabado_f = date("g:i A", strtotime($hora_corte_sabado));
                    if($dia_de_la_semana !=='Saturday'){ $hora_a_comparar = $hora_corte_normal; }else{ $hora_a_comparar = $hora_corte_sabado;}
                    
                    if($hora_actual < $hora_a_comparar){
                        $bg_color = '#86fb7f';
                    }else{
                        $bg_color = '#ffe890';
                    }
                    ?>
                    <input onclick="alertaCorte()" style="cursor:pointer !important;" type="text" id="hora-actual" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" readonly>
                    <input onclick="alertaCorte()" style="background-color:<?php echo $bg_color; ?> !important;; cursor:pointer !important; color: black !important;" type="text" value="Corte: <?php if($dia_de_la_semana !=='Saturday'){ echo $hora_corte_normal_f; }else{ echo $hora_corte_sabado_f;}  ?>" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" readonly>
                </div>
            </form>
    </div>

    <!-- Nav Item - Alerts -->
    <li class="nav-item dropdown no-arrow mx-1">
       

        <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-bell fa-fw icon-menu"></i>
            <!-- Counter - Alerts -->
            <span id="contador-notifi" class="badge badge-danger badge-counter">0</span>
        </a>

        <!-- Dropdown - Alerts -->
        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
            <h6 class="dropdown-header">
                Notificaciones
            </h6>
            <div class="empty-notification">
                <img src="src/img/undraw_Notify_re_65on.svg" alt="" width="400px">
                <span>Ups, por el momento no hay nada por aqui</span>
            </div>

            <div id="contenedor-alertas"></div>
            <a class="dropdown-item text-center small text-black" href="#">Mostrar mas notificaciones</a>

        </div>
    </li>





    <div class="topbar-divider d-none d-sm-block"></div>

    <!-- Nav Item - User Information -->
    <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="mr-2 d-none d-lg-inline small" style="color:black !important;"><?php

                                                                                    echo $_SESSION['nombre'] . " " . $_SESSION['apellidos'];

                                                                                    ?></span>
            <img class="img-profile rounded-circle" src="src/img/undraw_profile.svg">
        </a>
        <!-- Dropdown - User Information -->
        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="#">
                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                Perfil
            </a>
            <a class="dropdown-item" href="configuraciones.php?id=0&nav=configuraciones">
                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                Configuraciones
            </a>
            <?php 
               $rol = $_SESSION['rol'];
               if($rol == 1 || $rol == 2 || $rol == 4 || $_SESSION['id_usuario'] == 19){ //19 es el usuario de javier
            ?>
            <a class="dropdown-item" href="mercancia_pendiente.php?nav=0&id=<?php echo $_SESSION['id_sucursal'] ?>">
                <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                Llantas pendientes
            </a>
            <?php 
               }
            ?>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                Salir
            </a>
        </div>
    </li>

</ul>

</nav>

<script src="src/js/navbar.js"></script>
<script>
    function showTime(){
    var myDate = new Date();
    var hours = myDate.getHours();
    var minutes = myDate.getMinutes();
    var seconds = myDate.getSeconds();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    
  // Convierte las horas a formato de 12 horas
  hours = hours % 12;
  hours = hours ? hours : 12; // "0" se muestra como "12"

  if (hours < 10) hours = "0" + hours;
  if (minutes < 10) minutes = "0" + minutes;
  if (seconds < 10) seconds = "0" + seconds;

  var currentTime = hours + ":" + minutes + ":" + seconds + " " + ampm;
  document.getElementById('hora-actual').value = currentTime;
  setTimeout(showTime, 1000);

}
showTime()

</script>
<!-- End of Topbar -->