<?php
session_start();

include 'modelo/conexion.php';
$con = $conectando->conexion();


if (!isset($_SESSION['id_usuario'])) {
    header("Location:login.php");
}

if ($_SESSION['rol'] == 3 ) {
    header("Location:nueva-venta.php?nav=ventas");
}
if ($_SESSION['rol'] == 5) {
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

    <title>Garantias | El Rayo Service Manager</title>

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
            height: 37.99px !important;
            border-radius: 6px !important;
            /* border: 1px solid #CDD9ED !important; */
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
                        <div class="row mt-3 mb-3">
                            <div class="col-md-6">
                                <h5><b>Garantias activas</b></h5>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-1">
                                <label>Folio</label>
                                <input id="folio" placeholder="0" class="form-control"></input>
                            </div>
                            <div class="col-3">
                                <label>Cliente</label><br>
                                <select id="cliente_" class="form-control selectpicker" data-live-search="true" multiple></select>
                            </div>
                            <div class="col-4">
                                <label>Llanta</label>
                                <select id="llanta" class="form-control selectpicker" data-live-search="true" multiple></select>
                            </div>
                            <div class="col-2">
                                <label>Proveedor</label>
                                <select id="proveedor" class="form-control selectpicker" data-live-search="true" multiple>
                                    <option value="">Selecciona un proveedor</option>
                                <?php

                                $querySucu = "SELECT COUNT(*) FROM proveedores";
                                $resps = $con->prepare($querySucu);
                                $resps->execute();
                                $resps->bind_result($total_sucu);
                                $resps->fetch();
                                $resps->close();

                                if($total_sucu > 0) {
                                    $querySuc = "SELECT * FROM proveedores";
                                    $respon = mysqli_query($con, $querySuc);



                                    while ($rows = $respon->fetch_assoc()) {
                                        $suc_identificador = $rows['id'];
                                        $nombre_suc = $rows['nombre'];

                                        echo "<option value='". $suc_identificador."'>".$nombre_suc."</option>";
                                    }
                                }

                                ?>
                                </select>
                            </div>
                            <div class="col-1">
                                <label>Fecha inicio</label>
                                <input type="date" id="fecha-inicio" class="form-control"></input>
                            </div>
                            <div class="col-1">
                                <label>Fecha fin</label>
                                <input type="date" id="fecha-fin" class="form-control"></input>
                            </div>

                           
                           
                        </div>
                        <div class="row mb-3">
                            
                            <div class="col-2">
                                <label>Estatus fis.</label>
                                <select id="estatus-fisico_" class="form-control selectpicker" multiple>
                                <option value=''>Selecciona un estatus</option>
                                <option value="1">Recibida por el vendedor</option>
                                <option value="2">Recibida por dep. garantias</option>
                                <option value="3">Entregado a proveedor</option>
                                <option value="4">Entregado de proveedor a dep. garantias</option>
                                <option value="5">Entregado a cliente</option>
                                </select>
                            </div>
                            <div class="col-1">
                                <label>Dictamen</label>
                                <select id="dictamen_" class="form-control selectpicker" multiple>
                                <option value="">Selecciona un dictmen</option>
                                <option value="pendiente">Pendiente</option>
                                  <option value="entregado">Entregado al proveedor</option>
                                  <option value="procedente">Procedente</option>
                                  <option value="improcedente">Improcedente</option>
                                  <option value="concluido">Concluido</option>
                                </select>
                            </div>
                            <div class="col-1">
                                <label>RAY</label>
                                <input placeholder="Folio RAY" id="ray" class="form-control"></input>
                            </div>
                            <div class="col-2">
                                <label>Factura</label>
                                <input placeholder="Factura proveedor" id="factura" class="form-control"></input>
                            </div>
                            <div class="col-2">
                                <label>Serie</label>
                                <input placeholder="Serie" id="serie_" class="form-control"></input>
                            </div>
                            <div class="col-1">
                                <label>DOT fecha fab.</label>
                                <input type="text" placeholder="Ejem. 1RYF7HFHC" id="dot-fabricacion" class="form-control"></input>
                            </div>
                            <div class="col-1">
                                <label>DOT produccion</label>
                                <input type="text" placeholder="Ejem.2724 " id="dot-produccion" class="form-control"></input>
                            </div>
                            <div class="col-1" style="margin-top: 30px">
                               <div class="btn btn-primary" onclick="buscarGarantias()">Hacer busqueda</div>
                            </div>
                            <div class="col-1" style="margin-top: 30px">
                               <div class="btn btn-info">Nueva garantia</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                        
                        </div>

                        <table id="garantias" style="font-size: 12px;" class="table table-striped table-bordered table-hover">
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
    <script src="src/js/garantias.js"></script>

</body>

</html>