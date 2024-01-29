<!-- Sidebar -->
<?php
$flag = isset($_GET['nav']) ? $_GET['nav'] : 0;
$claseInicio = "";
$claseNuevaVenta = "";
$claseNuevaCotizacion = "";
$claseNuevaPedido = "";
$claseHistorial = "";
$showHistorial = "";
$claseVentas = "";
$claseCotizaciones = "";
$claseGastos = "";
$claseCustomer = "";
$showClientes = "";
$claseClientes = "";
$claseCreditos = "";
$claseToken = "";
$showMisLlantas = "";
$claseMisLlantas = "";
$claseExistencia = "";
$claseApartados = '';
$claseServicios = "";
$claseGarantias = '';
$claseMovimientos = '';
$showLogistica='';
$claseRequerimientos = '';
$claseHistorialRequerimientos ='';
$claseMovimientosClientes = "";
$user_jerarquia = $_SESSION["rol"];



switch ($flag) {
    case 'inicio':
        $claseInicio = "active";
        break;
    case 'nueva_venta':
        $claseNuevaVenta = "active";
        break;
    case 'nueva_cotizacion':
        $claseNuevaCotizacion = "active";
        break;
    case 'nueva_pedido':
        $claseNuevaPedido = "active";
        break;
    case 'nueva_garantia':
        $claseNuevaGarantia = "active";
        break;
    case 'ventas':
        $claseHistorial = "active";
        $claseVentas = "active";
        $showHistorial = "show";
        break;
    case 'cotizaciones':
        $claseHistorial = "active";
        $claseCotizaciones = "active";
        $showHistorial = "show";
        break;
    case 'apartados':
        $claseHistorial = "active";
        $claseApartados = "active";
        $showHistorial = "show";
        break;
    case 'pedidos':
        $claseHistorial = "active";
        $clasePedidos = "active";
        $showHistorial = "show";
        break;
    case 'garantias':
        $claseHistorial = "active";
        $clasePedidos = "active";
        $showHistorial = "show";
        break;
    case "inv":
        $showMisLlantas = "show";
        $claseMisLlantas = "active";
        break;
    case 'existencia':
        $claseExistencia = "active";
        $showMisLlantas = "show";
        break;
    case 'servicios':
        $claseServicios = "active";
        $showMisLlantas = "show";
        break;
    case 'movimientos':
        $claseMovimientos = "active";
        $showMisLlantas = "";
        $showLogistica ="show";

        break;
    case 'cambios':
        $claseCambios = "active";
        $showMisLlantas = "";
        $showLogistica ="show";
        break;

    case 'clientes':
        $claseCustomer = "active";
        $claseClientes = "active";
        $showClientes = "show";
        break;
    case 'creditos':
        $claseCustomer = "active";
        $claseCreditos = "active";
        $showClientes = "show";
        break;
    case 'token':
        $claseToken = "active";
        break;
    case 'cuentas-por-pagar':
        $claseCuentasPorPagar = 'active';
        break;
    case 'requerimientos':
        $claseRequerimientos = 'active';
        $showLogistica = 'show';
        break;
    case 'historial-requerimientos':
        $claseHistorialRequerimientos = 'active';
        $showLogistica = 'show';

        break;
    case 'proveedores':
        $claseProveedores = 'active';
        break;    
    default:
        $clase = "";
        break;
}
?>
<ul class="navbar-nav sidebar sidebar-dark accordion" style="background-color:#0d0d0d" id="accordionSidebar"><!-- bg-gradient-dark  -->

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php?id=0&nav=inicio">
        <div class="sidebar-brand-icon rotate-n-15">
            <!-- <i class="fas fa-laugh-wink"></i>--->
            <img style="filter: invert(100%);" width="40px" src="src/img/racing.svg" />
        </div>
        <div class="sidebar-brand-text mx-3" id="emp-title" sesion_sucursal_id="<?php echo $_SESSION["id_sucursal"]; ?>" sesion_id="<?php echo $_SESSION["id_usuario"]; ?>" sesion_rol="<?php echo $_SESSION["rol"]; ?>">El Rayo<sup style="font-size:6px !important; margin-left:5px;">app</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <?php if ($user_jerarquia == 1 || $user_jerarquia == 2) { ?>
        <li class="nav-item <?php echo $claseInicio; ?> rol-4">
            <a class="nav-link" href="index.php?id=0&nav=inicio">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Inicio</span></a>
        </li>
    <?php } ?>
    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading rol-4">
        Punto de venta
    </div>
    <?php
    if ($user_jerarquia == 1 || $user_jerarquia == 2 || $user_jerarquia == 3) { ?>
        <!-- Nav Item - Pages Collapse Menu -->
        <li class="nav-item <?php echo $claseNuevaVenta ?> rol-4">
            <a class="nav-link" href="nueva-venta.php?id=0&nav=nueva_venta">
                <i class="fas fa-fw fa-cart-plus"></i>
                <span>Nueva venta</span>
            </a>
        </li>

        <li class="nav-item <?php echo $claseNuevaCotizacion ?> rol-4">
            <a class="nav-link" href="nueva-cotizacion.php?id=0&nav=nueva_cotizacion">
                <i class="fas fa-fw fa-clipboard"></i>
                <span>Nueva cotización</span>
            </a>
        </li>

        <li class="nav-item <?php echo $claseNuevaPedido ?> rol-4">
            <a class="nav-link" href="nuevo-pedido.php?id=0&nav=nueva_pedido">
                <i class="fas fa-fw fa-clipboard"></i>
                <span>Nuevo pedido</span>
            </a>
        </li>
        <?php if ($user_jerarquia == 1 || $user_jerarquia == 2 || $_SESSION['id_usuario'] ==19) { ?>

        <li class="nav-item <?php echo $claseNuevaGarantia ?> rol-4">
            <a class="nav-link" href="nueva-garantia.php?id=0&nav=nueva_garantia">
                <i class="fas fa-fw fa-tag"></i>
                <span>Nueva garantia</span>
            </a>
        </li>
    <?php } ?>

    <?php } ?>
    <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item <?php echo $claseHistorial ?>">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-history"></i>
            <span>Historial</span>
        </a>
        <div id="collapseUtilities" class="collapse <?php echo $showHistorial ?>" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Ordenes de:</h6>
                <a class="collapse-item <?php echo $claseVentas ?>" href="ventas-pv.php?id=0&nav=ventas">
                    <img src="src/img/ventas.png" width="18px" />
                    <span style="margin-left:12px;"> Ventas</span>
                </a>
                <a class="collapse-item <?php echo $claseCotizaciones ?> rol-4" href="cotizaciones-lista.php?id=0&nav=cotizaciones">
                    <img src="src/img/cotizaciones.png" width="18px" />
                    <span style="margin-left:12px;"> Cotizaciones</span>
                </a>
                <a class="collapse-item <?php echo $claseApartados ?>" href="apartados.php?id=0&nav=apartados">
                    <img src="src/img/apartados.png" width="18px" />
                    <span style="margin-left:12px;"> Apartados</span>
                </a>
                <a class="collapse-item <?php echo $clasePedidos ?>" href="pedidos.php?id=0&nav=pedidos">
                    <img src="src/img/pedidos.png" width="18px" />
                    <span style="margin-left:12px;"> Pedidos</span>
                </a>
                <?php

                if ($user_jerarquia == 1 || $user_jerarquia == 2) { ?>
                <a class="collapse-item <?php echo $claseGarantias ?> rol-4" href="garantias.php?id=0&nav=garantias">
                    <img src="src/img/warranty.png" width="18px" />
                    <span style="margin-left:12px;"> Garantias</span>
                </a>
                <?php } ?>
                <?php

                if ($user_jerarquia == 1 || $user_jerarquia == 2) { ?>
                    <a class="collapse-item <?php echo $claseGastos ?> rol-4" href="gastos.php?id=0&nav=gastos">
                        <img src="src/img/gastos.png" width="18px" />
                        <span style="margin-left:12px;"> Gastos</span>
                    </a>
                <?php } ?>
            </div>
        </div>
    </li>


    <?php


    if ($user_jerarquia == 1) {
        $name = "Inventario";
    } else if ($user_jerarquia == 2) {
        $name = "Clientes y creditos";
    } else {
        $name = "";
    }

    ?>

    <!-- Divider -->
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        <?php echo $name ?>
    </div>
    <?php

    if ($user_jerarquia == 1 || $user_jerarquia == 4 || $user_jerarquia == 2) {
        # code...


    ?>


        <!-- Nav Item - Pages Collapse Menu -->
        <li class="nav-item <?php echo $claseMisLlantas ?>">


            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTyres" aria-expanded="true" aria-controls="collapsePages">
                <i class="fas fa-fw fa-folder"></i>
                <span>Inventarios</span>
            </a>
            <div id="collapseTyres" class="collapse <?php echo $showMisLlantas ?>" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                
            <?php
                if ($user_jerarquia == 1 || $user_jerarquia == 4 || $_SESSION['id_usuario'] == 7) { // 7 es el id de Karina Flores
                    ?>
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Sucursales:</h6>

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
                                echo '<a class="collapse-item ' . $class_suc . '" href="inventario.php?id=' . $suc_identificador . '&nav=inv" style="display:flex; flex-direction: row; justify-content:start;">
                        <i class="fas fa-fw fa-store"></i> 
                            <span style="margin-left:12px;">' . $nombre . '</span></a>';
                            }
                        }

                        ?>


                    </div>

                <?php
                }
                ?>
                <div class="bg-white py-2 collapse-inner rounded">
                    <?php
                    if ($user_jerarquia == 1 || $user_jerarquia == 4 || $_SESSION['id_usuario'] == 7) {//Usuario de Kari
                    ?>
                        <h6 class="collapse-header">Catalogos:</h6>
                        <a class="collapse-item <?php echo $claseExistencia ?>" href="inventario-total.php?id=0&nav=existencia" style="display:flex; flex-direction: row; justify-content:start;">
                            <img src="src/img/tyre-invent.svg" width="18px" />
                            <span style="margin-left:6px;"> Existencia</span> </a>
                        <a class="collapse-item <?php echo $claseServicios ?>" href="servicios.php?id=0&nav=servicios" style="display:flex; flex-direction: row; justify-content:start;">
                            <i class="fas fa-car"></i>
                            <span style="margin-left:7px;">Servicios</span> </a>
                    <?php
                    }

                    ?>

                </div>
            </div>
        </li>


    <?php }

    if ($user_jerarquia == 1 || $user_jerarquia == 2) {
    ?>


        <li class="nav-item <?php echo $claseCustomer ?> rol-4">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseClients" aria-expanded="true" aria-controls="collapsePages">
                <i class="fas fa-fw fa-user-tag"></i>
                <span>Mis clientes</span>
            </a>
            <div id="collapseClients" class="collapse <?php echo $showClientes ?>" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Categorias:</h6>
                    <a class="collapse-item <?php echo $claseClientes ?>" href="clientes.php?id=0&nav=clientes" style="display:flex; flex-direction: row; justify-content:start;">
                        <img src="src/img/cliente.svg" width="18px" />
                        <span style="margin-left:12px;"> Clientes</span> </a>
                    <a class="collapse-item <?php echo $claseMovimientosClientes ?>" href="movimientos_clientes.php?id=0&nav=movimientos_creditos" style="display:flex; flex-direction: row; justify-content:start;">
                        <img src="src/img/entrada.svg" width="18px" />
                        <span style="margin-left:12px;"> Movimientos clientes</span> </a>
                    <a class="collapse-item <?php echo $claseCreditos ?>" href="creditos.php?id=0&nav=creditos" style="display:flex; flex-direction: row; justify-content:start;">
                        <img src="src/img/credito.svg" width="18px" />
                        <span style="margin-left:12px;"> Creditos</span> </a>
                    <!--  <a class="collapse-item" href="forgot-password.html">
                <img src="src/img/pago.svg" width="18px" /> 
                <span style="margin-left:12px;"> Creditos vencidos</span></a> 
            </a> -->

                </div>
            </div>
        </li>


        <!---  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProvider"
        aria-expanded="true" aria-controls="collapsePages">
        <i class="fas fa-fw fa-user-cog"></i>
        <span>Mis provedores</span>
    </a>
    <div id="collapseProvider" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Categorias:</h6>
            <a class="collapse-item" href="clientes.php" style="display:flex; flex-direction: row; justify-content:start;">
                <img src="src/img/tyre-invent.svg" width="18px" /> 
                <span style="margin-left:12px;"> Clientes</span> </a>
            <a class="collapse-item" href="creditos.php" style="display:flex; flex-direction: row; justify-content:start;">
                <img src="src/img/salida.svg" width="18px" /> 
                <span style="margin-left:12px;"> Creditos</span> </a>
            <a class="collapse-item" href="forgot-password.html">
                <img src="src/img/entrada.svg" width="18px" /> 
                <span style="margin-left:12px;"> Creditos vencidos</span></a>
            </a>
        
        </div>
    </div>
</li>  -->

    <?php }

    if ($user_jerarquia == 1 || $user_jerarquia == 4 && $_SESSION['id_usuario'] !=16 ||$_SESSION['id_usuario'] ==7) { //16 usuario de Javier Lozano y 7 de Kari
    ?>
        <!-- Nav Item - Charts -->
        <li class="nav-item <?php echo $claseToken; //rol-4 
                            ?>">
            <a class="nav-link" href="generar-token.php?id=0&nav=token">
                <i class="fas fa-fw fa-lock"></i>
                <span>Generar token</span></a>
        </li>

    <?php
    }     
    ?>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
    <div class="sidebar-heading rol-4">
        Administración
    </div>
    <?php 

if ($user_jerarquia == 1 || $_SESSION['id_usuario'] ==7) { // 7 de Kari
?>
    

    <li class="nav-item <?php echo $claseCuentasPorPagar; ?>">
        <a class="nav-link" href="cuentas-por-pagar.php?id=0&nav=cuentas-por-pagar">
            <i class="fas fa-fw fa-money-bill"></i>
            <span>Cuentas por pagar</span>
        </a>
    </li>

   <!--  <li class="nav-item <?php echo $claseProveedores; ?>">
        <a class="nav-link" href="cuentas-por-pagar.php?id=0&nav=proveedores">
            <i class="fas fa-fw fa-truck"></i>
            <span>Proveedores</span>
        </a>
    </li> -->
    <?php } 
    
?>
    <li class="nav-item <?php echo $claseMovimientos ?> rol-4">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLogistica" aria-expanded="true" aria-controls="collapsePages">
            <!-- <i class="fas fa-truck"></i> -->
            <i class="fas fa-link"></i>
                <span>Logistica</span>
            </a>
            <div id="collapseLogistica" class="collapse <?php echo $showLogistica ?>" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Categorias:</h6>
                    <a class="collapse-item <?php echo $claseRequerimientos ?>" href="nuevo-requerimiento.php?id=0&nav=requerimientos" style="display:flex; flex-direction: row; justify-content:start;">
                    <img src="src/img/racing.svg" width="18px" />
                            <span style="margin-left:7px;">Requerir mercancia</span> 
                    </a>
                    <?php
                    if ($user_jerarquia == 1 || $user_jerarquia == 4) {
                    ?>
                        <a class="collapse-item <?php echo $claseCambios ?>" href="cambio_inventario.php?id=0&nav=cambios" style="display:flex; flex-direction: row; justify-content:start;">
                            <i class="fas fa-people-carry"></i>
                            <span style="margin-left:7px;">Mover mercancia</span> </a>
                    <?php
                    }
                    ?>
                    <a class="collapse-item <?php echo $claseHistorialRequerimientos ?>" href="requerimientos.php?id=0&nav=historial-requerimientos">
                        <img src="src/img/contract.png" width="18px" />
                        <span style="margin-left:0px;"> Requerimientos</span></a>
                    </a>
                    <a class="collapse-item <?php echo $claseMovimientos ?>" href="movimientos.php?id=0&nav=movimientos">
                        <img src="src/img/contract.png" width="18px" />
                        <span style="margin-left:0px;"> Movimientos</span></a>
                    </a>
                    

                </div>
            </div>
        </li>
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline mt-3">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <!-- Sidebar Message -->
    <div class="sidebar-card">
        <img class="sidebar-card-illustration mb-2" src="src/img/logo.jpg" alt="" style="border-radius: 8px;">
        <p class="text-center mb-2"><strong>El Rayo Service Manager</strong><br> Es un sistema de ERP.</p>
        <!--     <a class="btn btn-success btn-sm" href="https://startbootstrap.com/theme/sb-admin-pro">Ir a sitio web!</a>
 -->
    </div>

</ul>
<!-- End of Sidebar -->