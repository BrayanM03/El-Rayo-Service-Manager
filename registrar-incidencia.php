<?php
session_start();

include 'modelo/conexion.php';
$con = $conectando->conexion();


if (!isset($_SESSION['id_usuario'])) {
    header("Location:login.php");
}

/* if ($_SESSION['rol'] == 4 ) {
    header("Location:nueva-venta.php?nav=nueva_venta");
} */
if ($_SESSION['rol'] == 5) {
    header('Location:ventas-pv.php?id=0&nav=ventas');
}

/* if ($_SESSION['rol'] == 4) {
    header("Location:inventario.php?id=1&nav=inv");
} */


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

    <title>Incidencias | El Rayo Service Manager</title>

    <!-- Custom fonts for this template-->
    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom fonts for this template-->

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
    <link href="src/css/form-field.css" rel="stylesheet">
    <link href="src/css/calendario-dinamico.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />

    <style>
        table.dataTable td {
            font-size: 12px !important;
        }

        #detalle_requerimiento_info,
        #detalle_requerimiento_length,
        #detalle_requerimiento_filter,
        #detalle_requerimiento_paginate {
            font-size: 12px !important;
        }

        .tooltip span {
            display: none;
        }

        .tooltip:hover span {
            display: block;
            position: fixed;
            overflow: hidden;
        }

        .selectpicker {
            background-color: white !important;
            z-index: 9999999 !important;
        }

        /*   .bootstrap-select{
            border: 1px solid #ccc !important;
            max-width: 350px !important;
    
        } */

        .toast-container {
            z-index: 999999999 !important;
        }

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

        /* Estilos para el contenedor del loader */
        #custom-loader {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
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
                <!-- End of Topbar -->


                <!-- Begin Page Content -->
                <!-- Begin Page Content -->
                <div class="mt-3" style="width:90%; margin:auto;">

                    <!-- Contenido inventario -->

                    <div class="contenedor-tit">
                        <div class="row">
                            <div class="col-md-10">
                                <h3><b>Registrar incidencia</b></h3>
                            </div>
                        </div>
                        <hr>
                    </div>

                    <div class="card">
                        <form id="formulario-nueva-incidencia">
                        <div class="row m-3">
                            <div class="col-12 col-md-5">
                                <label for="empleado">Empleado</label><br>
                                <select class="form-control selectpicker" data-live-search="true" id="empleado">
                                    <option value="">Selecciona un empleado</option>
                                    <?php

                                    $select = "SELECT COUNT(*) FROM empleados WHERE estatus = 1";
                                    $res = $con->prepare($select);
                                    $res->execute();
                                    $res->bind_result($total_s);
                                    $res->fetch();
                                    $res->close();

                                    if ($total_s > 0) {

                                        $consultar = "SELECT id, nombre, apellidos, salario_base FROM empleados WHERE estatus = 1";
                                        $resp = $con->prepare($consultar);
                                        $resp->execute();
                                        $resultado = $resp->get_result();
                                        while ($data = $resultado->fetch_assoc()) {
                                            echo '<option value="' . $data['id'] . '" salario_base="'.$data['salario_base'].'">' . $data['nombre'] . ' ' . $data['apellidos'] . '</option>';
                                        }
                                        $resp->close();
                                    }
                                    ?>
                                </select>
                                <small style="color:red" class="d-none" id="empleado-adv">Ingresa nombre del empleado</small>

                            </div>

                            <div class="col-12 col-md-3">
                                <label for="nombre">Tipo</label>
                                <select class="form-control selectpicker" id="tipo">
                                    <option value="1">Deduccion</option>
                                    <option value="2">Percepcion</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="nombre">Categoria</label>
                                <select class="form-control selectpicker" onchange="aplicarCambiosExtras(event)" data-live-search="true" id="categoria">
                                <option value="">Selecciona una categoria</option>

                                    <?php

                                    $select = "SELECT COUNT(*) FROM categoria_incidencias WHERE estatus = 1";
                                    $res = $con->prepare($select);
                                    $res->execute();
                                    $res->bind_result($total_s);
                                    $res->fetch();
                                    $res->close();

                                    if ($total_s > 0) {

                                        $consultar = "SELECT id, nombre FROM categoria_incidencias WHERE estatus = 1";
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
                                <small style="color:red" class="d-none" id="categoria-adv">Ingresa nombre del empleado</small>

                            </div>
                        </div>
                        <div id="area-cambios-extras">

                        </div>
                        <div class="row m-3">
                            <div class="col-12 col-md-3">
                                <label for="nombre">Fecha(s) de la incidencia</label>
                                <span type="text" class="form-field w-100" style="color: #99A3BA; cursor: pointer;" id="fechas-incidencia" onclick="mostrarCalendarioDinamico()">Selecciona una fecha</span>
                                <small style="color:red" class="d-none" id="fechas-incidencia-adv">Ingresa las fechas de la incidencias</small>

                                <div class="contenedor-calendario">
                                    <div class="calendar-wrapper">
                                        <div class="calendar">
                                            <div class="header-calendar">
                                                <button id="prevBtn"><i class="fas fa-chevron-left"></i></button>
                                                <div class="monthYear" id="monthYear1"></div>
                                                <div class="monthYear" id="monthYear2"></div>
                                                <button id="nextBtn"><i class="fas fa-chevron-right"></i></button>
                                            </div>
                                            <div class="contendor-days">
                                                <div class="row justify-content-between">
                                                    <div class="col-6">
                                                        <div class="days">
                                                            <div class="day">Lun</div>
                                                            <div class="day">Mar</div>
                                                            <div class="day">Mie</div>
                                                            <div class="day">Jue</div>
                                                            <div class="day">Vie</div>
                                                            <div class="day">Sab</div>
                                                            <div class="day">Dom</div>
                                                        </div>
                                                        <div class="dates" id="dates1"></div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="days">
                                                            <div class="day">Lun</div>
                                                            <div class="day">Mar</div>
                                                            <div class="day">Mie</div>
                                                            <div class="day">Jue</div>
                                                            <div class="day">Vie</div>
                                                            <div class="day">Sab</div>
                                                            <div class="day">Dom</div>
                                                        </div>
                                                        <div class="dates" id="dates2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <input type="date" id="fecha-inicio" onchange="establecerFecha(event,1);"class="d-none">
                                <input type="date" id="fecha-final" onchange="establecerFecha(event,2);" class="d-none">
                            </div>
                            <div class="col-12 col-md-3">
                                   <label for="monto" id="label-monto">Monto</label> 
                                   <input class="form-field" placeholder="0.00" id="monto" type="number">
                                    <small style="color:red" class="d-none" id="monto-adv">Ingresa el monto total</small>

                            </div>
                            <div class="col-12 col-md-3">
                                <label for="periocidad">Periocidad</label> 
                                <select class="form-control selectpicker"  onchange="cambiarPeriocidad(event)" id="periocidad">
                                    <option value="1">Solo esta semana</option>
                                    <option value="2">Cada semana</option>
                                    <option value="3">Cada 15 días</option>
                                    <option value="4">Cada mes</option>
                                </select>
                            </div>
                        </div>
                        <div class="row m-3">
                            <div class="col-12 col-md-12">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-field" id="descripcion" placeholder="Descripción incidencia"></textarea>
                            </div>
                            
                        </div>
                        <div class="row m-3">
                            <div class="col-12 col-md-12 text-center">
                            <a href="incidencias.php"><div class="btn btn-info">Volver</div></a>
                                <div class="ml-2 btn btn-info" onclick="registrarIncidencia()">Registrar</div>
                            </div>
                            
                        </div>
                        </form>
                        
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
    <script src="./src/js/empleados/registrar-incidencia.js"></script>
    <script src="./src/js/calendario-dinamico.js"></script>
    <script>
        $("#categoria").selectpicker()
        $("#empleado").selectpicker()
    </script>

</body>

</html>