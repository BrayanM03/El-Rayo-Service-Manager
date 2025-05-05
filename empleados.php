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

    <title>Empleados | El Rayo Service Manager</title>

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
    <link href="src/css/skeleton.css" rel="stylesheet">
    <link href="src/css/form-field.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />

    <style>
         table.dataTable td {
            font-size: 12px !important;
            }

         #detalle_requerimiento_info, #detalle_requerimiento_length, #detalle_requerimiento_filter, #detalle_requerimiento_paginate{
            font-size: 12px !important;
         }   

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

        /* Estilos para el contenedor del loader */
#custom-loader {
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: white;
  padding: 20px;
  border-radius: 8px;
}

.employee-card {
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .employee-card:hover {
            transform: scale(1.05);
            cursor: pointer;
            border: 1px solid cadetblue;
        }
        .employee-img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
        }

        .expanded-card {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width:900px;
            height: auto;
            background: white;
            z-index: 1000;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        .hidden {
            display: none;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }



        .expanded {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
        }
        .back-btn {
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .icon-hover {
        transition: transform 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .icon-hover:hover {
        transform: scale(1.2);

    }

    /* Tooltip */
    .icon-hover::after {
        content: attr(data-title);
        position: absolute;
        bottom: 100%; /* Arriba del ícono */
        left: 50%;
        transform: translateX(-50%);
        background-color: #333;
        color: #fff;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s;
    }

    .icon-hover:hover::after {
        opacity: 1;
    }

    .doc_vacio{
        opacity: .4;
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
                            <div class="col-md-6">
                                <h3><b>Lista de empleados</b></h3>
                                <span>Esta es la lista de empleados que estan contratados actualmente.</span>
                            </div>
                            <div class="col-md-6 text-right">
                                <a class="btn btn-success mt-4" href="nomina.php">Generar nomina</a>
                                <a class="btn btn-primary mt-4 mr-2 ml-2" href="incidencias.php">Incidencias</a>
                                <a class="btn btn-info mt-4" href="nuevo-empleado.php">Agregar empleado</a>
                            </div>
                        </div>
                        <hr>
                        </div>

                        <div class="card">
                            <div class="card-body" id="card-employe-body" style="height:30rem; display:flex; flex-direction:column; justify-content:center; align-items:center">
                                <div class="row">
                                    <img src="./src/img/leaf.png" alt="hojas" style="width: 10rem;"><br>
                                </div>
                                <div class="row mt-3">
                                    <span style="font-size: 20px;">Sin empleados registrados</span>
                                </div>
                            </div>
                        </div>

                        <div class="overlay hidden" id="overlay"></div>
                        <div class="expanded-card hidden" id="expandedCard">
                            <div class="row">
                                <div class="col-11">
                                    <div class="row">
                                        <div class="col-3">
                                            <button class="btn btn-danger mb-3" id="closeCard">Atrás</button>
                                            <a href="editar-empleado.php" id="link-editar-empleado"><button class="btn btn-info mb-3" id="closeCard">Editar</button></a>
                                        </div>
                                        <div class="col-9 text-center">
                                            <h3 id="expandedName" class="text-center text-dark mt-3"></h3>
                                            <p id="puesto-empleado">Sistemas</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4 text-center">
                                            <img src="" class="employee-img border" onerror="this.src='./src/img/neumaticos/NA.JPG';" id="expandedImg" style="max-width: 200px;">
                                        </div>
                                        <div class="col-6 text-center">
                                        <p id="expandedDetails" class="text-center ml-5"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-1 border text-center" style="border-radius: 8px;">
                                    <a href="" id="link_ine" target="_blank" style="cursor:default;"><img id="doc_ine" src="src/img/documentos_iconos/ine.png" class="" data-toggle="tooltip" data-placement="top" title="Tooltip on top" style="width:35px; margin-bottom: 5px;" alt=""></a>
                                    <a href="" id="link_domicilio" target="_blank" style="cursor:default;"><img id="doc_domicilio" src="src/img/documentos_iconos/domicilio.png" class="" data-title="Comprobante de domicilio" style="width:35px; margin-bottom: 5px;" alt=""></a>
                                    <a href="" id="link_imss" target="_blank" style="cursor:default;"><img id="doc_imss" src="src/img/documentos_iconos/imss.png" class="" data-title="IMSS" style="width:45px; margin-bottom: 5px;" alt=""></a>
                                    <a href="" id="link_cv" target="_blank" style="cursor:default;"><img id="doc_cv" src="src/img/documentos_iconos/cv.png" class="" data-title="Currículum" style="width:35px; margin-bottom: 5px;" alt=""></a>
                                    <a href="" id="link_curp" target="_blank" style="cursor:default;"><img id="doc_curp" src="src/img/documentos_iconos/curp.png" class="" data-title="CURP" style="width:35px; margin-bottom: 5px;" alt=""></a>
                                    <a href="" id="link_contrato" target="_blank" style="cursor:default;"><img id="doc_contrato" src="src/img/documentos_iconos/contrato.png" class="" data-title="Contrato" style="width:35px; margin-bottom: 5px;" alt=""></a>
                                    <a href="" id="link_bancario" target="_blank" style="cursor:default;"><img id="doc_bancario" src="src/img/documentos_iconos/bancario.png" class="" data-title="Datos bancarios" style="width:35px; margin-bottom: 5px;" alt=""></a>
                                </div>

                            </div>
                            <div class="row mt-4">
                                <div class="col-2">
                                    <span>Estatus: <span id="estatus-empleado"></span></span>
                                </div>
                                <div class="col-10 text-right">
                                    <span style="margin-right: 10px;">
                                        <img src="src/img/birth.png" style="width:15px; margin-bottom: 5px; vertical-align: middle;" alt="">
                                        <b>Fecha de cumpleaños:</b> <span id="fecha_nacimiento"></span>
                                    </span>
                                    <span>
                                        <img src="src/img/ingreso.png" style="width:15px; margin-bottom: 5px; vertical-align: middle;" alt="">
                                        <b>Fecha de Ingreso:</b> <span id="fecha_ingreso"></span>
                                    </span>
                                </div>

                            </div>
                        </div>

                        <!-- <table id="empleados" class="table table-stripe table-hover">
                        </table> -->

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
    <script src="src/js/empleados/empleados.js"></script>




    <script>
 MostrarEmpleados()
    </script>

</body>

</html>