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
                <?php include 'views/navbar.php'; ?>
                <!-- End of Topbar -->


                <!-- Begin Page Content -->
                <div class="container-fluid">
                <div class="row">
                     <div class="col-12 justify-content-center align-items-center m-auto">
                        <h3 class="text-center">Realizar corte</h3>
                       </div>
                </div>  
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="col-12 col-md-10" style="margin:auto;">
                        <!-- <div class="btn btn-warning">Cerrar todo</div> -->
                        <div class="list-group mt-3 col-12">
                            <span href="" class="list-group-item">
                                <div class="row">
                                    <div class="col-12 col-md-3">
                                      <b>Sucursal</b>
                                    </div>
                                    <div class="col-12 col-md-3">
                                      <b>Ventas hoy</b>
                                    </div>
                                    <div class="col-12 col-md-3">
                                      <b>Ganancia hoy</b>
                                    </div>
                                    <div class="col-12 col-md-3">
                                      <b>Botones de accion</b> 
                                    </div>
                                </div>
                                
                            </span>

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
                                                        
                                                         
                                                           
                                                            echo '<a href="#" class="list-group-item list-group-item-action">'.
                                                                    '<div class="row">'.
                                                                        '<div class="col-12 col-md-3">'.
                                                                            'Sucursal '. $nombre    .             
                                                                        '</div>'.
                                                                        '<div class="col-12 col-md-3">'.
                                                                            '<span>$<span id="ventas_'.$suc_identificador.'"></span></span>'.            
                                                                        '</div>'.
                                                                        '<div class="col-12 col-md-3">'.
                                                                            '<span>$<span id="ganancia_'.$suc_identificador.'"></span></span>'.                 
                                                                        '</div>'.
                                                                        '<div class="col-12 col-md-3">'.
                                                                           '<div class="btn btn-primary mr-2" id="btn_estatus_corte_'. $suc_identificador.'" onclick="realizarCorte('. $suc_identificador .');">Realizar corte</div>'.  
                                                                           '<div class="btn btn-info" onclick="resumenCorte('. $suc_identificador .');">Ver </div>'  .         
                                                                        '</div>'.
                                                                    '</div></a>';
                                                        }
                                                    }
                                                
                                                ?>
<!-- 
                            <a href="#" class="list-group-item d-flex justify-content-between list-group-item-action">Sucursal Sendero:  <span>$<span id="ganancia-sendero"></span></span>
                            <div><div class="btn btn-primary" id="corte-btn-sendero" onclick="realizarCorte('Sendero');">Realizar corte</div>
                            <div class="btn btn-info" id="corte-btn-sendero" onclick="resumenCorte('Sendero');">Ver </div></div></a>
                            <a href="#" class="list-group-item d-flex justify-content-between list-group-item-action"><span>Sucursal Pedro Cardenas:</span> <span class="mr-4">$<span id="ganancia-pedro"></span></span>
                            <div><div class="btn btn-primary" id="corte-btn-pedro" onclick="realizarCorte('Pedro');">Realizar corte</div>
                            <div class="btn btn-info" id="corte-btn-sendero" onclick="resumenCorte('Pedro');">Ver </div></div></a> -->
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
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- Custom scripts for all pages-->
    <script src="src/js/sb-admin-2.min.js"></script>

 
    <script src="src/js/cortes.js"></script>

  
 
    </script>
   
</body>

</html>
