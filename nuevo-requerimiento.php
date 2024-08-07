<?php
    session_start();

    include 'modelo/conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (!isset($_SESSION['id_usuario'])) {
        header("Location:login.php");
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

    <title>Nuevo requerimiento | El Rayo Service Manager</title>

    <!-- Custom fonts for this template-->
    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="src/vendor/datatables/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="src/vendor/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="src/vendor/bower_components/select2-bootstrap-theme/dist/select2-bootstrap.css">
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <link href="src/css/bootstrap-select.min.css" rel="stylesheet" />

<style>
/* Estilos para el indicador de carga */
.loader {
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 2s linear infinite;
}

#area-loader{
    display: flex;
    align-items: center; /* Centrar verticalmente */
    justify-content: center; /* Centrar horizontalmente */
}

/* Animación de rotación para el indicador de carga */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

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
                <?php include 'views/navbar.php'; ?>
                <!-- End of Topbar -->


                <!-- Begin Page Content -->
                <div class="container-fluid">

                     <!-- Contenido -->
                     <div class="card" style="height: auto; width: auto;">
                            <h4 class="ml-auto mr-auto mt-5" style="color:#191919;">Nuevo requerimiento</h4>
                            <div class="row mt-4">
                            <div class="col-md-12">
                            <form action=""  class="m-auto" style="width: 50%;">   
                            <div class="form-group m-auto"  style="width: 100%;">
                                <div class="row">
                                <div class="col-12 col-md-11" id="contenedor-llantas-buscador">
                                        <label for="busquedaLlantas" class="">Selecciona una llanta</label>
                                        <select style="width:100%" class="form-control" id="busquedaLlantas" value="" name="search"></select> 
                                    </div>
                                   <!--  <div class="col-12 col-md-2">
                                            <a href="#" class="btn btn-success" style="margin-top:27px !important;" onclick="agregarLLanta();">
                                                <span class="icon text-white-50">
                                                    <i class="fas fa-plus-circle"></i>
                                                </span>
                                            </a>
                                    </div> -->
                                </div>
                            </div> 
                            <?php
                                $estatus_pendiente = 'Pendiente';
                                $select_s = "SELECT * FROM sucursal";
                                $stmt = $con->prepare($select_s);
                                $stmt->execute();
                                $result_data = $stmt->get_result();
                                //$stmt->fetch();
                                $stmt->free_result();
                                $stmt->close();
                                $arreglo_suc = [];
                               ?>
                            <div class="form-group mt-3 row"  style="width: 100%;">
                                <div class="col-md-6">
                                    <label for="sucursal-ubicacion" class="">Sucursal</label>
                                    <select style="width:100%" class="form-control" id="sucursal-ubicacion" onchange="resetearSelect2()" name="clientes">
                                    <option value="">Seleccionar sucursal</option>
                                    <?php 
                                    while ($fila_suc = $result_data->fetch_assoc()) {
                                        $nombre_sucursal = $fila_suc['nombre'];
                                        $id_sucursal = $fila_suc['id'];
                                        $id_sucursal_usuario = $_SESSION['id_sucursal'];
                                        if($id_sucursal != $id_sucursal_usuario){
                                            print_r("<option value='$id_sucursal'>$nombre_sucursal</option>");
                                        }
                                    }
                                    ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Cantidad</label>
                                    <input type="number" id="cantidad" class="form-control" placeholder="0">
                                </div>    
                            </div>
                            <div class="form-group mt-3 row justify-content-center"  style="width: 100%;">
                                <div class="col-8 d-flex flex-column justifiy-content-center" id="area-btn-agregar">
                                    <div class="btn btn-success m-auto" cotizacion="0" id="btn-agregar"onclick="agregarProducto();">Agregar</div>
                                </div>
                            </div>

                            </form>
                            </div>
                            
                            <div class="col-12 col-md-10 ml-auto mr-auto mt-5 text-center">
                                <table id="pre-requisicion" class="table table-warning table-bordered table-hover round_table"></table>
                                <div class="row text-center mt-3 justify-content-center align-items-center">
                                    <div class="col-12 col-md-4">
                                        <div class="center-block">
                                            <label for="">Total:</label>
                                            <input type="text" id="total-llantas" class="form-control" placeholder="0" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            </div>
                            <div class="form-group mt-3 row justify-content-center align-items-center"  style="width: 100%;">
                            <div class="btn btn-danger text-white mr-3" onclick="realizarComentario();" comentario="" style="color: rgb(31, 28, 28);" id="hacer-comentario"><i class="fas fa-comment-dots"></i></div>
                            <div class="btn btn-danger" id="btn-cotizar" onclick="realizarRequerimiento();">Realizar requerimiento</div>
                            </div>

                     </div>
                   

                </div>
            <!-- End of Main Content -->
  <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; El Rayo Service Manager 2020</span><br><br>
<!--                         <span>Edicion e integración por <a href="https://www.facebook.com/BrayanM03/">Brayan Maldonado</a></span>
 -->                    </div>
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
                    <a class="btn btn-primary" href="./modelo/login/cerrar-sesion.php">Salir</a>
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

    <!-- Page level plugins -->
    <script src="src/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
 
    <script src="src/vendor/datatables/jquery.dataTables.min.js"></script> 
    <script src="src/vendor/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="src/vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous"></script>
    <script src="src/js/bootstrap-select.min.js"></script>

    <script src="src/js/generar-token.js"></script>
    <script src="src/js/nueva-requision.js"></script>

   
</body>

</html>