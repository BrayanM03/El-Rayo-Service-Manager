<!-- Sidebar -->
<?php
$flag = $_GET['nav'];
$claseInicio = "";
$claseNuevaVenta = "";
$claseNuevaCotizacion = "";
$claseHistorial = "";
$showHistorial = "";
$claseVentas = "";
$claseCotizaciones = "";
$claseCustomer = "";
$showClientes = "";
$claseClientes = "";
$claseCreditos = "";
$claseToken = "";
$showMisLlantas = "";
$claseMisLlantas = "";
$claseExistencia = "";
$claseServicios = "";
$claseMovimientos = "";



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
        $showMisLlantas = "show";
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
    default:
    $clase = "";
        break;
}
?>
<ul class="navbar-nav bg-gradient-dark sidebar sidebar-dark accordion" id="accordionSidebar">

<!-- Sidebar - Brand -->
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php?id=0&nav=inicio">
    <div class="sidebar-brand-icon rotate-n-15">
        <!-- <i class="fas fa-laugh-wink"></i>--->
        <img style="filter: invert(100%);" width="40px" src="src/img/racing.svg"/>
    </div>
    <div class="sidebar-brand-text mx-3">El Rayo<sup style="font-size:12px; margin-left:5px;">app</sup></div>
</a>

<!-- Divider -->
<hr class="sidebar-divider my-0">

<!-- Nav Item - Dashboard -->
<li class="nav-item <?php echo $claseInicio;?>">
    <a class="nav-link" href="index.php?id=0&nav=inicio">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Inicio</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Punto de venta
</div>

<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item <?php echo $claseNuevaVenta ?>">
    <a class="nav-link" href="nueva-venta.php?id=0&nav=nueva_venta">
        <i class="fas fa-fw fa-cart-plus"></i>
        <span>Nueva venta</span>
    </a>
</li>

<li class="nav-item <?php echo $claseNuevaCotizacion ?>">
    <a class="nav-link" href="cotizacion.php?id=0&nav=nueva_cotizacion">
        <i class="fas fa-fw fa-clipboard"></i>
        <span>Nueva cotización</span>
    </a>
</li>

<!-- Nav Item - Utilities Collapse Menu -->
<li class="nav-item <?php echo $claseHistorial ?>">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
        aria-expanded="true" aria-controls="collapseUtilities">
        <i class="fas fa-fw fa-history"></i>
        <span>Historial</span>
    </a>
    <div id="collapseUtilities" class="collapse <?php echo $showHistorial ?>" aria-labelledby="headingUtilities"
        data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Ordenes de:</h6>
            <a class="collapse-item <?php echo $claseVentas ?>" href="ventas-pv.php?id=0&nav=ventas">
                <img src="src/img/ventas.svg" width="18px" /> 
                <span style="margin-left:12px;"> Ventas</span>
            </a>
            <a class="collapse-item <?php echo $claseCotizaciones ?>" href="cotizaciones-lista.php?id=0&nav=cotizaciones">
                <img src="src/img/compras.svg" width="18px" /> 
                <span style="margin-left:12px;"> Cotizaciones</span>
            </a>
        </div>
    </div>
</li>


<?php 
    $user_jerarquia = $_SESSION["rol"];

    if ($user_jerarquia == 1) {
       $name = "Inventario";
    }else if($user_jerarquia ==2){
     $name = "Clientes y creditos";
    }else{
        $name="";
    }

?>

<!-- Divider -->
<hr class="sidebar-divider">
<div class="sidebar-heading">
<?php echo $name ?>
</div>
<?php 

    if ($user_jerarquia == 1) {
        # code...
    

?>


<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item <?php echo $claseMisLlantas?>">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTyres"
        aria-expanded="true" aria-controls="collapsePages">
        <i class="fas fa-fw fa-folder"></i>
        <span>Mis llantas</span>
    </a>
    <div id="collapseTyres" class="collapse <?php echo $showMisLlantas?>" aria-labelledby="headingPages" data-parent="#accordionSidebar">
    <div class="bg-white py-2 collapse-inner rounded">
    
            <?php
            
                $querySuc = "SELECT COUNT(*) FROM sucursal";
                $resp=$con->prepare($querySuc);
                $resp->execute();
                $resp->bind_result($total_suc);
                $resp->fetch();
                $resp->close();

                if($total_suc>0){
                    $querySuc = "SELECT * FROM sucursal";
                    $resp = mysqli_query($con, $querySuc);

                    

                    while ($row = $resp->fetch_assoc()){
                        $suc_identificador = $row['id'];
                        $esta_suc = $_GET["id"];
                       if($esta_suc == $suc_identificador){
                        $class_suc = "active";
                         }else{
                        $class_suc = "";
                        
                        }
                        $nombre = $row['nombre'];
                        echo '<a class="collapse-item '.$class_suc .'" href="inventario.php?id='. $suc_identificador .'&nav=inv" style="display:flex; flex-direction: row; justify-content:start;">
                        <i class="fas fa-fw fa-store"></i> 
                            <span style="margin-left:12px;">'.$nombre.'</span></a>';
                        }
                }
            
            ?>

    
            <!-- <h6 class="collapse-header">Sucursales:</h6>
            <a class="collapse-item" href="inventario-pedro.php" style="display:flex; flex-direction: row; justify-content:start;">
            <i class="fas fa-fw fa-store"></i> 
                <span style="margin-left:12px;"> Pedro Cardenas</span> </a>
            <a class="collapse-item" href="inventario-sendero.php" style="display:flex; flex-direction: row; justify-content:start;">
            <i class="fas fa-fw fa-store"></i>
                <span style="margin-left:12px;"> Sendero</span> </a> -->

        
        </div>
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Stock total:</h6>
            <a class="collapse-item <?php echo $claseExistencia ?>" href="inventario-total.php?id=0&nav=existencia" style="display:flex; flex-direction: row; justify-content:start;">
                <img src="src/img/tyre-invent.svg" width="18px" /> 
                <span style="margin-left:12px;"> Existencia</span> </a>
                <a class="collapse-item <?php echo $claseServicios ?>" href="servicios.php?id=0&nav=servicios" style="display:flex; flex-direction: row; justify-content:start;">
                <i class="fas fa-car"></i>
                <span style="margin-left:7px;">Servicios</span> </a>
            <a class="collapse-item <?php echo $claseMovimientos ?>" href="movimientos.php?id=0&nav=movimientos">
            <img src="src/img/entrada.svg" width="18px" /> 
                <span style="margin-left:12px;"> Movimientos</span></a>
            </a>
        
        </div>
    </div>
</li>


<?php }

if ($user_jerarquia == 1 || $user_jerarquia == 2) {
?>


<li class="nav-item <?php echo $claseCustomer ?>">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseClients"
        aria-expanded="true" aria-controls="collapsePages">
        <i class="fas fa-fw fa-user-tag"></i>
        <span>Mis clientes</span>
    </a>
    <div id="collapseClients" class="collapse <?php echo $showClientes ?>" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Categorias:</h6>
            <a class="collapse-item <?php echo $claseClientes ?>" href="clientes.php?id=0&nav=clientes" style="display:flex; flex-direction: row; justify-content:start;">
                <img src="src/img/cliente.svg" width="18px" /> 
                <span style="margin-left:12px;"> Clientes</span> </a>
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

if ($user_jerarquia == 1 ) {
?>
<!-- Nav Item - Charts -->
<li class="nav-item <?php echo $claseToken ?>">
    <a class="nav-link" href="generar-token.php?id=0&nav=token">
        <i class="fas fa-fw fa-lock"></i>
        <span>Generar token</span></a>
</li>


<?php 
  }     # code...
    

?>

<!-- Divider -->
<hr class="sidebar-divider d-none d-md-block">

<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>

<!-- Sidebar Message --> 
<div class="sidebar-card">
    <img class="sidebar-card-illustration mb-2" src="src/img/logo.jpg" alt="" style="border-radius: 8px;">
    <p class="text-center mb-2"><strong>El Rayo Servce Manager</strong><br> Es un sistema de gestion de inventario. punto de venta y facturación.</p>
    <a class="btn btn-success btn-sm" href="https://startbootstrap.com/theme/sb-admin-pro">Ir a sitio web!</a>
</div>

</ul>
<!-- End of Sidebar -->