<?php
session_start();

include 'modelo/conexion.php';
$con= $conectando->conexion(); 


if (!isset($_SESSION['id_usuario'])) {
    header("Location:login.php");
}

if ($_SESSION['rol'] == 3 || $_SESSION['rol'] == 2) {
    header("Location:nueva-venta.php");
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
                <nav class="navbar navbar-expand navbar-info bg-info topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
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
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw icon-menu"></i>
                                <!-- Counter - Alerts -->
                                <span id="contador-notifi" class="badge badge-danger badge-counter">0</span>
                            </a>
 
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                   Notificaciones
                                </h6>
                                <div class="empty-notification">
                                 <img src="src/img/undraw_Notify_re_65on.svg" alt="" width="400px">
                                 <span>Ups, por el momento no hay nada por aqui</span> 
                              </div>
                              
                              <div id="contenedor-alertas"></div>
                              <a class="dropdown-item text-center small text-gray-500" href="#">Mostrar mas notificaciones</a>
                             
                            </div>
                        </li>



                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline  small" style="color: aliceblue;"><?php
                                
                                    echo $_SESSION['nombre'] . " " . $_SESSION['apellidos'];
                                
                                ?></span>
                                <img class="img-profile rounded-circle"
                                    src="src/img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Perfil
                                </a>
                                <a class="dropdown-item" href="configuraciones.php?id=0&nav=configuraciones">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Configuraciones
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Registro de actividad
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Salir
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->


                <!-- Begin Page Content -->
                <div class="container-fluid">

                     <!-- Page Heading -->
                     <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h3 class="h3 mb-0 text-gray-800">Metrica de ganancias</h3>
                        <a href="index.php" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm"><i
                                class="fas fa-hand-point-left fa-sm text-white-50"></i> Volver</a>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generar reporte</a>
                    </div>
                   
                            <!-- Content Row -->
                    <div class="row mb-3">

                    <div class="col-12 col-md-4">
                            <div class="card p-5 text-center bg-whitesmoke rounded bordered">
                                <h6>Ganancia de la semana:</h6>
                                <h4 class="color-black" id="ganancia_semana">$</h4>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="card p-5 text-center bg-whitesmoke rounded bordered">
                                <h6>Ganancia de hoy:</h6>
                                <h4 class="color-black" id="ganancia_hoy">$</h4>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card p-5 text-center bg-whitesmoke rounded bordered">
                                <h6>Ventas de hoy:</h6>
                                <h4 class="color-black" id="ventas_hoy"></h4>
                            </div>
                        </div>
                    </div>

                    

                </div>
                <!-- /.container-fluid -->


                
                    <!-- Content Row -->

                    <div class="row">

                    <div class="col-12 col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Grafica de ventas de esta semana<span id="titulo-graf"></span></h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Sucursales:</div>
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
                                                            $nombre = $row['nombre'];

                                                            $tarer_colores = $con->prepare("SELECT color_out, color_hover, color_sweet FROM `colores_sucursales` WHERE id_suc = ?");
                                                            $tarer_colores->bind_param('i', $suc_identificador);
                                                            $tarer_colores->execute();
                                                            $tarer_colores->bind_result($background, $hover, $sweet);
                                                            $tarer_colores->fetch();
                                                            $tarer_colores->close();
                                                        
                                                         
                                                           
                                                            echo '<a class="dropdown-item" href="#" onclick="graficaBarPorSucursal('.$suc_identificador.', `'. $background .'`, `'. $hover .'`,  `'. $sweet .'`,  `'. $nombre .'`);">'.$nombre.'</a>';
                                                            }
                                                    }
                                                
                                                ?>
                                            <!-- <a class="dropdown-item" href="#" onclick="graficaBarPedroCardenas();">Pedro Cardenas</a> 
                                            <a class="dropdown-item" href="#" onclick="graficaBarSendero();">Sendero</a> -->
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#" onclick="graficaBarGeneral();">Fusionar</a> 
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-area" id="chart-bar-container">
                                        <canvas id="myBarChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <!-- Buscador de ganacias darias -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary" id="titulo-graf-pie">Consultar rango ganancias</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Mostrar:</div>
                                            <a class="dropdown-item" href="#" onclick="totalVentas();">Dia en especifico</a>
                                            <a class="dropdown-item" href="#" onclick="numeroVentas();">Numero de ventas</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#" onclick="totalCreditos();">Rango de ganancias</a> 
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2" id="chart-pie-container">
                                     <!--    <canvas id="myPieChart"></canvas> -->
                                     <div class="row">
                                         <div class="col-12 col-md-5">
                                         <label for="">Fecha inicio</label>
                                         <input type="date" id="fecha-inicio" class="form-control">
                                         <div class="invalid-feedback">Elige una fecha inicial.</div>
                                         </div>
                                         <div class="col-12 col-md-5">
                                         <label for="">Fecha final</label>
                                         <input type="date" id="fecha-final" class="form-control">
                                         <div class="invalid-feedback">Elige una fecha final.</div> 
                                        </div>
                                         <div class="col-12 col-md-2" style="display:flex; justify-content:center; align-items:center; margin-top:30px; height:70%;" onclick="traerInfoRangoFecha();">
                                         <div  id="fecha-final" class="btn btn-danger"><i class="fas fa-search fa-sm"></i></div>
                                         </div>
                                     </div>
                                     <div class="row text-center mt-3">
                                         <div class="col-12 col-md-12 ">
                                             <h6>General</h6>
                                             <h4 id="result-ganancia-rango"></h4>
                                         </div>
                                         <div class="col-12 col-md-12 ">
                                             <h6>Pedro Cardenas</h6>
                                             <h4 class="text-primary" id="result-ganancia-rango-pedro"></h4>
                                         </div>
                                         <div class="col-12 col-md-12 ">
                                             <h6>Sendero</h6>
                                             <h4 class="text-success" id="result-ganancia-rango-sendero"></h4>
                                         </div>
                                     </div>
                                     </div>
                                  
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row" style="display: flex; justify-content: center; margin-top: 80px;">
                        <img src="src/img/undraw_sobre_ruedas.svg" alt="" width="400px">
                    </div>

            </div>
            <!-- End of Main Content -->
  <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; El Rayo Service Manager  <?php print_r(date("Y")) ?></span><br><br>
                        <span>Edicion e integración por <a href="https://www.facebook.com/BrayanM03/">Brayan Maldonado</a></span>
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

    <!-- Bootstrap core JavaScript-->
    <script src="src/vendor/jquery/jquery.min.js"></script>
    <script src="src/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="src/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="src/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="src/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="src/js/demo/chart-bar-demo.js"></script>


  
 
    </script>
   
</body>

</html>
