<?php
    session_start();

    date_default_timezone_set("America/Matamoros");
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

    <title>Nueva Venta | El Rayo Service Manager</title>

    <!-- Custom fonts for this template-->
 
    <link rel="stylesheet" href="src/css/nueva-venta.css">
    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <link href='https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.css' rel='stylesheet' />

    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- <link href="src/vendor/node_modules/@splidejs/splide/dist/css/splide.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="src/vendor/datatables/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="src/vendor/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="src/vendor/bower_components/select2-bootstrap-theme/dist/select2-bootstrap.css">

    <!---Librerias de estilos-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <link href="src/css/slide_notificaciones.css" rel="stylesheet">
    <link href="src/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="src/css/form-field.css" rel="stylesheet">
    <link href="src/css/punto_venta.css" rel="stylesheet">
     
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />
<style>
   /*  .card,.grupo-2, #content, .sticky-footer{
        background-color: rgb(21, 20, 27);
    } */
    /* .selectpicker{
        background-color: white !important; 
      
    } */
    /* .bootstrap-select{
       // border: 1px solid #ccc !important;
        //max-width: 350px !important;
   
    } */
    input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
    .btn-light{
            height: 43px !important;
            border-radius: 6px !important;
            border: 1px solid #CDD9ED !important;
            background: #fff !important;
            width: 100% !important;
            color: #99A3BA !important;
            padding: 8px 16px !important;
            line-height: 25px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            font-family: inherit !important;
            transition: border 0.3s ease !important;
      
        }

        .btn-light:focus{
            outline: none !important;
        border-color: #4570f0 !important;
        }

       /*  @media  (max-width: 1600px) {
            #area-resultados{
                width: 100% !important;
                border: 1px solid red !important;
            }
        } */
</style>
</head>

<body id="page-top"> 

<?php include 'views/loader.php'; ?>
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!----sidebar--->

        <?php 
            require_once 'sidebar.php'
        ?>


        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content" sucursal_session_id= "<?php echo $_SESSION['id_sucursal']; ?>" rol_session_id="<?php echo $_SESSION['rol']?>">

                <!-- Topbar -->
                <?php include 'views/navbar.php'; ?>
                <!-- End of Topbar -->
                <?php include 'views/sidebar-derecho.php'; ?>


                <!-- Begin Page Content -->
                <div class="container-fluid">
                        <?php include 'views/punto-venta-servicios.php' ?>
                </div>
             </div>
            <!-- End of Main Content -->
  <!-- Footer -->
            <footer class="sticky-footer">
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
    
    <script src="src/vendor/datatables/defaults.js"></script>
    <script src="src/vendor/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.min.js"></script>
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.css" type="text/css">

    <!--<script src="src/js/agregar-info-tabla-venta.js"></script>-->
    <!-- <script src="src/vendor/node_modules/@splidejs/splide/dist/js/splide.min.js"></script> -->
    <script src="src/js/bootstrap-select.min.js"></script>
    <script src="src/js/punto-venta.js?v=<?php echo(rand()); ?>"></script>
    <script src="src/js/agregar-cliente.js?v=<?php echo(rand()); ?>"></script>
    
  <script>
     document.addEventListener('keydown', function(event) {
            // Verifica si la tecla presionada es 'Tab'
            if (event.key === 'Tab') {
                mostrarSidebarCart()
            }
        });
   </script>
   
</body>

</html>