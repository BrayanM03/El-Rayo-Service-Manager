
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
 
	

    <title>Ventas totales</title>

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
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="src/vendor/bower_components/select2-bootstrap-theme/dist/select2-bootstrap.css">

    <!---Librerias de estilos-->
   
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <link rel="stylesheet" href="src/css/paginacionjs.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />

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
                <nav class="navbar navbar-expand navbar-info bg-info topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw icon-menu"></i>
                                <!-- Counter - Alerts -->
                                <span id="contador-notifi" class="badge badge-danger badge-counter">0</span>
                            </a>
 
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                   Notificaciones
                                </h6>
                                <div class="empty-notification">
                                 <img src="src/img/undraw_Notify_re_65on.svg" alt="" width="400px">
                                 <span>Ups, por el momento no hay nada por aqui</span> 
                              </div>
                              
                              <div id="contenedor-alertas"></div>
                              <a class="dropdown-item text-center small text-gray-500" href="#">Mostrar mas notificaciones</a>
                             
                            </div>
                        </li>

                     

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline  small" style="color: aliceblue;"><?php
                                
                                echo $_SESSION['nombre'] . " " . $_SESSION['apellidos'];
                            
                            ?></span>
                                <img class="img-profile rounded-circle"
                                    src="src/img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Perfil
                                </a>
                                <a class="dropdown-item" href="configuraciones.php?id=0&nav=configuraciones">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Configuraciones
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Registro de actividad
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar sesión
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

 
                <!-- Begin Page Content -->
                <div class="container-fluid">

                     <!-- Contenido inventario -->
                     <div class="row justify-content-center">
                        <div class="contenedor-tit col-md-3 text-center"> 
                        <div class="titulo-inventario" id="titulo-hv" rol="<?php echo $_SESSION["rol"] ?>">
                         <h5 style="margin: 10px 0px;">Sistema de busqueda</h5>
                         <p style="color: gray;">Elige un filtro de busqueda de Ventas totales</p>
                        </div>  
                        </div>
                    </div>

                        <div class="row justify-content-center mb-3">
                            <div class="col-12 col-md-5">
                                    <label for="filtro">Elegir filtro de busqueda</label>
                                    <select class="form-control" id="filtro" name="filtro" onchange="setFilters()">
                                        <option value="no aplica">Selecciona un filtro de busqueda</option>
                                        <option value="sucursal">Por sucursal</option>
                                        <option value="vendedor">Por vendedor</option>
                                        <option value="cliente">Por cliente</option>
                                        <option value="medidas">Por medidas</option>
                                    </select>
                            </div>
                        </div>

                        <div class="filters-area m-3"></div>


                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="list-group">
                                        <a href="#" class="list-group-item list-group-item-action active">
                                            <div class="row">
                                                <div class="col-12 col-md-1">#</div>
                                                <div class="col-12 col-md-1">Folio</div>
                                                <div class="col-12 col-md-1">Fecha</div>
                                                <div class="col-12 col-md-1">Sucursak</div>
                                                <div class="col-12 col-md-2">Vendedor</div>
                                                <div class="col-12 col-md-3">Cliente</div>
                                                <div class="col-12 col-md-1">Tipo</div>
                                                <div class="col-12 col-md-1">Total</div>
                                                <div class="col-12 col-md-1">Estatus</div>
                                            </div>
                                        </a>
                                        <div class="resultados" style="background-color:white">
                                            <a href="#" class="list-group-item list-group-item-action">
                                            <div class="row">
                                                <div class="col-12 col-md-12 text-center">Sin datos</div>
                                            </div>
                                            </a>
                                           
                                        </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12 cold-md-12 d-flex justify-content-center">
                            <div id="pagination-container"></div>
                            <!-- <nav aria-label="Page navigation example">
                                <ul class="pagination">
                                    <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                        <span class="sr-only">Anterior</span>
                                    </a>
                                    </li>

                                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                                    

                                    <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                        <span class="sr-only">Siguiente</span>
                                    </a>
                                    </li>
                                </ul>
                            </nav> -->
                            </div>
                        </div>
                      
                     
                          
                                        
                     </table>

                     
                   

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
    <script src="https://pagination.js.org/dist/2.1.5/pagination.js"></script>
    <script src="src/js/busqueda-avanzada.js"></script>
   
   
</body>

</html>