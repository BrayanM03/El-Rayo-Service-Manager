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

    <title>Nueva Venta</title>

    <!-- Custom fonts for this template-->
 
    <link rel="stylesheet" href="src/css/nueva-venta.css">
    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
        <link href='https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.css' rel='stylesheet' />

    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="src/vendor/datatables/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="src/vendor/datatables-responsive/css/responsive.bootstrap4.min.css">
   
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="src/vendor/bower_components/select2-bootstrap-theme/dist/select2-bootstrap.css">

    <!---Librerias de estilos-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />

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
            <li class="nav-item active">
                <a class="nav-link" href="#">
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
                        <a class="collapse-item" href="utilities-border.html">
                            <img src="src/img/compras.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Compras</span>
                        </a>
                    </div>
                </div>
            </li>


            <?php 
                $user_jerarquia = $_SESSION["rol"];

                if ($user_jerarquia == 1) {
                   $name = "Inventario";
                }else if($user_jerarquia ==2){
                 $name = "Clientes y creditos";
                }

            ?>

            <!-- Divider -->
            <hr class="sidebar-divider">
            <div class="sidebar-heading">
            <?php echo $name   ?>
            </div>
            <?php 

                if ($user_jerarquia == 1) {
                    # code...
                

            ?>


            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTyres"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Mis llantas</span>
                </a>
                <div id="collapseTyres" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
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
                        <a class="collapse-item" href="register.html" style="display:flex; flex-direction: row; justify-content:start;">
                            <img src="src/img/salida.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Vendidas</span> </a>
                        <a class="collapse-item" href="forgot-password.html">
                            <img src="src/img/entrada.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Entradas</span></a>
                        </a>
                    
                    </div>
                </div>
            </li>


            <?php }
            
            if ($user_jerarquia == 1 || $user_jerarquia == 2) {
            ?>


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
                        <a class="collapse-item" href="forgot-password.html">
                            <img src="src/img/pago.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Creditos vencidos</span></a> 
                        </a>
                     
                    </div>
                </div>
            </li>


          <!---  <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProvider"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-user-cog"></i>
                    <span>Mis provedores</span>
                </a>
                <div id="collapseProvider" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Categorias:</h6>
                        <a class="collapse-item" href="clientes.php" style="display:flex; flex-direction: row; justify-content:start;">
                            <img src="src/img/tyre-invent.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Clientes</span> </a>
                        <a class="collapse-item" href="creditos.php" style="display:flex; flex-direction: row; justify-content:start;">
                            <img src="src/img/salida.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Creditos</span> </a>
                        <a class="collapse-item" href="forgot-password.html">
                            <img src="src/img/entrada.svg" width="18px" /> 
                            <span style="margin-left:12px;"> Creditos vencidos</span></a>
                        </a>
                    
                    </div>
                </div>
            </li>  -->

            <?php }
            
            if ($user_jerarquia == 1 ) {
            ?>
            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="generar-token.php">
                    <i class="fas fa-fw fa-lock"></i>
                    <span>Generar token</span></a>
            </li>

           
            <?php 
              }     # code...
                

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
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 7, 2019</div>
                                        $290.29 has been deposited into your account!
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 2, 2019</div>
                                        Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                <span class="badge badge-danger badge-counter">7</span>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Message Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="src/img/undraw_profile_1.svg"
                                            alt="">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div class="font-weight-bold">
                                        <div class="text-truncate">Hi there! I am wondering if you can help me with a
                                            problem I've been having.</div>
                                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="src/img/undraw_profile_2.svg"
                                            alt="">
                                        <div class="status-indicator"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">I have the photos that you ordered last month, how
                                            would you like them sent to you?</div>
                                        <div class="small text-gray-500">Jae Chun · 1d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="src/img/undraw_profile_3.svg"
                                            alt="">
                                        <div class="status-indicator bg-warning"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Last month's report looks great, I am very happy with
                                            the progress so far, keep up the good work!</div>
                                        <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60"
                                            alt="">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                                            told me that people say this to all dogs, even if they aren't good...</div>
                                        <div class="small text-gray-500">Chicken the Dog · 2w</div>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
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
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->


                <!-- Begin Page Content -->
                <div class="container-fluid" style="display: flex; justify-content: center; ">

                     <!-- Contenido Nueva venta -->

                     <div class="card" style="margin-bottom: 7vh; padding-bottom: 5vh; padding-right: 30px;">
                        
                             <h3 class="titulo-nueva-venta">Nueva venta</h3>
                         
                         <div class="card-body">
                            <div class="row">

                            <div class="grupo-1 col">
                            
                                                <div class="logo-marca-grande">

                                                </div>

                                                <!--Fila 1-->
                                                <form id="form-punto-venta">

                                                <div class="fila1">
                                                        <label class="input-largo">
                                                            <span for="search">Busqueda</span>
                                                            <input type="text" id="search" name="search" class="input-group">
                                                        </label> 
                                                        <div class="contenedor-tabla oculto">
                                                                <table class="table" id="table-llantas-mostradas">
                                                                <thead class="table-info">
                                                                <tr>
                                                                    <th>Codigo</th>
                                                                    <th>Descripción</th>
                                                                    <th>Modelo</th>
                                                                    <th>Costo</th>
                                                                    <th>Costo c/Desc</th>
                                                                    <th>Marca</th>
                                                                    <th>Sucursal</th>
                                                                    <th>Stock</th>
                                                                </tr>
                                                                </thead> 
                                                                <tbody class="tbody">
                                                                </tbody> 
                                                                </table>
                                                        </div>

                                                      
                                                        <select id="clientes" name="clientes" class="form-control"> 
                                                            
                                                         </select>

                                                                              
                                                        

                                                         <select id="metodos-pago" name="clientes" class="form-control"> 
                                                                <option disabled selected value></option>
                                                                <option value="0">Efectivo</option>
                                                                <option value="1">Tarjeta</option>
                                                                <option value="2">Transferencia</option>
                                                                <option value="3">Cheque</option>
                                                                <option value="4">Sin definir</option>
                                                         </select>
                                                  

                                                        <label class="no-editable">
                                                            <span for="description">Descripción</span>
                                                            <input style="cursor: not-allowed;color:#696969;" type="text" id="description" name="description" class="input-group" disabled>
                                                        </label>
                                                        
                                                </div> 
                                                
                                                <!--Fila 1-->

                                                <div class="fila2 row">
                                                    
                                                    <label class="no-editable-corto">
                                                        <span for="modelo">Modelo</span>
                                                        <input type="text" style="cursor: not-allowed; color:#696969;" id="modelo" name="modelo" class="input-group" disabled>
                                                    </label>

                                                    <label class="no-editable-corto">
                                                         <span for="sucursal" >Sucursal</span>
                                                         <select style="cursor: not-allowed; color:#696969;" id="sucursal" name="sucursal" class="select-group form-select" disabled> 
                                                                <option disabled selected value></option>
                                                                <option value="0">Pedro Cardenas</option>
                                                                <option value="1">Sendero</option>
                                                         </select>
                                                    </label>

                                                    <label class="input-corto">
                                                        <span for="cantidad">Cantidad</span>
                                                        <input type="number" id="cantidad" name="cantidad" class="input-group" required>
                                                    </label>
                                        
                                                    <label class="input-corto precio-pointer" id="precio-tok" onclick="generarToken();">
                                                        <span for="precio" class="precio-pointer">$ Precio</span>
                                                        <input type="number" id="precio" name="precio" class="input-group precio-pointer" disabled>
                                                    </label>

                                                    <label style="border-color: transparent;">
                                                    <div class="btn btn-info" onclick="agregarcliente();" id="btn-add-client" style="width: 50px;margin: 8px 10px;"><i style=" width: 20px;" class="fas fa-user-plus"></i></div>
                                                              

                                                    <div class="btn btn-info" rol="<?php echo $_SESSION['rol']; ?>" sucursal="<?php echo $_SESSION['sucursal']; ?>"  id="agregar-producto" onclick="agregarInfo()" >Agregar</div>
                                                    </label>
                                                    
                                                    <div id="help-addclient-span" class="targeta-ayuda">
                                                       <div class="card text-white border-info">
                                                       <div class="card-header bg-info">Agregar un cliente</div>
                                                       <div class="card-body bg-white text-info" >
                                                       <p class="card-text">Con este boton pueden agregar un cliente a la base de datos</p>
                                                       </div>
                                                    </div> 
                                                    </div> 

                                                </div>
                                                 
                                                </form>
                                        
                                </div>


                            <div class="grupo-2 col">

                                <div class="fila3">
                                            <div class="folio-fecha">
                                                
                                                <label>
                                                    <span for="fecha">Fecha</span>
                                                    <input class="form-control"  type="date" id="fecha" name="fecha" style="width: 150px;"> 
                                                </label>
                                            </div>  
                                </div>
                                    
                                    
                                    <div class="fila4 row">
                                        
                                                <table id="pre-venta" class="table table-striped table-bordered table-hover">
                                           
                                                </table>
                                        
                                        
                                                
                                                <div class="row" style="width: 100%; display: flex; justify-content: center; align-items: center; margin-top:25px">
                                                    <div class="form-group" style="display: flex; align-items: center; justify-content: space-around; width: 25%;" >
                                                        <span for="total" >Total: $</span>
                                                        <input type="number" value="0" class="form-control" id="total" name="total" style="width:120px; margin-left:5px; display:flex; justify-content:center;" disabled> 
                                                
                                                    </div>
                                                    </div>
                                                <div class="botones-de-venta" style="margin-top: 8px;">
                                                    <div class="btn btn-warning" onclick="limpiarTabla();" style="color: rgb(31, 28, 28); margin-right: 2vh;" id="limpiar-venta">Limpiar</div>
                                                    <div class="btn btn-success" onclick="realizarVenta();" id="realizar-venta">Realizar venta</div>
                                                    <div class="btn btn-primary" onclick="realizarVentaCredito();" style="margin-left: 2vh;" id="realizar-venta">Realizar venta a credito</div>
                                                </div>   
                                                
                                    
                                    </div>
                                </div>

                            </div> <!---Fin row-->

                                
                              
                                
                            
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
    
    <script src="src/js/nueva-venta.js"></script>
    <script src="src/vendor/datatables/defaults.js"></script>
    <script src="src/vendor/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.min.js"></script>
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.css" type="text/css">

    <!--<script src="src/js/agregar-info-tabla-venta.js"></script>-->
    <script src="src/js/agregar-product-temp.js"></script>
    <script src="src/js/generar-token.js"></script>
    <script src="src/js/nueva-venta-credito.js"></script>
  <!--  <script src="src/js/notificaciones.js"></script>   -->
   
   
</body>

</html>