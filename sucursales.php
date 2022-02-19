<?php
session_start();

include 'modelo/conexion.php';
$con= $conectando->conexion(); 


if (!isset($_SESSION['id_usuario'])) {
    header("Location:login.php");
}

if ($_SESSION['user'] !==  "brayanm03") {
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

    <title>Configuraciones - El Rayo | Service Manager</title>

    <!-- Custom fonts for this template-->
    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <div class="f">
                <div class="row justify-content-center">
                       <div class="col-12 col-md-3 text-center">
                        <h3>Sucursales del sistema</h3>
                       </div>
                </div>  

                <div class="row mt-3 justify-content-center">
                    <div class="col-12 col-md-2 text-center">
                        <div class="btn btn-info" onclick="addSucursal();">Agregar sucursal</div>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-12 justift-content-center">
                        <div class="col-10 text-center" style="margin:auto;">
                        <div class="card">
                            <span href="#" class="list-group-item" style="background-color:#FFBF00; color:white;">
                                <b>Sucursales activas</b>
                            </span>
                            <span class="list-group-item">
                                <div class="row">
                                    <div class="col-12 col-md-1">
                                        <b>id</b>
                                    </div>
                                    <div class="col-12 col-md-3">
                                    <b> Nombre</b>
                                    </div>
                                    <div class="col-12 col-md-4">
                                    <b>Dirección</b>
                                    </div>
                                    <div class="col-12 col-md-2">
                                    <b>Telefono</b>
                                    </div>
                                    <div class="col-12 col-md-2">
                                    <b>Accion</b>
                                    </div>
                                </div>
                            </span>

                            <?php
                                $traer="SELECT COUNT(*) FROM sucursal";
                                $res= $con->prepare($traer);
                                $res->execute();
                                $res->bind_result($tot);
                                $res->fetch();
                                $res->close();

                                if($tot > 0){

                                    $query = "SELECT * FROM sucursal";
                                    $resp = mysqli_query($con, $query);

                                    while ($fila = $resp->fetch_assoc()) {
                                        $id = $fila["id"];
                                        $nombre_suc = $fila["nombre"];
                                        $calle = $fila["calle"];
                                        $numero =$fila["numero"];
                                        $colonia = $fila["colonia"];
                                        $ciudad = $fila["ciudad"];
                                        $estado = $fila["estado"];
                                        $pais = $fila["pais"];
                                        $telefono = $fila["telefono"];
                                        $rfc = $fila["RFC"];
                                        $cp = $fila["CP"];
                                        $corte = $fila["corte"];

                                        $direccion = $calle . " " . $numero . " " . $colonia . " " . $ciudad . " " . $estado . " " . $pais . " " .$cp;



                                        echo '
                                        <span class="list-group-item">
                                        <div class="row">
                                            <div class="col-12 col-md-1">
                                           <b> '.$id.'</b>
                                            </div>
                                            <div class="col-12 col-md-3">
                                            '.$nombre_suc.'
                                            </div>
                                            <div class="col-12 col-md-4">
                                            '.$direccion.'
                                            </div>
                                            <div class="col-12 col-md-2">
                                            '.$telefono.'
                                            </div>
                                            <div class="col-12 col-md-2">
                                            <i class="fas fa-edit"></i>
                                            <i class="fas fa-trash-alt"></i>
                                            </div>
                                        </div>
                                    </span>';

                                    }

                                }
                            ?>
                           </div>             
                        </div>
                    </div>
                </div>
                     

                </div>
                <!-- /.container-fluid -->


                
                    <!-- Content Row -->

                    
                    <div class="row" style="display: flex; justify-content: center; margin-top: 80px;">
                        <img src="src/img/undraw_by_my_car_ttge.svg" alt="" width="400px">
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
    <script src="src/js/agregar-sucursal.js"></script>

   
 

  
 
    </script>
   
</body>

</html>