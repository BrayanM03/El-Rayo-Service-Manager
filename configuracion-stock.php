<?php
session_start();

include 'modelo/conexion.php';
$con = $conectando->conexion();


if (!isset($_SESSION['id_usuario'])) {
    header("Location:login.php");
}
if ($_SESSION['rol'] != 4 && $_SESSION['rol'] !=1) {
    header('Location:ventas-pv.php?id=0&nav=ventas');
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

    <title>Configuracion de Stock | El Rayo Service Manager</title>

    <!-- Custom fonts for this template-->
    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="src/css/inventario.css">
    <link rel="stylesheet" href="src/css/historial-ventas.css">
    <link rel="stylesheet" href="src/css/proceso-apartados.css">

    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">


    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="src/vendor/datatables/dataTables.bootstrap4.css">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="src/vendor/bower_components/select2-bootstrap-theme/dist/select2-bootstrap.css">
    
    <!---Librerias de estilos-->
    
    <link href="src/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />

    <!-- PDF viewer and library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.10.111/web/pdf_viewer.min.css">
    <style>
        .tooltip span {
            display:none;
        }
        .tooltip:hover span {
            display:block;
            position:fixed;
            overflow:hidden;
        }

        .selectpicker{
        background-color: white !important; 
        z-index: 9999999 !important;
        }
        .bootstrap-select{
            border: 1px solid #ccc !important;
            max-width: 350px !important;
    
        }

        .toast-container{
            z-index: 999999999 !important;
        }

        .delete-thumbnail{
            border: 1px solid red !important;
            position: fixed;
            left: 52% !important;
            top: 56% !important;
            color: white;
            background-color: red;
            width: 32px;
            border-radius: 18px;
            cursor: pointer !important;
        }

        .delete-thumbnail:hover{
            background-color: orange;
        }
        .btn-light{
            background-color: white !important;
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
                <?php include 'views/navbar.php'; ?>
                <!-- Begin Page Content -->
                <!-- Begin Page Content -->
                    <div class="container-fluid">

                        <!-- Contenido inventario -->
                        <div class="row">
                            <div class="col-md-6">

                            </div>
                        </div>
                        <div class="row mt-3 mb-3 justify-content-between">
                            <div class="col-md-10">
                                <h5><b>Configuracion de Stock</b></h5>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end">
                                <div class="btn btn-primary" onclick="agregarMedida()">Agregar medida</div>
                            </div>
                        </div>

                        <table id="medidas" class="table table-striped table-bordered table-hover">
                        </table>
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
                        <span>Copyright &copy; El Rayo Service Manager <?php print_r(date("Y")) ?></span><br><br>
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

    <!-- Page level plugins -->
    <script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.10.111/build/pdf.min.js"></script>
    <script src="src/vendor/chart.js/Chart.min.js"></script>
    <script src="src/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="src/vendor/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="src/vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="src/vendor/datatables/defaults.js"></script>
    <script src="src/vendor/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="src/js/bootstrap-select.min.js"></script>


    <!-- Page level plugins -->
    <script src="src/js/configuracion-stock.js"></script>

</body>

</html>