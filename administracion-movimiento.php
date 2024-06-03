
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
 
	

    <title>Movimiento <?php echo $_GET['id'] ?> | El Rayo Service Manager</title>

    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="src/css/inventario.css">
    <link rel="stylesheet" href="src/css/historial-ventas.css">

    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">


    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="src/vendor/datatables/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.10.111/web/pdf_viewer.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="src/vendor/bower_components/select2-bootstrap-theme/dist/select2-bootstrap.css">
    
    <!---Librerias de estilos-->
    
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <style>
        #content-wrapper{
            font-size: 12px !important;
        }
        .swal2-popup {
        font-size: 12px !important;
        font-weight: bold;
        font-family: Inherit, serif;
        }

        .toast-container{
            z-index: 999999999 !important;
        }
        .toast-success{
            background-color: #55A38B !important;
        }
        .toast-error{
            background-color: tomato !important;
        }

        .swal2-container {
        z-index: 99 !important;
        }
        .btn-x-documento{
            font-size: 2.5rem;
            font-weight: 500;
            color: white;
            -webkit-text-stroke: 2px black;
            font-weight: bold;
            position: absolute;
            right: 10rem;
            top: .3rem;
            cursor: pointer;
         }
         .btn-x-documento:hover{
            color: red;
         }
         .contenedor-cargar-documento{
            width: 19rem;
            border-radius: 9px;
            height: 10rem;
            margin-left: auto;
            margin-right: auto;
            border: 2px dashed #D4D1D1;
            font-size: larger;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: whitesmoke;
            color: gray;
            
         }

         .contenedor-cargar-documento-edicion{
            width: 19rem;
            border-radius: 9px;
            height: 10rem;
            margin-left: auto;
            margin-right: auto;
            border: 2px dashed #D4D1D1;
            font-size: larger;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background-color: white;
            color: black;
         }
         .contenedor-cargar-documento-edicion:hover{
            font-weight: bolder;
            color: green;
            border: 2px dashed #59EB32;
         }

         #thumbnailCanvas:hover{
            border: 2px dashed #55A38B;
            cursor: pointer;
         }
    </style>
</head>
<body id="page-top"> 

    <?php include 'views/loader.php'; ?>
    <div class="container"></div>
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
                <div class="d-none" id="titulo-hv" sucursal="<?php echo $_SESSION['sucursal']?>" id_sucursal="<?php echo $_SESSION['id_sucursal']?>" rol="<?php echo $_SESSION['rol'] ?>" id_usuario="<?php echo $_SESSION['id_usuario'] ?>"  nombre_usuario="<?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellidos'] ?>"></div>
                <div class="container-fluid" >
                <div class="row">
                            <div class="col-md-6">
                                <h3><b> Movimiento <?php echo $_GET['id']?></b></h3>
                            </div>
                </div>
                <hr>
                
                <div class="row">
                            <div class="col-md-6">
                                <h5><b>Desde este panel puede editar los datos generales del folio de ingreso</b></h5>
                            </div>
                            <div class="col-md-3 d-flex">
                                <h5 class="mt-2">Buscar: </h5>
                                <input type="text" class="form-control ml-3" id="buscar-folio-factura" placeholder="Folio ó Factura">
                                <div class="btn btn-info ml-2" onclick="buscarFoliosCuentasPorPagar()">Buscar</div>
                            </div>
                            <div class="col-md-3" id="contenedor-botones-edicion">
                                <div class="row justify-content-end">
                                    <div class="col-12 col-md-4">
                                    <?php if($_SESSION['id_usuario'] != 22 && $_SESSION['id_usuario'] != 15){ ?>
                                        <div class="btn btn-primary w-100" onclick="actualizarDatosGenerales(0)">Editar</div>
                                    <?php }?>
                                    </div>
                                </div>
                            </div>   
                </div>
                <div id="contenedor-datos-generales">
                    <div class="row">
                            <div class="col-md-8 col-12">
                                <div class="row mt-5">
                                    <div class="col-12 col-md-4" style="font-size: 14px !important;">
                                        <label for="proveedor"><b>Proveedor</b></label>
                                        <select class="form-control" id="proveedor" placeholder="Proveedor" disabled>
                                        <option value="">Seleccione un proveedor</option>
                                        </select>  
                                    </div>
                                    <div class="col-12 col-md-4" style="font-size: 14px !important;">
                                        <label for="factura"><b>Factura</b></label>
                                        <input type="text" class="form-control" id="factura" placeholder="Folio" disabled>
                                    </div>
                                    <div class="col-12 col-md-4" style="font-size: 14px !important;">
                                        <label for="usuario"><b>Usuario</b></label>
                                        <select name="usuario" class="form-control" id="usuario" disabled>
                                                <option value="">Seleccione un usuario</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-3 mb-3">
                                    <div class="col-12 col-md-4" style="font-size: 14px !important;">
                                            <label for="estado-factura"><b>Estado de la factura</b></label>
                                            <select class="form-control" id="estado-factura" disabled>
                                                <option value="">Seleccione una opción</option>
                                                <option value="1">Sin factura</option>
                                                <option value="2">Factura completa</option>
                                                <option value="3">Factura incompleta</option>
                                                <option value="4">Factura pagada</option>
                                            </select>    
                                    </div>
                                    <div class="col-12 col-md-4" style="font-size: 14px !important;">
                                            <label for="estatus"><b>Estatus del movimiento</b></label>
                                            <select class="form-control" id="estatus" disabled>
                                                <option value="">Seleccione una opción</option>
                                                <option value="Completado">Completado</option>
                                                <option value="Pendiente">Pendiente</option>
                                                <option value="Cancelada">Cancelado</option>
                                            </select> 
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <label>Sucursal</label>
                                        <select class="form-control" id="sucursales" name="sucursales" disabled>
                                            <option value="0">Bodega</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-12">
                                    <div class="row mt-5">
                                        <div class="col-md-12 text-center">
                                            <div class="contenedor-pdf">
                                                <div id="area-canvas"></div>
                                                <input class="d-none" name="archive" type="file" id="input-comprobante-edicion" onchange="cargarComprobanteRegistro()">
                                            </div>
                                        </div>
                                    </div>
                            </div>

                    </div>

                    
                    <hr class="mt-3">
                    <div class="row">
                            <div class="col-md-10">
                                <h5><b>Administración</b></h5>
                            </div> 
                    </div>
                    <div class="row">
                            <div class="col-12 col-md-3">
                                <label for=""><b>Importe total</b></label>
                                <input type="number" id="importe" class="form-control" disabled>
                            </div>  
                            <div class="col-12 col-md-3">
                                <label for=""><b>Pagado</b></label>
                                <input type="number" id="pagado-total" class="form-control" disabled>
                            </div> 
                            <div class="col-12 col-md-3">
                                <label for=""><b>Restante</b></label>
                                <input type="number" id="restante-total" class="form-control" disabled>
                            </div>  
                    </div>
                </div>
                    <hr>
                    <div class="row">
                            <div class="col-md-9">
                                <h5><b>Material en las partidas</b></h5>
                            </div>
                            <div class="col-md-3">
                                <div class="row justify-content-end">
                                    <div class="col-12 col-md-8">
                                        <?php if($_SESSION['id_usuario'] != 22 && $_SESSION['id_usuario'] != 15){ ?>
                                        <div class="btn btn-primary w-100" onclick="modalAgregarLlantas()">Agregar llantas</div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>   
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <table class="table mt-4">
                                <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>Cantidad</th>
                                        <th>Descripción</th>
                                        <th>Marca</th>
                                        <th>Ubicación</th>
                                        <th>Destino</th>
                                        <th>Stock anterior</th>
                                        <th>Stock actual</th>
                                        <th>Costo</th>
                                        <th>Importe</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-llantas-remision" style="background-color: white !important;">

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-12 col-md-6">
                            <label for="">Mercancia</label>
                            <textarea class="form-control" id="mercancia" cols="30" rows="5" disabled></textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="">Descripción</label>
                            <textarea class="form-control" id="descripcion-remision" cols="30" rows="5" disabled></textarea>
                        </div>
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

    <script>
        // Seleccionamos el input
        const input = document.getElementById('buscar-folio-factura');

        // Agregamos un evento 'keydown' al input
        input.addEventListener('keydown', (event) => {

        // Si la tecla presionada es Enter (código 13)
        if (event.keyCode === 13) {

            // Ejecutamos la función
            buscarFoliosCuentasPorPagar()
        }
        });
    </script>

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
    <!-- <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script> -->
    <script src="src/js/vue.min.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script src="src/vendor/datatables/jquery.dataTables.min.js"></script> 
    <script src="src/vendor/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="src/vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.10.111/build/pdf.min.js"></script>
    <script src="src/vendor/datatables/defaults.js"></script>
    <script src="src/vendor/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="src/js/bootstrap-select.min.js"></script>
    <script src="src/js/administracion-movimiento.js"></script>
   
</body>

</html>