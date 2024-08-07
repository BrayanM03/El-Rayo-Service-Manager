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

    <title>Historial de cortes - El Rayo | Service Manager</title>

    <!-- Custom fonts for this template-->
    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <link rel="stylesheet" href="src/vendor/datatables/dataTables.bootstrap4.css">

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
                <div class="row">
                       <div class="col-12 justify-content-center align-items-center m-auto" style="">
                        <h3 class="text-center">Historial de cortes realizados</h3>
                       </div>
                </div>  
                <div class="row mt-5">
                    <div class="col-12 justift-content-center">
                        <div class="col-12text-center" style="margin:auto;">
                        <div class="card p-3">
                        
                        <table class="table m-3 table-striped  table-md display" style="width:80%" id="historial-cortes">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Sucursal</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Venta total</th>
                                    <th>Ganancia total</th>
                                    <th>Ganancia efectivo</th>
                                    <th>Ganancia transferencia</th>
                                    <th>Ganancia tarjeta</th>
                                    <th>Ganancia cheque</th>
                                    <th>Sin definir</th>
                                    <th>Creditos realizados</th>
                                    <th>Ventas realizadas</th>
                                </tr>
                            </thead>
                        </table>
                               
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
    <script src="src/js/sb-admin-2.min.js?v=1239"></script>

    
   <!--  <script src="src/js/notificaciones.js"></script> -->
       
   <script src="src/vendor/datatables/jquery.dataTables.min.js"></script> 
    <script src="src/vendor/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="src/vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="src/js/historial-cortes.js"></script>

   
</body>

</html>
