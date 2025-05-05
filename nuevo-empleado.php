<?php
session_start();

date_default_timezone_set("America/Matamoros");
include 'modelo/conexion.php';
$con = $conectando->conexion();

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

    <title>Nuevo empleado | El Rayo Service Manager</title>

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
        href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="src/vendor/bower_components/select2-bootstrap-theme/dist/select2-bootstrap.css">

    <!---Librerias de estilos-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <link href="src/css/slide_notificaciones.css" rel="stylesheet">
    <link href="src/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="src/css/form-field.css" rel="stylesheet">
    <link href="src/css/breadcrumb.css" rel="stylesheet">

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


        .btn-x-documento {
            font-size: 2.5rem;
            font-weight: 500;
            color: white;
            -webkit-text-stroke: 2px black;
            font-weight: bold;
            position: absolute;
            left: 2rem;
            cursor: pointer;
        }

        .btn-x-documento:hover {
            color: red;
        }

        .btn-light {
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

        .btn-light:focus {
            outline: none !important;
            border-color: #4570f0 !important;
        }

        .card_usuario {
            width: 50%;
            padding-bottom: 3rem;
            margin: auto;
        }

        /*  @media  (max-width: 1600px) {
            #area-resultados{
                width: 100% !important;
                border: 1px solid red !important;
            }
        } */

        .swal2-content {
            /*Este estilo oculta el boton del sweetalert que se sobreponen en los selects*/
            z-index: 2 !important;
        }

        .dropdown-toggle {
            height: 31px;

        }

        .filter-option-inner-inner {
            font-size: 13px;
            margin-left: .6rem;
        }


        .file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .file-upload-section {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
        }

        .file-label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .file-drop-zone {
            padding: 20px;
            border: 2px dashed rgb(133, 135, 150);
            text-align: center;
            color: rgb(133, 135, 150);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .file-drop-zone:hover, .file-drop-zone.drag-over{
            color: #51d1f6;
            border: 2px dashed #51d1f6;
        }

        .file-drop-zone:hover {
            background-color: #f0f8ff;
        }

        .browse-btn {
            background: none;
            border: 1px solid rgb(133, 135, 150);
            padding: 5px 10px;
            color:rgb(133, 135, 150);
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
        }

        .browse-btn:hover {
            background-color: #51d1f6;
            color: white;
        }

        .file-preview {
            margin-top: 10px;
        }
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
            <div id="content" sucursal_session_id="<?php echo $_SESSION['id_sucursal']; ?>" rol_session_id="<?php echo $_SESSION['rol'] ?>">

                <!-- Topbar -->
                <?php include 'views/navbar.php'; ?>
                <!-- End of Topbar -->
                <?php include 'views/sidebar-derecho.php'; ?>

                <!-- Begin Page Content -->
                <div class="containers border" style="height: 100%; padding-top:2%">
                    <div class="card card_usuario" id="formulario-nuevo-empleado" style="width:70%;">
                        <div class="card-header" style="background-color: white;">
                            <div class="row justify-content-between">
                                <div class="col-6 mt-3"><span>Información del empleado</span></div>
                                <div class="col-6 text-right">
                                    <nav class="breadcrumbs">
                                        <div id="bread_paso_1" class="breadcrumbs__item is-active">Datos generales</div>
                                        <div id="bread_paso_2" class="breadcrumbs__item">Horario</div>
                                        <div id="bread_paso_3" class="breadcrumbs__item">Datos adjuntos</div>
                                    </nav>
                                </div>
                            </div>
                        </div>

                        <div class="card-body" id="card-body" style="background-color: white;">
                            <div class="row mt-4 justify-content-center">

                                <div id="area-canvas" class="col-md-2 col-12">
                                    <img src="./src/img/neumaticos/NA.JPG" alt="" style="width: 9rem; border:1px whitesmoke solid; border-radius:7px">
                                </div>
                                <div class="col-md-8 col-12">
                                    <input type="file" id="foto-perfil" class="d-none" onchange="setearFotoThumb()">
                                    <div class="btn btn-sm btn-primary" style="margin-top: 5.5rem; margin-left:.7rem" onclick="cargarFoto()">Subir foto</div>
                                </div>
                            </div>
                            <div class="row mt-3 justify-content-center">
                                <div class="col-3">
                                    <label for="nombre"><b>Nombre</b></label>
                                    <input type="text" class="form-field form-control-sm" id="nombre" placeholder="Nombre">
                                    <small style="color:red" class="d-none" id="nombre-adv">Ingresa nombre del empleado</small>
                                </div>
                                <div class="col-4">
                                    <label for="apellidos"><b>Apellidos</b></label>
                                    <input type="text" class="form-field form-control-sm" id="apellidos" placeholder="Apellidos">
                                    <small style="color:red" class="d-none" id="apellidos-adv">Ingresa apellidos del empleado</small>
                                </div>
                                <div class="col-3">
                                    <label for="rfc"><b>RFC</b></label>
                                    <input type="text" class="form-field form-control-sm" id="rfc" placeholder="Nombre">
                                </div>
                                <div class="col-4 mt-3">
                                    <label for="sucursal"><b>Sucursal</b></label>
                                    <select class="form-control selectpicker form-control-sm" id="sucursal" style="border: 1px solid red !important;">
                                        <option value="">Selecciona una sucursal</option>
                                        <?php

                                        $select = "SELECT COUNT(*) FROM sucursal";
                                        $res = $con->prepare($select);
                                        $res->execute();
                                        $res->bind_result($total_s);
                                        $res->fetch();
                                        $res->close();

                                        if ($total_s > 0) {

                                            $consultar = "SELECT id, nombre FROM sucursal";
                                            $resp = $con->prepare($consultar);
                                            $resp->execute();
                                            $resultado = $resp->get_result();
                                            while ($data = $resultado->fetch_assoc()) {
                                                echo '<option value="' . $data['id'] . '">' . $data['nombre'] . '</option>';
                                            }
                                            $resp->close();
                                        }
                                        ?>
                                    </select>
                                    <small style="color:red" class="d-none" id="sucursal-adv">Selecciona una sucursal</small>
                                </div>
                                <div class="col-3 mt-3">
                                    <label for="puesto"><b>Puesto</b></label>
                                    <select class="form-control selectpicker form-control-sm" id="puesto" data-live-search="true">
                                        <option value="">Selecciona un puesto</option>
                                        <?php
                                        $select = "SELECT COUNT(*) FROM puestos WHERE estatus = 1";
                                        $res = $con->prepare($select);
                                        $res->execute();
                                        $res->bind_result($total_s);
                                        $res->fetch();
                                        $res->close();

                                        if ($total_s > 0) {
                                            $consultar = "SELECT id, nombre FROM puestos WHERE estatus = 1";
                                            $resp = $con->prepare($consultar);
                                            $resp->execute();
                                            $resultado = $resp->get_result();
                                            while ($data = $resultado->fetch_assoc()) {
                                                echo '<option value="' . $data['id'] . '">' . $data['nombre'] . '</option>';
                                            }
                                            $resp->close();
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-3 mt-3">
                                    <div>
                                        <label for="salario-base"><b>Salario base</b></label>
                                        <input type="number" class="form-field form-control-sm" id="salario-base" placeholder="0.00">
                                        <small style="color:red" class="d-none" id="salario-base-adv">Ingresa salario base</small>

                                    </div>
                                </div>
                            </div>
                            <div class="row text-start mt-4 justify-content-center">
                                <div class="col-4">
                                    <label for="telefono"><b>Telefono</b></label>
                                    <input type="text" class="form-field form-control-sm" id="telefono" placeholder="+52 83 4268 2283">
                                </div>
                                <div class="col-3">
                                    <label for="correo"><b>Correo</b></label>
                                    <input type="email" class="form-field form-control-sm" id="correo" placeholder="alguien@empresa.com">
                                </div>
                                <div class="col-3">
                                    <div>
                                        <label for="genero"><b>Genero</b></label>
                                        <select class="form-control selectpicker form-control-sm" id="genero">
                                            <option value="1">Masculino</option>
                                            <option value="2">Femenino</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row text-start mt-4 justify-content-center">
                                <div class="col-3">
                                    <div>
                                        <label for="fecha-cumple"><b>Cumpleaños</b></label>
                                        <input type="date" class="form-field form-control-sm" id="fecha-cumple">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div>
                                        <label for="fecha-ingreso"><b>Fecha ingreso</b></label>
                                        <input type="date" class="form-field form-control-sm" id="fecha-ingreso">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div>
                                        <label for="usuario"><b>Usuario sistema</b></label>
                                        <select class="form-control selectpicker form-control-sm" id="usuario" data-live-search="true">
                                            <option value="0">Sin usuario</option>
                                            <?php
                                            $select = "SELECT COUNT(*) FROM usuarios WHERE estatus = 1";
                                            $res = $con->prepare($select);
                                            $res->execute();
                                            $res->bind_result($total_s);
                                            $res->fetch();
                                            $res->close();

                                            if ($total_s > 0) {
                                                $consultar = "SELECT id, nombre FROM usuarios WHERE estatus = 1";
                                                $resp = $con->prepare($consultar);
                                                $resp->execute();
                                                $resultado = $resp->get_result();
                                                while ($data = $resultado->fetch_assoc()) {
                                                    echo '<option value="' . $data['id'] . '">' . $data['nombre'] . '</option>';
                                                }
                                                $resp->close();
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row text-start mt-4 justify-content-center">
                                <div class="col-10">
                                    <div>
                                        <label for="direccion"><b>Dirección</b></label>
                                        <textarea class="form-field form-control-md" id="direccion" placeholder="Escribe la dirección del empleado"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row text-center mt-4 justify-content-center">
                                <div class="col-4">
                                    <div class="btn btn-info" onclick="siguienteProceso(1,1)">Siguiente</div>
                                </div>
                            </div>


                        </div>

                    </div>
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
    <script src="src/js/bootstrap-select.min.js"></script>
    <script src="src/js/empleados/nuevo-empleado.js?v=<?php echo (rand()); ?>"></script>

    <script>
        $("#sucursal").selectpicker({
            style: "form-field form-control-sm "
        });

        $("#puesto").selectpicker({
            style: "form-field form-control-sm"
        });
        $("#genero").selectpicker({
            style: "form-field form-control-sm"
        });
        $("#usuario").selectpicker({
            style: "form-field form-control-sm"
        });

        let origen = 1;
    </script>

</body>

</html>