<?php
 $host = "localhost";
 $user = "root";
 $password = "root";
 $db = "el_rayo";  
$con = mysqli_connect($host, $user, $password, $db);
mysqli_set_charset($con,"utf8");

date_default_timezone_set("America/Matamoros");
$fecha = date("Y-m-d");
$dia_de_la_semana = date("l");

$diaSemana = array(
    'Monday' => 'Lunes',
    'Tuesday' => 'Martes',
    'Wednesday' => 'Miércoles',
    'Thursday' => 'Jueves',
    'Friday' => 'Viernes',
    'Saturday' => 'Sábado',
    'Sunday' => 'Domingo'
);

$meses = array(
    'January' => 'Enero',
    'February' => 'Febrero',
    'March' => 'Marzo',
    'April' => 'Abril',
    'May' => 'Mayo',
    'June' => 'Junio',
    'July' => 'Julio',
    'August' => 'Agosto',
    'September' => 'Septiembre',
    'October' => 'Octubre',
    'November' => 'Noviembre',
    'December' => 'Diciembre'
)

?>
<style>
    .arrow {
        transition: transform 0.3s ease-in-out;
        transform-origin: 4px;
    }
    .arrow.up {
    transform: rotate(3.142rad); /* Rotar 180 grados hacia arriba */
}
</style>
<!-- Topbar -->
<div class="pos-f-t">
  <div class="collapse" id="navbarToggleExternalContent">
    <div class="p-4" style="background-color: #0d0d0d">
    <div class="row">
        <div class="col-11">
            <a class="sidebar-brand d-flex align-items-center justify-content-center ml-5" href="index.php?id=0&nav=inicio">
                <div class="sidebar-brand-icon rotate-n-15">
                    <!-- <i class="fas fa-laugh-wink"></i>--->
                    <img style="filter: invert(100%);" width="40px" src="src/img/racing.svg" />
                </div>
                <div class="sidebar-brand-text mx-3 text-light">El Rayo<sup style="font-size:10px !important; margin-left:5px;">app</sup></div>
            </a>
        </div>
        <div class="col-1">
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-4" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
                 <i class="fas fa-times fa-2xl"></i>
        </button>
        </div>
    </div>
    
    <ul class="list-group text-light mt-3" style="background-color: #0d0d0d !important;">
    <?php if ($user_jerarquia == 1 || $user_jerarquia == 2) { ?>
        <li class="list-group-item" style="background-color: #0d0d0d !important;"><a href="index.php?id=0&nav=inicio">
                <i class="fas fa-fw fa-tachometer-alt" style="color:white"></i>
                <span style="color:white">Inicio</span></a>
            </li>
    <?php } 
    if ($user_jerarquia == 1 || $user_jerarquia == 2 || $user_jerarquia == 3) { ?>
        <li class="list-group-item" style="background-color: #0d0d0d !important;">
            <a href="nueva-venta.php?id=0&nav=nueva_venta">
                <i class="fas fa-fw fa-cart-plus" style="color:white"></i>
                <span style="color:white">Nueva venta</span>
            </a>
        </li>
        <li class="list-group-item" style="background-color: #0d0d0d !important;">
            <a href="nueva-cotizacion.php?id=0&nav=nueva_cotizacion">
                <i class="fas fa-fw fa-clipboard" style="color:white"></i>
                <span style="color:white">Nueva cotización</span>
            </a>
        </li>
        <li class="list-group-item" style="background-color: #0d0d0d !important;">
            <a href="nuevo-pedido.php?id=0&nav=nueva_pedido">
                <i class="fas fa-fw fa-clipboard" style="color:white"></i>
                <span style="color:white">Nuevo pedido</span>
            </a>
        </li>
        <?php if ($user_jerarquia == 1 || $user_jerarquia == 2 || $_SESSION['id_usuario'] ==19) { ?>
        <li class="list-group-item" style="background-color: #0d0d0d !important;">
            <a href="nueva-garantia.php?id=0&nav=nueva_garantia">
                <i class="fas fa-fw fa-tag" style="color:white"></i>
                <span style="color:white">Nueva garantia</span>
            </a>
        </li>
        <?php } }?>
        <li class="list-group-item" style="background-color: #0d0d0d !important;">
            <a class="collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" onclick="arrowTransition('arrow-hist')" aria-expanded="true" aria-controls="collapseUtilities" style="text-decoration: none !important; color: white;">
                <div class="row">
                    <div class="col-11"> <i class="fas fa-fw fa-history"></i> Historial</div>
                    <div class="col-1"><div class="arrow" id="arrow-hist"><i class="fas fa-caret-down"></i></div></div>
                </div>
            </a>
            <div id="collapseUtilities" class="ml-3 collapse <?php echo $showHistorial ?>" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="collapse-inner rounded d-flex flex-column mt-3" style="text-decoration: none !important; color: white;">
                        <h6 class="collapse-header"><b>Ordenes de:</b></h6>
                        <a class="collapse-item <?php echo $claseVentas ?>" href="ventas-pv.php?id=0&nav=ventas">
                            <img src="src/img/ventas.png" width="18px" />
                            <span style="margin-left:12px; color:white"> Ventas</span>
                        </a>
                        <a class="collapse-item mt-2 <?php echo $claseCotizaciones ?> rol-4" href="cotizaciones-lista.php?id=0&nav=cotizaciones">
                            <img src="src/img/cotizaciones.png" width="18px" />
                            <span style="margin-left:12px; color:white"> Cotizaciones</span>
                        </a>
                        <a class="collapse-item mt-2 <?php echo $claseApartados ?>" href="apartados.php?id=0&nav=apartados">
                            <img src="src/img/apartados.png" width="18px" />
                            <span style="margin-left:12px; color:white"> Apartados</span>
                        </a>
                        <a class="collapse-item mt-2 <?php echo $clasePedidos ?>" href="pedidos.php?id=0&nav=pedidos">
                            <img src="src/img/pedidos.png" width="18px" />
                            <span style="margin-left:12px; color:white"> Pedidos</span>
                        </a>
                        <?php

                        if ($user_jerarquia == 1 || $user_jerarquia == 2) { ?>
                        <a class="collapse-item mt-2 <?php echo $claseGarantias ?> rol-4" href="garantias.php?id=0&nav=garantias">
                            <img src="src/img/warranty.png" width="18px" />
                            <span style="margin-left:12px; color:white"> Garantias</span>
                        </a>
                        <?php } ?>
                        <?php

                        if ($user_jerarquia == 1 || $user_jerarquia == 2 || $user_jerarquia == 3) { ?>
                            <a class="collapse-item mt-2 <?php echo $claseGastos ?> rol-4" href="gastos.php?id=0&nav=gastos">
                                <img src="src/img/gastos.png" width="18px" />
                                <span style="margin-left:12px; color:white"> Gastos</span>
                            </a>
                        <?php } ?>
                    </div>
            </div>
        </li>
        <li class="list-group-item" style="background-color: #0d0d0d !important;">
            <a class="collapsed" onclick="arrowTransition('arrow-inv')" href="#" data-toggle="collapse" data-target="#collapseTyres" aria-expanded="true" aria-controls="collapsePages" style="text-decoration: none !important; color: white;">
            <div class="row">
                <div class="col-11"><i class="fas fa-fw fa-folder"></i> Inventario</div>
                <div class="col-1"><div class="arrow" id="arrow-inv"><i class="fas fa-caret-down"></i></div></div></div>
            </a>
            <div id="collapseTyres" class="ml-3 mt-3 collapse <?php echo $showMisLlantas ?>" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                
            <?php
                if ($user_jerarquia == 1 || $user_jerarquia == 4 || $user_jerarquia == 2 || $user_id == 7 || $user_id == 19) { // 7 y 19  es el id de Karina Flores y Javier Pere
                    ?>
                    <div class="py-2 collapse-inner rounded">
                        <h6 class="collapse-header"><b>Sucursales:</b></h6>

                        <?php

                        $querySuc = "SELECT COUNT(*) FROM sucursal";
                        $resp = $con->prepare($querySuc);
                        $resp->execute();
                        $resp->bind_result($total_suc);
                        $resp->fetch();
                        $resp->close();

                        if ($total_suc > 0) {
                            $querySuc = "SELECT * FROM sucursal";
                            $resp = mysqli_query($con, $querySuc);



                            while ($row = $resp->fetch_assoc()) {
                                $suc_identificador = $row['id'];
                                $esta_suc = $_GET["id"];
                                if ($esta_suc == $suc_identificador) {
                                    $class_suc = "active";
                                } else {
                                    $class_suc = "";
                                }
                                $nombre = $row['nombre'];
                                echo '<a class="' . $class_suc . ' mt-2" href="inventario.php?id=' . $suc_identificador . '&nav=inv" style="display:flex; flex-direction: row; justify-content:start; text-decoration: none; color:white;">
                                <i class="fas fa-fw fa-store"></i> 
                                <span style="margin-left:12px;">' . $nombre . '</span></a>';
                            }
                        }

                        ?>


                    </div>

                <?php
                }
                ?>
                <div class="py-2 collapse-inner rounded">
                    <?php
                    if ($user_jerarquia == 1 || $user_jerarquia == 4 || $user_id == 7 || $user_id == 19) {//Usuario de Kari
                    ?>
                        <h6 class="collapse-header"><b>Catalogos:</b></h6>
                        <a class="collapse-item mt-2 <?php echo $claseExistencia ?>" href="inventario-total.php?id=0&nav=existencia" style="display:flex; flex-direction: row; justify-content:start;">
                            <img src="src/img/tyre-invent.svg" width="18px" style="filter: invert(1);" />
                            <span style="margin-left:6px; color:white;"> Existencia</span> </a>
                        <a class="collapse-item mt-2 <?php echo $claseServicios ?>" href="servicios.php?id=0&nav=servicios" style="display:flex; flex-direction: row; justify-content:start; color:white;">
                            <i class="fas fa-car"></i>
                            <span style="margin-left:7px;">Servicios</span> </a>
                    <?php
                    }

                    ?>

                </div>
            </div>
        </li>
        <li class="list-group-item" style="background-color: #0d0d0d !important;">
            <a class="collapsed" href="#" onclick="arrowTransition('arrow-clien')" data-toggle="collapse" data-target="#collapseClients" aria-expanded="true" aria-controls="collapsePages" style="text-decoration: none !important; color: white;">
                <div class="row">
                    <div class="col-11"> <i class="fas fa-fw fa-user-tag"></i><span> Mis clientes</span></div>       
                    <div class="col-1"><div class="arrow" id="arrow-clien"><i class="fas fa-caret-down"></i></div></div>
               </div>
            </a>
            <?php 

            if ($user_jerarquia == 1 || $user_jerarquia == 2) {
            ?>
                    <div id="collapseClients" class="collapse ml-3 <?php echo $showClientes ?>" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                        <div class="py-2 collapse-inner rounded">
                            <h6 class="collapse-header mt-3"><b>Categorias:</b></h6>
                            <a class="collapse-item mt-2 <?php echo $claseClientes ?>" href="clientes.php?id=0&nav=clientes" style="display:flex; flex-direction: row; justify-content:start;">
                                <img src="src/img/cliente.svg" width="18px" style="filter: invert(1);" />
                                <span style="margin-left:12px; color:white;"> Clientes</span> </a>
                            <a class="collapse-item mt-2 <?php echo $claseMovimientosClientes ?>" href="movimientos_clientes.php?id=0&nav=movimientos_creditos" style="display:flex; flex-direction: row; justify-content:start;">
                                <img src="src/img/entrada.svg" width="18px" style="filter: invert(1);" />
                                <span style="margin-left:12px; color:white;"> Movimientos clientes</span> </a>
                            <a class="collapse-item mt-2 <?php echo $claseCreditos ?>" href="creditos.php?id=0&nav=creditos" style="display:flex; flex-direction: row; justify-content:start;">
                                <img src="src/img/credito.svg" width="18px" style="filter: invert(1);" />
                                <span style="margin-left:12px; color:white;"> Creditos</span> </a>
                        </div>
                    </div>

            <?php } ?>
        
        </li>
        <?php 

        if ($user_jerarquia == 1 || $user_jerarquia == 4 && $_SESSION['id_usuario'] !=16 ||$_SESSION['id_usuario'] ==7) { //16 usuario de Javier Lozano y 7 de Kari
        ?>
        <li class="list-group-item" style="background-color: #0d0d0d !important;">
        <a href="generar-token.php?id=0&nav=token">
                <i class="fas fa-fw fa-lock" style="color:white"></i>
                <span style="color:white">Generar token</span></a></li>
        <?php
        }     
        ?>
        <li class="list-group-item" style="background-color: #0d0d0d !important;">
        <a href="cuentas-por-pagar.php?id=0&nav=cuentas-por-pagar">
            <i class="fas fa-fw fa-money-bill" style="color:white"></i>
            <span style="color:white">Cuentas por pagar</span>
        </a></li>
        <li class="list-group-item" style="background-color: #0d0d0d !important;">
            
            <a class="collapsed" href="#"  onclick="arrowTransition('arrow-logi')" data-toggle="collapse" data-target="#collapseLogistica" aria-expanded="true" aria-controls="collapsePages" style="text-decoration: none !important; color: white;">
            <!-- <i class="fas fa-truck"></i> -->
                <div class="row">
                    <div class="col-11"> <i class="fas fa-link"></i><span> Logistica</span></div>       
                    <div class="col-1"><div class="arrow" id="arrow-logi"><i class="fas fa-caret-down"></i></div></div>
               </div>
            </a>
            <div id="collapseLogistica" class="ml-3 collapse <?php echo $showLogistica ?>" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded d-flex flex-column">
                    <h6 class="collapse-header mt-3"><b>Categorias:</b></h6>
                    <a class="collapse-item mt-2<?php echo $claseRequerimientos ?>" href="nuevo-requerimiento.php?id=0&nav=requerimientos" style="display:flex; flex-direction: row; justify-content:start;">
                        <img src="src/img/racing.svg" width="18px" style="filter: invert(1);"/>
                        <span style="margin-left:7px; color:white">Requerir mercancia</span> 
                    </a>
                    <?php
                    if ($user_jerarquia == 1 || $user_jerarquia == 4) {
                    ?>
                        <a class="collapse-item mt-2 <?php echo $claseCambios ?>" href="cambio_inventario.php?id=0&nav=cambios" style="display:flex; flex-direction: row; justify-content:start;">
                            <i class="fas fa-people-carry" style="color:white"></i>
                            <span style="margin-left:7px; color:white;">Mover mercancia</span> </a>
                    <?php
                    }
                    ?>
                    <a class="collapse-item mt-2 <?php echo $claseHistorialRequerimientos ?>" href="requerimientos.php?id=0&nav=historial-requerimientos">
                        <img src="src/img/contract.png" width="18px" style="filter: invert(1);" />
                        <span style="margin-left:0px; color:white;"> Requerimientos</span></a>
                    </a>
                    <a class="collapse-item mt-2 <?php echo $claseMovimientos ?>" href="movimientos.php?id=0&nav=movimientos">
                        <img src="src/img/contract.png" width="18px" style="filter: invert(1);" />
                        <span style="margin-left:0px; color:white;"> Movimientos</span></a>
                    </a>
                    

                </div>
            </div>
        </li>
    </ul>
    </div>
  </div>
 
</div>
<nav class="navbar navbar-expand navbar-info bg-info topbar mb-4 static-top shadow bg-warning">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search -->
    <form class="d-none d-flex d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        

        <div class="input-group"> 
                    <?php
                    $select = "SELECT hora_corte_normal, hora_corte_sabado FROM sucursal where id =?";
                    $r = $con->prepare($select);
                    $r->bind_param('s', $_SESSION['id_sucursal']);
                    $r->execute();
                    $r->bind_result($hora_corte_normal, $hora_corte_sabado);
                    $r->fetch();
                    $r->close();
                    $hora_actual = date("H:i:s");
                    $hora_corte_normal_f = date("g:i A", strtotime($hora_corte_normal));
                    $hora_corte_sabado_f = date("g:i A", strtotime($hora_corte_sabado));
                    if ($dia_de_la_semana !== 'Saturday') {
                        $hora_a_comparar = $hora_corte_normal;
                    } else {
                        $hora_a_comparar = $hora_corte_sabado;
                    }

                    if ($hora_actual < $hora_a_comparar) {
                        $bg_color = '#86fb7f';
                    } else {
                        $bg_color = '#ffe890';
                    }
                    ?>
                    <input onclick="alertaCorte(<?php echo $_SESSION['id_usuario']; ?>)" style="cursor:pointer !important;" type="text" id="hora-actual" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" readonly>
                    <input onclick="alertaCorte(<?php echo $_SESSION['id_usuario']; ?>)" style="background-color:<?php echo $bg_color; ?> !important;; cursor:pointer !important; color: black !important;" type="text" value="Corte: <?php if ($dia_de_la_semana !== 'Saturday') {
                                                                                                                                                                                                    echo $hora_corte_normal_f;                                                                                                                                                                       }  ?>" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" readonly>
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
        <!-- <li class="nav-item no-arrow mx-1">
            <i class="fas fa-question icon-menu"></i>
        </li> -->

        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" style="color:white" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 <i class="fas fa-cart-plus"></i>
                <!-- Counter - Alerts -->
                <span id="contador-items-carrito" class="badge badge-danger badge-counter">0</span>
            </a>

            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header" style="background-color: orangered; border:1px solid red;">
                    Carrito de compra
                </h6>
                <div id="carrito_compra">
                    <div class="empty-notification">
                        <img src="src/img/undraw_Notify_re_65on.svg" alt="" width="400px">
                        <span>Ups, por el momento no hay nada por aqui</span>
                    </div> 
                </div>
               
                <a class="dropdown-item text-center small text-black" href="#">Mostrar mas notificaciones</a>

            </div>
        </li>

        <!-- Nav Item - Alerts -->

        <?php 
            $select_count = 'SELECT COUNT(*) FROM notificaciones_usuarios WHERE id_usuario = ?';
            $stmt = $con->prepare($select_count);
            $stmt->bind_param('s', $_SESSION['id_usuario']);
            $stmt->execute();
            $stmt->bind_result($notifications_count);
            $stmt->fetch();
            $stmt->close();

            $select_count = 'SELECT COUNT(*) FROM notificaciones_usuarios WHERE id_usuario = ? AND estatus_vista =0';
            $stmt = $con->prepare($select_count);
            $stmt->bind_param('s', $_SESSION['id_usuario']);
            $stmt->execute();
            $stmt->bind_result($notifications_vista_count);
            $stmt->fetch();
            $stmt->close();
            
        ?>
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" onclick="abrirCampana(<?php echo $user_id ?>)" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw icon-menu"></i>
                <!-- Counter - Alerts -->
                <?php if($notifications_vista_count>0){?>

                <span id="contador-notificaciones" class="badge badge-danger badge-counter"><?php echo $notifications_vista_count ?></span>
                <?php  }?>
            </a>

            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header" style="background-color: orange; border:1px solid orange;">
                    Notificaciones
                </h6>
                <div id="cuerpo_notificaciones">
                <?php if($notifications_count>0){
                    $select_count = 'SELECT n.*, nu.estatus_abierta, nu.id as nu_id FROM notificaciones_usuarios nu INNER JOIN notificaciones n
                    ON nu.id_notificacion = n.id WHERE nu.id_usuario = ? ORDER BY n.id DESC LIMIT 10';
                    $stmt = $con->prepare($select_count);
                    $stmt->bind_param('s', $_SESSION['id_usuario']);
                    $stmt->execute();
                    $response = Arreglo_Get_Result($stmt);
                    $stmt->close(); 
        
                    foreach ($response as $key => $value) {
                        // Convertir la fecha a un objeto DateTime
                        $fecha_notificacion = $value['fecha'];
                        $dateTime = new DateTime($fecha_notificacion);
                        $id_notificacion = $value['id'];
                        $nu_id = $value['nu_id'];
                        $class_estatus_abierta = $value['estatus_abierta'] == 0 ? 'font-weight-bold' : '';
                        // Formatear la fecha
                        $fechaFormateada = $dateTime->format('l j \d\e F Y');
                        $fechaFormateada = str_replace(array_keys($diaSemana), array_values($diaSemana), $fechaFormateada);
                        $fechaFormateada = str_replace(array_keys($meses), array_values($meses), $fechaFormateada);

                        ?>
                         <a class="dropdown-item d-flex align-items-center" href="./modelo/configuraciones/configuracion_notificaciones/abrir_notificacion.php?visto=1&abierto=1&id_nu=<?= $nu_id;?>&id_notificacion=<?=$id_notificacion?>">
                        <div class="mr-3">
                            <div class="icon-circle bg-danger">
                               <i class="fas fa-clock text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500"><?=  $fechaFormateada . ', ' . $value['hora']; ?></div>
                               <span class="<?= $class_estatus_abierta;?>"><?= $value['mensaje']; ?></span>
                               <?php if($value['estatus_abierta'] ==0){?>
                               <span class="badge badge-lg badge-warning">Nuevo</span>
                               <?php }; ?>
                            </div>
                        </a>
                        <?php
                    }
                    ?>
                   
                <?php }else{ ?>
                    <div class="empty-notification">
                        <img src="src/img/undraw_Notify_re_65on.svg" alt="" width="400px">
                        <span>Ups, por el momento no hay nada por aqui</span>
                    </div> 
                <?php } ?>
                </div>
               
                <a class="dropdown-item text-center small text-black" href="panel_notificaciones.php">Mostrar mas notificaciones</a>

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
                if ($rol == 1 || $rol == 2 || $rol == 4 || $_SESSION['id_usuario'] == 19) { //19 es el usuario de javier
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
<script src="./src/vendor/jquery/jquery.min.js"></script>
<script src="src/js/navbar.js"></script>
<script>
    function showTime() {
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

  /*   $.ajax({
        type: "post",
        url: "./modelo/configuraciones/configuracion_notificaciones/comprobacion_credito_vencido.php",
        data: "data",
        dataType: "json",
        success: function (response) {
        
        }
    }); */

    function abrirCampana(id_usuario){

        $.ajax({
        type: "post",
        url: "./modelo/configuraciones/configuracion_notificaciones/abrir_notificacion.php?visto=all",
        data: {id_usuario},
        dataType: "json",
        success: function (response) {
           if(response.estatus == true){
            $("#contador-notificaciones").remove();
           }
        }
    });
    }
    //Esto simula un web socket
    //const evtSource = new EventSource("./modelo/configuraciones/configuracion_notificaciones/notificaciones.php");

</script>

<?php
function Arreglo_Get_Result( $Statement ) {
    $RESULT = array();
    $Statement->store_result();
    for ( $i = 0; $i < $Statement->num_rows; $i++ ) {
        $Metadata = $Statement->result_metadata();
        $PARAMS = array();
        while ( $Field = $Metadata->fetch_field() ) {
            $PARAMS[] = &$RESULT[ $i ][ $Field->name ];
        }
        call_user_func_array( array( $Statement, 'bind_result' ), $PARAMS );
        $Statement->fetch();
    } 
    return $RESULT;
}
?>
<!-- End of Topbar -->