<?php
session_start();

include 'modelo/conexion.php';
$con = $conectando->conexion();
date_default_timezone_set("America/Matamoros");


if (!isset($_SESSION['id_usuario'])) {
    header("Location:login.php");
}

if ($_SESSION['rol'] == 3 || $_SESSION['rol'] == 2 || $_SESSION['id_usuario'] == 7) {
    header("Location:nueva-venta.php?nav=inicio&id=0");
}

if ($_SESSION['rol'] == 5) {
    header('Location:ventas-pv.php?id=0&nav=ventas');
}

if ($_SESSION['rol'] == 4) {
    header("Location:inventario.php?id=1&nav=inv");
}


$querySuc = "SELECT COUNT(*) FROM sucursal";
$resp = $con->prepare($querySuc);
$resp->execute();
$resp->bind_result($total_suc);
$resp->fetch();
$resp->close();
$sucursales_array = array();
if ($total_suc > 0) {
    $querySuc = "SELECT * FROM sucursal";
    $resp = mysqli_query($con, $querySuc);

    while ($row = $resp->fetch_assoc()) {
        $suc_identificador = $row['id'];
        $nombre = $row['nombre'];

        $tarer_colores = $con->prepare("SELECT color_out, color_hover, color_sweet FROM `colores_sucursales` WHERE id_suc = ?");
        $tarer_colores->bind_param('i', $suc_identificador);
        $tarer_colores->execute();
        $tarer_colores->bind_result($background, $hover, $sweet);
        $tarer_colores->fetch();
        $tarer_colores->close();
        $sucursales_array[] = array("nombre"=>$nombre, 'id_sucursal'=>$suc_identificador, 'background'=>$background, 'hover'=>$hover, 'sweet'=>$sweet); 
        //echo '<a class="dropdown-item" bandera_fusionar="0" style="cursor: pointer;" onclick="graficaAreaPorSucursal(' . $suc_identificador . ', `' . $background . '`, `' . $hover . '`,  `' . $sweet . '`,  `' . $nombre . '`); marcarSeleccionado(this, `lista-suc-graph-area-ventas`);" chart_sucursal_id="' . $suc_identificador . '" chart_background_color="' . $background . '" chart_hover_color="' . $hover . '" chart_sweet_color="' . $sweet . '" nombre_sucursal="' . $nombre . '">' . $nombre . '</a>';
    }
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

    <title>El Rayo | Service Manager</title>

    <!-- Custom fonts for this template-->
    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="src/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <style>
        .my-vertical-tab {
            margin-bottom: 1rem;
            margin-top: 1rem;
            margin-right: 0 !important;
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 500;
            color: #6B7280;
            list-style: none;
            padding: .5rem;

            @media (min-width: 768px) {
                margin-bottom: 0;
            }
        }

        .my-vertical-tab li {
            margin-bottom: .5rem;
        }

        .my-vertical-tab li a {
            text-decoration: none;
        }

        .selected-a {
            display: inline-flex;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            padding-left: 1rem;
            padding-right: 1rem;
            align-items: center;
            border-radius: 0.5rem;
            width: 100%;
            color: #ffffff;
            background-color: #F59E0B;
            cursor: pointer;
        }

        .selected-a:hover {
            background-color: #f5c20b;
            color: #ffffff;
        }

        .my-menu-a {
            display: inline-flex;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            padding-left: 1rem;
            padding-right: 1rem;
            align-items: center;
            border-radius: 0.5rem;
            width: 100%;
            background-color: white;
            cursor: pointer;
        }

        .my-menu-a:hover {
            color: #111827;
            background-color: #F3F4F6;
        }

        .vertical-menu-main-container {
            padding: 1.5rem;
            border-radius: 0.5rem;
            width: 100%;
            color: #6B7280;
            background-color: white;
        }

        .titulo-vertical-menu {
            margin-bottom: 0.5rem;
            font-size: 1.125rem;
            line-height: 1.75rem;
            font-weight: 700;
            color: #111827;

        }

        .dato-circulo {
            align-items: center;
            justify-content: center;
            display: flex;
            margin: .60rem;
            padding: 0.75rem;
            border: 1px solid #2563EB;
            background-color: rgb(0, 255, 255, .2);
            width: 8rem;
            height: 7.7rem;
            /* width: 73%;
            height: 122%;  */
            border-radius: 50%;
            font-size: 11px;
        }

        .opcion-seleccionada::after {
            content: "\2713";
            /* Código Unicode para una marca de verificación */
            float: right;
            /* Alinear a la derecha */
            color: green;
            /* Color de la marca de verificación */
        }

        .animacion_deslizar {
            animation-name: slide-bottom;
            animation-duration: 1s;
        }

        @-webkit-keyframes slide-bottom {
            0% {
                -webkit-transform: translateY(0);
                transform: translateY(0)
            }

            100% {
                -webkit-transform: translateY(100px);
                transform: translateY(100px)
            }
        }

        @keyframes slide-bottom {
            0% {
                -webkit-transform: translateY(0);
                transform: translateY(0)
            }

            100% {
                -webkit-transform: translateY(100px);
                transform: translateY(100px)
            }
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

                <!-- Topbar -->
                <?php include 'views/navbar.php'; ?>
                <!-- End of Topbar -->


                <!-- Begin Page Content -->
                <div class="container-fluid">
              
                    <!-- Page Heading -->

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h3 class="h3 mb-0 text-gray-800">Bievenido al panel <?php echo $_SESSION['nombre']; ?></h3>
                       <!--  <a href="ganancias-diarias.php?id=0&nav=ganancias_diarias" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm"><i class="fas fa-eye fa-sm text-white-50"></i> Ganancias diarias</a> -->
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" onclick="ejecutarPanelReporteVentas();"><i
                                class="fas fa-download fa-sm text-white-50"></i> Reporte de corte</a>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm" onclick="ejecutarPanelTipoComision();"><i
                                class="fas fa-download fa-sm text-white-50"></i> Reporte comisiones</a>
                    </div>

                    <div class="row mb-5 justify-content-center">
                        <div class="col-md-8 col-sm-12">
                            <img class="tyre-decoration-left" src="./src/img/tyre.svg" alt="insertar SVG con la etiqueta image" style="position: absolute; width:10rem; left:-70px; top:50px"> 
                                <div class="card border-left-success shadow h-100 py-2" style="z-index: 9 !important;">
                                        
                                    <div class="card-header d-flex flex-row align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-success"><span id="titulo-ventas-hoy">Montos totales (Todas las sucursales)</span></h6>
                                        <div class="dropdown no-arrow">
                                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" id="lista-suc-ventas-hoy" aria-labelledby="dropdownMenuLink">
                                                <div class="dropdown-header" id="sucursales-contenedor-ventas-hoy" id_sucursal_seleccionada="%" nombre_sucursal_seleccionada='General'>Sucursales:</div>
                                                <?php

                                                $querySuc = "SELECT COUNT(*) FROM sucursal";
                                                $resp = $con->prepare($querySuc);
                                                $resp->execute();
                                                $resp->bind_result($total_suc);
                                                $resp->fetch();
                                                $resp->close();

                                                if ($total_suc > 0) {
                                                    $querySuc = "SELECT * FROM sucursal";
                                                    $resp = mysqli_query($con, $querySuc);

                                                    while ($row = $resp->fetch_assoc()) {
                                                        $suc_identificador = $row['id'];
                                                        $nombre = $row['nombre'];

                                                        $tarer_colores = $con->prepare("SELECT color_out, color_hover, color_sweet FROM `colores_sucursales` WHERE id_suc = ?");
                                                        $tarer_colores->bind_param('i', $suc_identificador);
                                                        $tarer_colores->execute();
                                                        $tarer_colores->bind_result($background, $hover, $sweet);
                                                        $tarer_colores->fetch();
                                                        $tarer_colores->close();



                                                        echo '<a class="dropdown-item" href="#" onclick="cambiarSucursalVentasHoy('.$suc_identificador.', `'.$nombre.'`); marcarSeleccionado(this, `lista-suc-ventas-hoy`, true, '.$suc_identificador.')">' . $nombre . '</a>';
                                                    }
                                                }

                                                ?>
                                                <!-- <a class="dropdown-item" href="#" onclick="graficaBarPedroCardenas();">Pedro Cardenas</a> 
                                                <a class="dropdown-item" href="#" onclick="graficaBarSendero();">Sendero</a> -->
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="#" onclick="graficaBarGeneral();">Fusionar</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body" id="card-body-ventas-hoy">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-md-5">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Descripción</div>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <div class="text-sm mb-1 font-weight-bold text-gray-800">Efectivo</div>
                                                        </div>
                                                        <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <div class="text-sm mb-1 font-weight-bold text-gray-800">Tarjeta</div>
                                                        </div>
                                                        <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <div class="text-sm mb-1 font-weight-bold text-gray-800">Transferencia</div>
                                                        </div>
                                                        <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <div class="text-sm mb-1 font-weight-bold text-gray-800">Cheque</div>
                                                        </div>
                                                        <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                                                            
                                                        </div>
                                                    </div>
                                                    <!-- <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <div class="text-sm mb-1 font-weight-bold text-gray-800">Deposito</div>
                                                        </div>
                                                        <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                                                            
                                                        </div>
                                                    </div> -->
                                                    <div class="row mb-2">
                                                        <div class="col-12 col-md-6">
                                                            <div class="text-sm mb-1 font-weight-bold text-gray-800">Sin definir</div>
                                                        </div>
                                                        <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <div class="h5 mb-1 font-weight-bold text-gray-800">Total</div>
                                                        </div>
                                                        <div class="col-12 col-md-6 border-secondary pb-3" style="border-style:none none dotted none;">
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Ingreso</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="entrada-hoy-efectivo">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="entrada-hoy-tarjeta">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="entrada-hoy-transferencia">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="entrada-hoy-cheque">$0,00</div>
                                                    <!-- <div class="text-sm mb-1 font-weight-bold text-gray-800" id="entrada-hoy-deposito">$0,00</div> -->
                                                    <div class="text-sm mb-3 font-weight-bold text-gray-500" id="entrada-hoy-sin-definir">$0,00</div>
                                                    <div class="h5 mb-1 font-weight-bold text-gray-500" id="entrada-hoy-total">$0,00</div>
                                                </div>
                                                <div class="col-md-1 text-center">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Gastos</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="gasto-hoy-efectivo">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="gasto-hoy-tarjeta">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="gasto-hoy-transferencia">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="gasto-hoy-cheque">$0,00</div>
                                                    <!-- <div class="text-sm mb-1 font-weight-bold text-gray-800" id="gasto-hoy-deposito">$0,00</div> -->
                                                    <div class="text-sm mb-3 font-weight-bold text-gray-500" id="gasto-hoy-sin-definir">$0,00</div>
                                                    <div class="h5 mb-1 font-weight-bold text-gray-500" id="gasto-hoy-total">$0,00</div>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Importe</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="importe-hoy-efectivo">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="importe-hoy-tarjeta">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="importe-hoy-transferencia">$0,00</div>
                                                    <div class="text-sm mb-1 font-weight-bold text-gray-500" id="importe-hoy-cheque">$0,00</div>
                                                    <!-- <div class="text-sm mb-1 font-weight-bold text-gray-800" id="importe-hoy-deposito">$0,00</div> -->
                                                    <div class="text-sm mb-3 font-weight-bold text-gray-500" id="importe-hoy-sin-definir">$0,00</div>
                                                    <div class="h5 mb-1 font-weight-bold text-gray-500" id="importe-hoy-total">$0,00</div>
                                                </div>
                                            </div>
                                        </div>
                                </div>   
                            <img class="tyre-decoration-left" src="./src/img/tyre.svg" alt="insertar SVG con la etiqueta image" style="position: absolute; width:10rem; right:-70px; top:50px">              
                        </div>
                    </div>
                    <div class="row justify-content-center mb-5">
                        <div class="col-md-6 col-lg-5 text-center d-flex justify-content-center">
                            <div class="opciones-inferiores" id="opciones_inferiores" tipo_opcion='5'>
                                <ul>
                                    <li onclick="ventasRelizadasHoy()">Ventas realizadas</li>
                                    <li onclick="creditosAbiertosHoy()">Creditos abiertos</li>
                                    <li onclick="abonosRealizados()">Abonos realizados</li>
                                    <li onclick="gastosRealizados()">Gastos realizados</li>
                                    <li onclick="montosTotales()">Montos totales</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-3">
                    <!-- Content Row -->
                    <div class="row mt-5">
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="titulo-vertical-menu">Metricas generales</h3>
                                    <p class="mb-2">Ventas, creditos y dinero en mercancia y por cobrar este mes.</p>
                                </div>
                                <div class="col-md-12" style=" height:15rem">
                                    <!-- Earnings (Monthly) Card Example -->
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="card border-left-primary shadow h-100 py-2">
                                                <div onclick="notificarCreditosVencidos();" class="card-body">
                                                    <div class="row no-gutters align-items-center">
                                                        <div class="col mr-2">
                                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                                Entradas (Este mes)</div>
                                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="ganancias-mes-actual">$0,00</div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                          <!-- Earnings (Monthly) Card Example -->
                                            <div class="col-md-6 mb-4">
                                                <div class="card border-left-success shadow h-100 py-2">
                                                    <div class="card-body">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                                    Entradas (Anual)</div>
                                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="ganancia-anual-actual">$0,00</div>
                                                            </div>
                                                            <div class="col-auto">
                                                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    
                                    <div class="row">
                                        <!-- Earnings (Monthly) Card Example -->
                                        <div class="col-md-6 mb-4">
                                            <div class="card border-left-info shadow h-100 py-2">
                                                <div class="card-body">
                                                    <div class="row no-gutters align-items-center">
                                                        <div class="col mr-2">
                                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Ventas
                                                            </div>
                                                            <div class="row no-gutters align-items-center">
                                                                <div class="col-auto">
                                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800" id="total_ventas">0</div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Pending Requests Card Example -->
                                        <div class="col-md-6 mb-4">
                                            <div class="card border-left-warning shadow h-100 py-2">
                                                <div class="card-body">
                                                    <div class="row no-gutters align-items-center">
                                                        <div class="col mr-2">
                                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                                Creditos pendientes</div>
                                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="creditos_pendientes">0</div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="d-flex">
                                <ul class="my-vertical-tab">
                                     <li>
                                        <a onclick="sucursalSeleccionadaMetricaGeneral(this)" id_sucursal="0" nombre_sucursal="generales" class="selected-a a-element-vertical-tab" aria-current="page">
                                            Todas
                                        </a>
                                    </li> 
                                    <?php
                                if(count($sucursales_array)>0){
                                                    foreach ($sucursales_array as $key => $value) {
                                                        $suc_identificador = $value['id_sucursal'];
                                                        $background = $value['background'];
                                                        $hover = $value['hover'];
                                                        $sweet = $value['sweet'];
                                                        $nombre = $value['nombre'];
                                                        echo "<li>
                                                        <a onclick='sucursalSeleccionadaMetricaGeneral(this)' id_sucursal='$suc_identificador' nombre_sucursal='$nombre' class='my-menu-a a-element-vertical-tab'>
                                                           $nombre
                                                        </a></li>";
                                                    }
                                                }
                                    ?>   
                                            
                                   <!--  
                                    <li>
                                        <a onclick="sucursalSeleccionadaMetricaGeneral(this)" id_sucursal="1" nombre_sucursal="Pedro Cardenas" class="my-menu-a a-element-vertical-tab">
                                            <i class="fas fa-store mr-2"></i>
                                            Pedro Cardenas
                                        </a>
                                    </li>
                                    <li>
                                        <a onclick="sucursalSeleccionadaMetricaGeneral(this)" id_sucursal="2" nombre_sucursal="Sendero" class="my-menu-a a-element-vertical-tab">
                                            <i class="fas fa-store mr-2"></i>
                                            Sendero
                                        </a>
                                    </li>
                                    <li>
                                        <a onclick="sucursalSeleccionadaMetricaGeneral(this)" id_sucursal="5" nombre_sucursal="Valle Hermoso" class="my-menu-a a-element-vertical-tab">
                                            <i class="fas fa-store mr-2"></i>
                                            Valle Hermoso
                                        </a>
                                    </li>
                                    <li>
                                        <a class="my-menu-a a-element-vertical-tab" id_sucursal="6" nombre_sucursal="Rio Bravo" onclick="sucursalSeleccionadaMetricaGeneral(this)">
                                            <i class="fas fa-store mr-2"></i>
                                            Rio Bravo
                                        </a>
                                    </li> -->
                                </ul>
                                <div class="vertical-menu-main-container" id="vertical-menu-main-container">
                                    <div class="row">
                                        <div class="col-10">
                                            <h3 class="titulo-vertical-menu">Metricas <span id="metricas-generales-titulo">generales<span></h3>
                                        </div>
                                        <div class="col-2">
                                            <div class="btn btn-secondary" onclick="modalPersonalizarFechaMetricasSucursales()" id="datos-personalizar" tipo_filtro="3" mes="<?php echo date('n') ?>" fecha_inicial="" fecha_final="" year="<?php echo date('Y') ?>" semana="">Personalizar</div>
                                        </div>
                                    </div>
                                    <p class="mb-2">Estadisticas de ventas, creditos y dinero en deuda <span id="metricas-generales-texto-filtro">este mes </span>.</p>
                                    <div style="display: flex; flex-direction: row;" id="contenedor-datos-circulos" class="">
                                        <div class="basis-1/4" style="flex-basis: 25%; display:flex; flex-direction:column; align-items:center">
                                            <div class="dato-circulo">
                                                <span>$1,230,140.00</span>
                                            </div>
                                            <b class="mt-2">Utilidad</b>
                                        </div>
                                        <div class="basis-1/4" style="flex-basis: 25%; display:flex; flex-direction:column; align-items:center">
                                            <div class="dato-circulo">
                                                <span>$5,430,503.00</span>
                                            </div>
                                            <b class="mt-2">Entrada</b>
                                        </div>
                                        <div class="basis-1/4" style="flex-basis: 25%; display:flex; flex-direction:column; align-items:center">
                                            <div class="dato-circulo">
                                                <span>$5,430,503.00</span>
                                            </div>
                                            <b class="mt-2 text-center">Creditos por</br> cobrar</b>
                                        </div>
                                        <div class="basis-1/4" style="flex-basis: 25%; display:flex; flex-direction:column; align-items:center">
                                            <div class="dato-circulo">
                                                <span>$5,430,503.00</span>
                                            </div>
                                            <b class="mt-2 text-center">Dinero en mercancia</b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <!-- Area Chart -->
                                <div class="col-xl-12 col-lg-12 col-md-12">
                                    <div class="card shadow mb-4">
                                        <!-- Card Header - Dropdown -->
                                        <div class="card-header py-3">
                                            <div class="row d-flex flex-row align-items-center justify-content-between">
                                                <div class="col-6 col-md-8">
                                                    <h6 class="m-0 font-weight-bold text-primary">Grafica de ventas <span id="titulo-graf"></span></h6>
                                                </div>
                                                <div class="col-6 col-md-3 d-flex flex-row align-items-center justify-content-end">
                                                    <select class="form-control w-20" id="año-grafica-meses" onchange="graficaAreaYear()">
                                                        <option value="2021">2021</option>
                                                        <option value="2022">2022</option>
                                                        <option value="2023">2023</option>
                                                        <option value="2024" selected>2024</option>
                                                    </select>
                                                </div>
                                                <div class="col-6 col-md-1 d-flex flex-row align-items-center justify-content-end">
                                                    <div class="dropdown no-arrow">
                                                        <a class="dropdown-toggle" style="cursor: pointer;" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" id="lista-suc-graph-area-ventas" aria-labelledby="dropdownMenuLink">
                                                            <div class="dropdown-header">Sucursales:</div>
                                                            <?php

                                                                    if(count($sucursales_array)>0){
                                                                        foreach ($sucursales_array as $key => $value) {
                                                                            $suc_identificador = $value['id_sucursal'];
                                                                            $background = $value['background'];
                                                                            $hover = $value['hover'];
                                                                            $sweet = $value['sweet'];
                                                                            $nombre = $value['nombre'];
                                                                            echo '<a class="dropdown-item" bandera_fusionar="0" style="cursor: pointer;" onclick="graficaAreaPorSucursal(' . $suc_identificador . ', `' . $background . '`, `' . $hover . '`,  `' . $sweet . '`,  `' . $nombre . '`); marcarSeleccionado(this, `lista-suc-graph-area-ventas`);" chart_sucursal_id="' . $suc_identificador . '" chart_background_color="' . $background . '" chart_hover_color="' . $hover . '" chart_sweet_color="' . $sweet . '" nombre_sucursal="' . $nombre . '">' . $nombre . '</a>';
                                                                        }
                                                                    }
                                                            ?>
                                                            <!-- <a class="dropdown-item" href="#" onclick="graficaAreaPedroCardenas();">Pedro Cardenas</a>
                                                            <a class="dropdown-item" href="#" onclick="graficaAreaSendero();">Sendero</a> -->
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item" bandera_fusionar="1" style="cursor: pointer;" onclick="graficaAreaGeneral(); marcarSeleccionado(this)">Fusionar</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Card Body -->
                                        <div class="card-body">
                                            <div class="chart-area" id="chart-area-container">
                                                <canvas id="myAreaChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Grafica de ventas de esta semana<span id="titulo-graf"></span></h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" id="lista-suc-graph-bar-medidas" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Sucursales:</div>
                                            <?php

                                                if(count($sucursales_array)>0){
                                                    foreach ($sucursales_array as $key => $value) {
                                                        $suc_identificador = $value['id_sucursal'];
                                                        $background = $value['background'];
                                                        $hover = $value['hover'];
                                                        $sweet = $value['sweet'];
                                                        $nombre = $value['nombre'];
                                                        echo '<a class="dropdown-item" href="#" onclick="graficaBarPorSucursal(' . $suc_identificador . ', `' . $background . '`, `' . $hover . '`,  `' . $sweet . '`,  `' . $nombre . '`); marcarSeleccionado(this, `lista-suc-graph-bar-medidas`)">' . $nombre . '</a>';
                                                    }
                                                }

                                            ?>
                                            <!-- <a class="dropdown-item" href="#" onclick="graficaBarPedroCardenas();">Pedro Cardenas</a> 
                                            <a class="dropdown-item" href="#" onclick="graficaBarSendero();">Sendero</a> -->
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#" onclick="graficaBarGeneral();">Fusionar</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-area" id="chart-bar-container">
                                        <canvas id="myBarChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->


                <div class="container-fluid">
                    <div class="row">

                        <!-- Pie Chart -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary" id="titulo-graf-pie">Ventas sucursales</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Mostrar:</div>
                                            <a class="dropdown-item" href="#" onclick="totalVentas();">Total de ventas</a>
                                            <a class="dropdown-item" href="#" onclick="numeroVentas();">Numero de ventas</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#" onclick="totalCreditos();">Numero de creditos</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2" id="chart-pie-container">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small" id="store-tags">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8 col-lg-5">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary" id="titulo-graf-pie">Medidas mas vendidas (este mes)</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Mostrar:</div>
                                            <a class="dropdown-item" href="#" onclick="totalVentas();">Total de ventas</a>
                                            <a class="dropdown-item" href="#" onclick="numeroVentas();">Numero de ventas</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#" onclick="totalCreditos();">Numero de creditos</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2" id="chart-bar-medida-container">
                                        <canvas id="grafica-bar-medidas"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small" id="store-tags">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" style="display: flex; justify-content: center; margin-top: 80px;">
                    <img src="src/img/undraw_snow_fun_re_plbr.svg" alt="" width="400px">
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

    <script>



    </script>

    <!-- Bootstrap core JavaScript-->
    <script src="src/vendor/jquery/jquery.min.js"></script>
    <script src="src/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>
    <!-- Core plugin JavaScript-->
    <script src="src/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="src/js/sb-admin-2.min.js?v=1239"></script>

    <!-- Page level plugins -->
    <script src="src/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="src/js/demo/chart-bar-demo.js"></script>
    <script src="src/js/demo/chart-area-demo.js"></script>
    <script src="src/js/demo/chart-pie-demo.js"></script>

    <!--  <script src="src/js/notificaciones.js"></script> -->
    <script src="src/js/panel.js"></script>
    <script src="src/js/panel_descarga_reporte_ventas.js"></script> 
    <script src="src/js/panel_descarga_reporte_ventas.js"></script>


    </script>

</body>

</html>