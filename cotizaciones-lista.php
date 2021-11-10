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
 
	

    <title>Historial de cotizaciones | El rayo service manager</title>

    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="src/css/inventario.css">
    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">


    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="src/vendor/datatables/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="src/vendor/datatables-responsive/css/responsive.bootstrap4.min.css">

  <link rel="stylesheet" href="https://nightly.datatables.net/colreorder/css/colReorder.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="src/vendor/bower_components/select2-bootstrap-theme/dist/select2-bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css">
    <!---Librerias de estilos-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <link href="src/css/tabla.css" rel="stylesheet">
   

</head>

<body id="page-top"> 

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-dark sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <!-- <i class="fas fa-laugh-wink"></i>--->
                    <img style="filter: invert(100%);" width="40px" src="src/img/racing.svg"/>
                </div>
                <div class="sidebar-brand-text mx-3">El Rayo<sup style="font-size:12px; margin-left:5px;">app</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Inicio</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Punto de venta
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="nueva-venta.php">
                    <i class="fas fa-fw fa-cart-plus"></i>
                    <span>Nueva venta</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="cotizacion.php">
                    <i class="fas fa-fw fa-clipboard"></i>
                    <span>Nueva cotización</span>
                </a>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-history"></i>
                    <span>Historial</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Ordenes de:</h6>
                        <a class="collapse-item" href="ventas-pv.php">
                            <img src="src/img/ventas.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Ventas</span>
                        </a>
                        <a class="collapse-item" href="cotizaciones-lista.php">
                            <img src="src/img/compras.svg" width="18px" /> 
                            <span style="margin-left:12px;">Cotizaciones</span>
                        </a>
                    </div>
                </div>
            </li>
            <?php 
                $user_jerarquia = $_SESSION["rol"];

                if ($user_jerarquia == 1) {
                    # code...
                

            ?>
            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Inventario
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item active">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTyres"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Mis llantas</span>
                </a>
                <div id="collapseTyres" class="collapse show" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Sucursales:</h6>
                        <a class="collapse-item" href="inventario-pedro.php" style="display:flex; flex-direction: row; justify-content:start;">
                        <i class="fas fa-fw fa-store"></i> 
                            <span style="margin-left:12px;"> Pedro Cardenas</span> </a>
                        <a class="collapse-item" href="inventario-sendero.php" style="display:flex; flex-direction: row; justify-content:start;">
                        <i class="fas fa-fw fa-store"></i>
                            <span style="margin-left:12px;"> Sendero</span> </a>
    
                    
                    </div>
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Stock total:</h6>
                        <a class="collapse-item" href="inventario-total.php" style="display:flex; flex-direction: row; justify-content:start;">
                            <img src="src/img/tyre-invent.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Existencia</span> </a>
                            <a class="collapse-item" href="servicios.php" style="display:flex; flex-direction: row; justify-content:start;">
                            <i class="fas fa-car"></i>
                            <span style="margin-left:7px;">Servicios</span> </a>
                        <a class="collapse-item" href="movimientos.php">
                            <img src="src/img/entrada.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Movimientos</span></a>
                        </a>
                    
                    </div>
                </div>
            </li>


            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseClients"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-user-tag"></i>
                    <span>Mis clientes</span>
                </a>
                <div id="collapseClients" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Categorias:</h6>
                        <a class="collapse-item" href="clientes.php" style="display:flex; flex-direction: row; justify-content:start;">
                            <img src="src/img/cliente.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Clientes</span> </a>
                        <a class="collapse-item" href="creditos.php" style="display:flex; flex-direction: row; justify-content:start;">
                            <img src="src/img/credito.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Creditos</span> </a>
                      <!--   <a class="collapse-item" href="forgot-password.html">
                            <img src="src/img/pago.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Creditos vencidos</span></a>
                        </a> -->
                    
                    </div>
                </div>
            </li>


           <!--  <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProvider"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-user-cog"></i>
                    <span>Mis provedores</span>
                </a>
                <div id="collapseProvider" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Categorias:</h6>
                        <a class="collapse-item" href="login.html" style="display:flex; flex-direction: row; justify-content:start;">
                            <img src="src/img/tyre-invent.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Clientes</span> </a>
                        <a class="collapse-item" href="register.html" style="display:flex; flex-direction: row; justify-content:start;">
                            <img src="src/img/salida.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Creditos</span> </a>
                        <a class="collapse-item" href="forgot-password.html">
                            <img src="src/img/entrada.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Creditos vencidos</span></a>
                        </a>
                    
                    </div>
                </div>
            </li> -->


            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="generar-token.php">
                    <i class="fas fa-fw fa-lock"></i>
                    <span>Generar token</span></a>
            </li>

           

            <?php 
              }else{}  

            ?>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <!-- Sidebar Message --> 
            <div class="sidebar-card">
                <img class="sidebar-card-illustration mb-2" src="src/img/logo.jpg" alt="" style="border-radius: 8px;">
                <p class="text-center mb-2"><strong>El Rayo Servce Manager</strong><br> Es un sistema de gestion de inventario. punto de venta y facturación.</p>
                <a class="btn btn-success btn-sm" href="https://startbootstrap.com/theme/sb-admin-pro">Ir a sitio web!</a>
            </div>

        </ul>
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
                                <a class="dropdown-item" href="configuraciones.php">
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


                <!-- Begin Page Content style="display: flex; justify-content: center; align-items:center; flex-direction:column" -->
                <div class="containe" style="width:80%; margin:auto;"> 

                     <!-- Contenido inventario -->
                     <div class="titulo-inventario m-auto">
                         <h5 style="margin: 10px 0px;">Historial de cotizaciones</h5>
                         <p style="color: gray;">Esta el la lista de las cotizaciones que se han generado</p>
                        </div>
                        
                      <div class="botones">
                                    <a href="cotizacion.php" class="btn btn-success btn-icon-split">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-plus-circle"></i>
                                        </span>
                                        <span class="text">Nueva cotizacion</span>
                                    </a>
                                  
                      </div>
                      <table id="lista-cotizaciones"  class="table table-bordered table-hover">  
                          <thead>
                              <tr>
                                  <td>#</td>
                                  <td>Fecha</td>
                                  <td>Usuario</td>
                                  <td>Cliente</td>
                                  <td>Total</td>
                                  <td>Estatus</td>
                                  <td>Hora</td>
                                  <td>Comentario</td>
                                  <td>Acción</td>
                              </tr>
                          </thead>                 
                     </table>

                     
                   

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
    <script src="src/vendor/datatables/defaults.js"></script>
    <script src="src/vendor/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="src/vendor/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

  <script src="https://nightly.datatables.net/colreorder/js/dataTables.colReorder.min.js"></script>
    <script src="src/vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

     <!-- Scripts para exportar archivos de tablas  -->        
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>   
     <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
     <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script> 
     <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.colVis.min.js"></script>
    <script src="src/js/historial-cotizaciones.js"></script>
    
   
   
</body>

</html>