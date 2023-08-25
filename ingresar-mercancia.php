<?php
session_start();

include 'modelo/conexion.php';
$con = $conectando->conexion();


if (!isset($_SESSION['id_usuario'])) {
    header("Location:login.php");
}

if ($_SESSION['rol'] == 3) {
    header("Location:nueva-venta.php");
}
$sucursal_id = $_GET['sucursal_id'];

$querySucu = "SELECT COUNT(*) FROM sucursal WHERE id=?";
$resps = $con->prepare($querySucu);
$resps->bind_param('i', $sucursal_id);
$resps->execute();
$resps->bind_result($total_sucu);
$resps->fetch();
$resps->close();

if($total_sucu > 0) {
    $querySuc = "SELECT * FROM sucursal  WHERE id= $sucursal_id";
    $respon = mysqli_query($con, $querySuc);



    while ($rows = $respon->fetch_assoc()) {
        $suc_identificador = $rows['id'];
        $nombre_sucursal = $rows['nombre'];
    }
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
        <link rel="stylesheet" href="src/vendor/bower_components/select2-bootstrap-theme/dist/select2-bootstrap.css">
    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />
    <link rel="stylesheet" href="src/css/inventario.css">
    <link href="src/css/bootstrap-select.min.css" rel="stylesheet" />
    <style>
       .toastr-container{
         z-index: 999999999999999999;
         background-color: green;
         }
         .select2-container.form-control {
                height: auto !important;
         }
    </style>


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
               
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-6">
                            <div class="card p-3">
                                <div class="row mt-4">
                                    <div class="col-12 col-md-12 text-center">
                                        <h5><b>Agregar nueva llanta al <br>
                                            inventario de <?php echo $nombre_sucursal ?></b></h5>  
                                    </div>
                                </div>
                                <div class="row justify-content-center mt-4">
                                    <div class="col-12 col-md-6 text-center">
                                        <label>Proveedor:</label> 
                                        <select class="form-control selectpicker" data-live-search="true" id="proveedor">
                                            <option value="0">Selecciona un proveedor</option>
                                        <?php

        $querySucu = "SELECT COUNT(*) FROM proveedores";
$resps = $con->prepare($querySucu);
$resps->execute();
$resps->bind_result($total_sucu);
$resps->fetch();
$resps->close();

if($total_sucu > 0) {
    $querySuc = "SELECT * FROM proveedores";
    $respon = mysqli_query($con, $querySuc);



    while ($rows = $respon->fetch_assoc()) {
        $suc_identificador = $rows['id'];
        $nombre_suc = $rows['nombre'];

        echo "<option value='". $suc_identificador."'>".$nombre_suc."</option>";
    }
}

?>
                                        </select> 
                                    </div>
                                    <div class="col-md-4">
                                        <label>No. Factura</label>
                                        <input class="form-control mb-2" placeholder="Folio" type="text" id="folio-factura">
                                    </div>
                                </div>

                                <div class="row justify-content-center mt-3">
                                    <div class="col-12 col-md-8 text-center">
                                        <label for="buscador">Selecciona la llanta que moveras</label>
                                        <select  class="form-control" id="buscador" disabled></select>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <a href="#" class="btn btn-success mt-4" onclick="agregarLLanta();">
                                            <span class="icon text-white-50">
                                                <i class="fas fa-plus-circle"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>

                                <div class="row justify-content-center mt-3">

                                     <div class="col-12 col-md-3 text-center">
                                        <label for="stock">Stock actual</label>
                                        <input type="number" placeholder="0" class="form-control" id="stock_actual" disabled> 
                                    </div>
                                    

                                    <div class="col-12 col-md-7 text-center">
                                        <label for="stock">¿Cuantas llantas vas a ingresar?</label>
                                        <input type="number" placeholder="0" class="form-control" id="stock" valido disabled>
                                        <div class="invalid-feedback" id="label-validator">
                                            
                                        </div>
                                    </div>
                                </div>

                                

                                <div class="row justify-content-center mt-3 mb-3">
                                    <div class="col-12 col-md-10 text-center">
                                            <div class="btn btn-primary disabled" onclick="todas();" id="btn-mover" id_sucursal="<?php echo $sucursal_id ?>" id_usuario="<?php echo $_SESSION['id_usuario']; ?>" disabled>Agregar a la lista</div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 justify-content-center">
                        <div class="col-12 col-md-8">
                            <div class="card">
                                <div class="card-header text-center">
                                    <span>Se ingresaran las siguientes llantas:</span>
                                </div>
                            <div class="list-group" style="background-color: #32bacd">
                                <a href="#" class="list-group-item active">
                                    <div class="row">
                                        <div class="col-12 col-md-1">#</div>
                                        <div class="col-12 col-md-4">Llanta</div>
                                        <div class="col-12 col-md-2">Ubicación</div>
                                        <div class="col-12 col-md-2">Destino</div>
                                        <div class="col-12 col-md-2">Cantidad</div>
                                        <div class="col-12 col-md-1"></div>
                                    </div>
                                </a>
                                <div id="cuerpo_detalle_cambio">

                                </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row m-4 justify-content-center">
                        <div class="col-12 col-md-8 text-center">
                            <div class="btn btn-success" id="btn-mov" onclick="realizarIngreso(<?php echo $_SESSION['id_usuario']; ?>);">Realizar el movimiento</div>
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
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- Custom scripts for all pages-->

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous"></script>
    
    <script src="src/js/maximize-select2-height.js"></script>
    
    <script src="src/js/sb-admin-2.min.js"></script>

    <script src="src/js/bootstrap-select.min.js"></script>
    <script src="src/js/agregar-mercancia-inventario.js"></script>
    <script>
ocultarSidebar();
function ocultarSidebar(){
  let sesion = $("#emp-title").attr("sesion_rol");
  if(sesion == 4){
    $(".rol-4").addClass("d-none");

  }
};
   </script>
 
    </script>
   
</body>

</html>
