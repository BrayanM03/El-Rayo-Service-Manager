<?php
session_start();

include 'modelo/conexion.php';
$con = $conectando->conexion();

if (!$con) {
    echo "maaaaal";
}

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_usuario'] == 16 || ($_SESSION['rol'] != 4 && $_SESSION['rol'] != 1 && $_SESSION['id_usuario'] != 7)) {
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



    <title>Token | El Rayo Service Manager</title>

    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="src/css/token.css">
    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">


    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="src/vendor/datatables/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="src/vendor/bower_components/select2-bootstrap-theme/dist/select2-bootstrap.css">

    <!---Librerias de estilos-->
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
                <div class="container-fluid" style="display: flex; justify-content: center; align-items:center; flex-direction:column; ">

                    <!-- Contenido inventario -->
                    <div class="row">
                        <div class="col-10 col-md-6">
                            <div class="titulo-inventario">
                                <h5 style="margin: 10px 0px;"><b>Generar token operativo</b></h5>
                                <p style="color: gray;" class="text-center">Click en generar para generar un nuevo token de seguridad para cambiar el precio de una llanta durante una venta.</p>
                                <div class="tokens">
                                    <p style="color: gray;">El token actual es: </br>
                                        <span id="token-actual">0000</span>
                                    </p>
                                </div>
                            </div>
                            <div class="botones text-center">
                                <a href="#" class="btn btn-success btn-icon-split" onclick="random(1);">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                    <span class="text">Generar Token</span>
                                </a>
                            </div>
                        </div>
                        <div class="col-10 col-md-6">
                            <div class="titulo-inventario">
                                <h5 style="margin: 10px 0px;"><b>Generar token administrativo</b></h5>
                                <p style="color: gray;" class="text-center">Click en generar para generar un nuevo token de seguridad para autorizar ventas de credito a clientes nuevos o a clientes
                                    con credito vencido.</p>
                                <div class="tokens">
                                    <p style="color: gray;">El token actual es: </br>
                                        <span id="token-administrativo">0000</span>
                                    </p>
                                </div>
                            </div>

                            <div class="botones">
                                <a href="#" class="btn btn-success btn-icon-split" onclick="random(2);">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                    <span class="text">Generar Token</span>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End of Main Content -->
                <!-- Footer -->
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>Copyright &copy; El Rayo Service Manager <?php print_r(date("Y")) ?></span><br><br>
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
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
        <script src="src/js/sb-admin-2.min.js"></script>

        <!-- Page level plugins -->
        <script src="src/vendor/chart.js/Chart.min.js"></script>

        <!-- Page level custom scripts 
    <script src="src/js/demo/chart-area-demo.js"></script>
    <script src="src/js/demo/chart-pie-demo.js"></script>-->


        <!-- Cargamos nuestras librerias-->

        <script src="src/vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="src/vendor/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="src/vendor/datatables/dataTables.bootstrap4.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="src/vendor/datatables/defaults.js"></script>
        <script src="src/vendor/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script src="src/js/generar-token.js"></script>
        <script>
            //  function tokenActual() {  
            $.ajax({
                type: "post",
                url: "modelo/token.php",
                data: "traer-token",
                dataType: "json",
                success: function(response) {
                    cod = response["codigo"];
                    cod_admin = response["codigo_administrativo"];
                    $("#token-actual").text(cod);
                    $("#token-administrativo").text(cod_admin);
                }
            });
            //}

            //tokenActual();
        </script>


</body>

</html>