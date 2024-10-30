<?php
session_start();

include 'modelo/conexion.php';
$con= $conectando->conexion(); 


if (!isset($_SESSION['id_usuario'])) {
    header("Location:login.php");
}

///Redirección opcional
if ($_SESSION['id_usuario'] == 1 || $_SESSION['id_usuario'] == 8) {
    header("Location:dashboard.php?nav=inicio&id=0");
}


if ($_SESSION['rol'] == 5) {
    header('Location:ventas-pv.php?id=0&nav=ventas');
}

if ($_SESSION['rol'] == 4) {
    header("Location:inventario.php?id=1&nav=inv");
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="src/img/rayo.svg" />

    <title>El Rayo | Service Manager</title>

    <!-- Custom fonts for this template-->
    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/menu-vertical.css" rel="stylesheet">

</head>

<body id="page-top"> 

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php 
            require_once 'sidebar.php'
        ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include 'views/navbar.php'; ?>
                <!-- End of Topbar -->


                <!-- Begin Page Content --> 
                <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h3 class="h3 mb-0 text-gray-800">Bievenido al panel <?php echo $_SESSION['nombre']; ?></h3>
                        
                    </div>
                    <div class="row mb-5 justify-content-center">
                        <div class="col-md-8 col-sm-12">
                            <img class="tyre-decoration-left" src="./src/img/tyre.svg" alt="insertar SVG con la etiqueta image" style="position: absolute; width:10rem; left:-70px; top:50px"> 
                                <div class="card border-left-success shadow h-100 py-2" style="z-index: 9 !important;">
                                        
                                    <div class="card-header d-flex flex-row align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-success"><span id="titulo-ventas-hoy">Montos totales</span></h6>
                                        <div class="dropdown no-arrow">
                                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" id="lista-suc-ventas-hoy" aria-labelledby="dropdownMenuLink">
                                                <div class="dropdown-header" id="sucursales-contenedor-ventas-hoy" id_sucursal_seleccionada="<?php echo $_SESSION['id_sucursal'] ?>" nombre_sucursal_seleccionada='<?php echo $_SESSION['sucursal'] ?>'>Sucursales:</div>
                                                <?php
                                                /* $id_sucursal_usuario = $_SESSION['id_sucursal']; 
                                                $querySuc = "SELECT COUNT(*) FROM sucursal WHERE id = ?";
                                                $resp = $con->prepare($querySuc);
                                                $resp->bind_param('s', $id_sucursal_usuario);
                                                $resp->execute();
                                                $resp->bind_result($total_suc);
                                                $resp->fetch();
                                                $resp->close(); */

                                                
                                              /*   if ($total_suc > 0) {
                                                    $querySuc = "SELECT * FROM sucursal WHERE id_sucursal = $id_sucursal_usuario";
                                                    $resp = mysqli_query($con, $querySuc);

                                                    while ($row = $resp->fetch_assoc()) {
                                                        $suc_identificador = $row['id'];
                                                        $nombre = $row['nombre'];

                                                        $tarer_colores = $con->prepare("SELECT color_out, color_hover, color_sweet FROM `colores_sucursales` WHERE id_suc = ?");
                                                        $tarer_colores->bind_param('i', $suc_identificador);
                                                        $tarer_colores->execute();
                                                        $tarer_colores->bind_result($background, $hover, $sweet);
                                                        $tarer_colores->fetch();
                                                        $tarer_colores->close();



                                                        echo '<a class="dropdown-item" href="#" onclick="cambiarSucursalVentasHoy('.$suc_identificador.', `'.$nombre.'`); marcarSeleccionado(this, `lista-suc-ventas-hoy`, true, '.$suc_identificador.')">' . $nombre . '</a>';
                                                    }
                                                } */

                                                ?>
                                                <!-- <a class="dropdown-item" href="#" onclick="graficaBarPedroCardenas();">Pedro Cardenas</a> 
                                                <a class="dropdown-item" href="#" onclick="graficaBarSendero();">Sendero</a> -->
                                                <div class="dropdown-divider"></div>
                                              <!--   <a class="dropdown-item" href="#" onclick="graficaBarGeneral();">Fusionar</a> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body" id="card-body-ventas-hoy">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-md-5">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Descripción</div>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <div class="text-sm mb-1 font-weight-bold text-gray-800">Efectivo</div>
                                                        </div>
                                                        <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <div class="text-sm mb-1 font-weight-bold text-gray-800">Tarjeta</div>
                                                        </div>
                                                        <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <div class="text-sm mb-1 font-weight-bold text-gray-800">Transferencia</div>
                                                        </div>
                                                        <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <div class="text-sm mb-1 font-weight-bold text-gray-800">Cheque</div>
                                                        </div>
                                                        <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                                                            
                                                        </div>
                                                    </div>
                                                    <!-- <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <div class="text-sm mb-1 font-weight-bold text-gray-800">Deposito</div>
                                                        </div>
                                                        <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                                                            
                                                        </div>
                                                    </div> -->
                                                    <div class="row mb-2">
                                                        <div class="col-12 col-md-6">
                                                            <div class="text-sm mb-1 font-weight-bold text-gray-800">Sin definir</div>
                                                        </div>
                                                        <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <div class="h5 mb-1 font-weight-bold text-gray-800">Total</div>
                                                        </div>
                                                        <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Ingreso</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="entrada-hoy-efectivo">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="entrada-hoy-tarjeta">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="entrada-hoy-transferencia">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="entrada-hoy-cheque">$0,00</div>
                                                    <!-- <div class="text-sm mb-1 font-weight-bold text-gray-800" id="entrada-hoy-deposito">$0,00</div> -->
                                                    <div class="text-sm mb-3 font-weight-bold text-gray-500" id="entrada-hoy-sin-definir">$0,00</div>
                                                    <div class="h5 mb-1 font-weight-bold text-gray-500" id="entrada-hoy-total">$0,00</div>
                                                </div>
                                                <div class="col-md-1 text-center">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Gastos</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="gasto-hoy-efectivo">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="gasto-hoy-tarjeta">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="gasto-hoy-transferencia">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="gasto-hoy-cheque">$0,00</div>
                                                    <!-- <div class="text-sm mb-1 font-weight-bold text-gray-800" id="gasto-hoy-deposito">$0,00</div> -->
                                                    <div class="text-sm mb-3 font-weight-bold text-gray-500" id="gasto-hoy-sin-definir">$0,00</div>
                                                    <div class="h5 mb-1 font-weight-bold text-gray-500" id="gasto-hoy-total">$0,00</div>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Importe</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="importe-hoy-efectivo">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="importe-hoy-tarjeta">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="importe-hoy-transferencia">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="importe-hoy-cheque">$0,00</div>
                                                    <!-- <div class="text-sm mb-1 font-weight-bold text-gray-800" id="importe-hoy-deposito">$0,00</div> -->
                                                    <div class="text-sm mb-3 font-weight-bold text-gray-500" id="importe-hoy-sin-definir">$0,00</div>
                                                    <div class="h5 mb-1 font-weight-bold text-gray-500" id="importe-hoy-total">$0,00</div>
                                                </div>
                                            </div>
                                        </div>
                                </div>   
                            <img class="tyre-decoration-left" src="./src/img/tyre.svg" alt="insertar SVG con la etiqueta image" style="position: absolute; width:10rem; right:-70px; top:50px">              
                        </div>
                    </div>
                    <div class="row justify-content-center mb-5">
                        <div class="col-md-6 col-lg-5 text-center d-flex justify-content-center">
                            <div class="opciones-inferiores" id="opciones_inferiores" tipo_opcion='5'>
                                <ul>
                                    <li onclick="ventasRelizadasHoy()">Ventas realizadas</li>
                                    <li onclick="creditosAbiertosHoy()">Creditos abiertos</li>
                                    <li onclick="abonosRealizados()">Abonos realizados</li>
                                    <li onclick="gastosRealizados()">Gastos realizados</li>
                                    <li onclick="montosTotales()">Montos totales</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-3">
                        <!-- <div class="row justify-content-center">
                            <div class="col-12 col-md-5 mt-3 text-center border p-5" style="background-color: white; border-radius:8px;">
                                <h2><b>MODULO EN REMODELACIÓN</b></h2>
                                <div class="row">
                                        <div class="col-6">
                                            <div class="option-card text-center text-center">
                                                <dotlottie-player src="https://lottie.host/3b8fd7df-9115-47ec-a822-a8d71128c79d/AMeYTap4Re.json" background="transparent" speed="1" style="width: 250px; height: 250px;" loop autoplay></dotlottie-player>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="option-card text-center text-center">
                                                <dotlottie-player src="https://lottie.host/3969e87e-dd5c-4991-8ed9-8c9e2ce41ae6/hIR8pef1be.json" background="transparent" speed="1" style="width: 250px; height: 250px;" loop autoplay></dotlottie-player>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div> -->

                </div>
                
                <div class="row" style="display: flex; justify-content: center; margin-top: 80px;">
                        <img src="src/img/undraw_snow_fun_re_plbr.svg" alt="" width="400px">
                </div>

            </div>
            <!-- End of Main Content -->
  <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; El Rayo Service Manager  <?php print_r(date("Y")) ?></span><br><br>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">¿Listo para salir?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Seleccione "salir" para cerra su sesión actual.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-primary" href="modelo/login/cerrar-sesion.php">Salir</a>
                </div>
            </div>
        </div>
    </div>

    <script>

       

    </script>

    <!-- Bootstrap core JavaScript-->
    <script src="src/vendor/jquery/jquery.min.js"></script>
    <script src="src/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>
    <!-- Core plugin JavaScript-->
    <script src="src/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="src/js/sb-admin-2.min.js?v=1239"></script>

    <!-- Page level plugins -->
    <script src="src/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="src/js/demo/chart-area-demo.js"></script>
    <script src="src/js/demo/chart-pie-demo.js"></script>
   <!--  <script src="src/js/notificaciones.js"></script> -->
    <script src="src/js/panel-vendedores.js"></script>

  
 
    </script>
   
</body>

</html>
