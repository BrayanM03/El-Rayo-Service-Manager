<?php
session_start();

include 'modelo/conexion.php';
$con= $conectando->conexion(); 


if (!isset($_SESSION['id_usuario'])) {
    header("Location:login.php");
}

if ($_SESSION['rol'] == 3) {
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
        <link rel="stylesheet" href="src/vendor/bower_components/select2-bootstrap-theme/dist/select2-bootstrap.css">
    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />

    <style>
        /* .select2-results{
            border: 1px solid red;
        }
        .select2-results {
            height: 900px !important;
        }
        .select2-results__options{
            height: 500px !important;
            overflow: hidden !important;
        } */
        

        .toastr-container{
         z-index: 999999999999999999;
         background-color: green;
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
                <?php include 'views/navbar.php'; ?>
                <!-- End of Topbar -->


                <!-- Begin Page Content -->
                <div class="container-fluid">
               
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-5">
                            <div class="card">
                                <div class="row mt-4">
                                    <div class="col-12 col-md-12 text-center">
                                        <h5><b>Mover una llanta</b></h5>  
                                    </div>
                                </div>
                                <div class="row justify-content-center mt-4">
                                    <div class="col-12 col-md-10 text-center">
                                        <label>Mover desde:</label> 
                                        <select class="form-control" id="ubicacion">
                                            <option value="0">Selecciona una sucursal</option>
                                        <?php
            
                                        $querySucu = "SELECT COUNT(*) FROM sucursal";
                                        $resps=$con->prepare($querySucu);
                                        $resps->execute();
                                        $resps->bind_result($total_sucu);
                                        $resps->fetch();
                                        $resps->close();
                                        
                                        if($total_sucu>0){
                                            $querySuc = "SELECT * FROM sucursal";
                                            $respon = mysqli_query($con, $querySuc);
                                          
                                            

                                            while ($rows = $respon->fetch_assoc()){
                                                $suc_identificador = $rows['id'];
                                                $nombre_suc = $rows['nombre'];
                                                
                                                echo "<option value='". $suc_identificador."'>".$nombre_suc."</option>";
                                                }
                                        }
                                    
                                    ?>
                                        </select> 
                                    </div>
                                </div>

                                <div class="row justify-content-center mt-3">
                                    <div class="col-12 col-md-10 text-center">
                                        <label for="buscador">Selecciona la llanta que moveras</label>
                                        <select  class="form-control" id="buscador" disabled></select>
                                    </div>
                                </div>

                                <div class="row justify-content-center mt-3">

                                     <div class="col-12 col-md-3 text-center">
                                        <label for="stock">Stock actual</label>
                                        <input type="number" placeholder="0" class="form-control" id="stock_actual" disabled> 
                                    </div>
                                    

                                    <div class="col-12 col-md-4 text-center">
                                        <label for="stock">¿Cuantas llantas vas a mover?</label>
                                        <input type="number" placeholder="0" class="form-control" id="stock" onkeyup="comprobarStock()" onchange="comprobarStock()" valido disabled>
                                        <div class="invalid-feedback" id="label-validator">
                                            
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3 text-center">
                                        <label for="stock">Cantidad partidas</label>
                                        <input type="number" value="0" placeholder="0" class="form-control" id="cantidad-partidas" valido disabled>
                                        
                                    </div>
                                </div>

                                <div class="row justify-content-center mt-3">
                                    <div class="col-12 col-md-10 text-center">
                                        <label for="destino">Mover hacia: </label>
                                        <select name="destino" class="form-control" id="destino">
                                            <option value="0">Selecciona una sucursal</option>
                                        <?php
            
                                            $querySucu = "SELECT COUNT(*) FROM sucursal";
                                            $resps=$con->prepare($querySucu);
                                            $resps->execute();
                                            $resps->bind_result($total_sucu);
                                            $resps->fetch();
                                            $resps->close();
                                            
                                            if($total_sucu>0){
                                                $querySuc = "SELECT * FROM sucursal";
                                                $respon = mysqli_query($con, $querySuc);
                                            
                                                

                                                while ($rows = $respon->fetch_assoc()){
                                                    $suc_identificador = $rows['id'];
                                                    $nombre_suc = $rows['nombre'];
                                                    
                                                    echo "<option value='". $suc_identificador."'>".$nombre_suc."</option>";
                                                    }
                                            }
                                        
                                        ?>
                                        </select>
                                    </div>
                                </div>

                                

                                <div class="row justify-content-center mt-3 mb-3">
                                    <div class="col-12 col-md-10 text-center">
                                            <div class="btn btn-primary disabled" onclick="todas();" id="btn-mover" id_usuario="<?php echo $_SESSION['id_usuario']; ?>" disabled>Agregar a la lista</div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 justify-content-center">
                        <div class="col-12 col-md-8">
                            <div class="card">
                                <div class="card-header text-center">
                                    <span>Se moveran las siguientes llantas:</span>
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
                            <div class="btn btn-success" id="btn-mov" onclick="realizarMovimiento(<?php echo $_SESSION['id_usuario']; ?>);">Realizar el movimiento</div>
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

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous"></script>
    <script src="src/js/maximize-select2-height.js"></script>

    <script src="src/js/sb-admin-2.min.js"></script>

 
    <script src="src/js/buscador-llantas-por-sucursal.js"></script>
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
