<?php
session_start();

include 'modelo/conexion.php';
$con = $conectando->conexion();

if (!$con) {
    echo "maaaaal";
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location:login.php");
}

if ($_SESSION['rol'] != 1 || $_SESSION['rol'] != 4 || $_SESSION['rol'] != 2 || $_SESSION['id_usuario'] != 19) { //19 es el usuario de Javier
    header('nueva-venta.php?nav=inicio&id=0');
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



    <title>Mercancia pendiente | El Rayo Service Manager</title>

    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="src/css/inventario.css">
    <link rel="stylesheet" href="src/css/historial-ventas.css">

    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">


    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="src/vendor/datatables/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="src/vendor/bower_components/select2-bootstrap-theme/dist/select2-bootstrap.css">

    <!---Librerias de estilos-->

    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <link href="src/css/filtros.css" rel="stylesheet">
    <style>
        #content-wrapper {
            font-size: 12px !important;
        }
        
        .toastr-container{
   z-index: 999999999999999999;
   background-color: cadetblue;
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
                <div class="d-none" id="titulo-hv" sucursal="<?php echo $_SESSION['sucursal'] ?>" id_sucursal="<?php echo $_SESSION['id_sucursal'] ?>" rol="<?php echo $_SESSION['rol'] ?>" id_usuario="<?php echo $_SESSION['id_usuario'] ?>" nombre_usuario="<?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellidos'] ?>"></div>
                <?php

                $select_usuarios = "SELECT * FROM usuarios";
                $stmt = $con->prepare($select_usuarios);
                $stmt->execute();
                $result_data = $stmt->get_result();
                $stmt->free_result();
                $stmt->close();
                $arreglo_usuarios = array();
                while ($fila_users = $result_data->fetch_assoc()) {
                    array_push($arreglo_usuarios, array('id' => $fila_users['id'], 'nombre' => $fila_users['nombre'] . ' ' . $fila_users['apellidos']));
                }

                

                $dnone = ($_SESSION['rol'] != 1 || $_SESSION['rol'] != 4) ? 'd-none' : ''; 
                if ($_SESSION['rol'] != '4' && $_SESSION['rol'] !='1') {
                ?>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <h3><b>Mercancia pendiente</b></h3>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h5><b>Mercancia pendiente de enviar</b></h5>
                            </div>
                        </div>
                        <table id="mercancia-pendiente-enviar" class="table table-striped table-bordered table-hover">
                            <thead style="background-color:#36b9cc; color:white">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th>Marca</th>
                                    <th>Cantidad</th>
                                    <th>Suc. ubicación</th>
                                    <th>Suc. destino</th>
                                    <th>Folio mov.</th>
                                    <th>Comentario ubicación</th>
                                    <th>Comentario destino</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $estatus_pendiente = 'Pendiente';
                                $select_s = "SELECT * FROM sucursal";
                                $stmt = $con->prepare($select_s);
                                $stmt->execute();
                                $result_data = $stmt->get_result();
                                $stmt->free_result();
                                $stmt->close();
                                $arreglo_suc = [];
                                while ($fila_suc = $result_data->fetch_assoc()) {
                                    array_push($arreglo_suc, array('id' => $fila_suc['id'], 'nombre' => $fila_suc['nombre']));
                                }

                                $total_llantas_ = 0;
                                $id_sucursal = $_SESSION['id_sucursal'];
                                $select = "SELECT COUNT(dc.id) FROM historial_detalle_cambio dc INNER JOIN movimientos m ON dc.id_movimiento = m.id WHERE dc.id_ubicacion = ? AND m.estatus = ?";
                                $stmt = $con->prepare($select);
                                $stmt->bind_param('is', $id_sucursal, $estatus_pendiente);
                                $stmt->execute();
                                $stmt->bind_result($total_llantas_);
                                $stmt->fetch();
                                $stmt->close();

                                if ($total_llantas_ > 0) {
                                    $select_x = "SELECT dc.id, m.fecha, dc.cantidad,dc.id_movimiento, dc.id_ubicacion, dc.id_destino, ll.Descripcion, ll.Marca, dc.aprobado_receptor, dc.aprobado_emisor, dc.comentario_emisor, dc.comentario_receptor, dc.usuario_emisor, dc.usuario_receptor  FROM historial_detalle_cambio dc INNER JOIN llantas ll ON dc.id_llanta = ll.id INNER JOIN movimientos m ON dc.id_movimiento = m.id WHERE dc.id_ubicacion = ? AND m.estatus = ?";
                                    $stmt = $con->prepare($select_x);
                                    $stmt->bind_param('ss', $id_sucursal, $estatus_pendiente);
                                    $stmt->execute();
                                    $result_data = $stmt->get_result();
                                    $stmt->free_result();
                                    $stmt->close();
                                    $ind = 1;

                                    while ($fila_ab = $result_data->fetch_assoc()) {
                                        $cantidad = $fila_ab['cantidad'];
                                        $descripcion = $fila_ab['Descripcion'];
                                        $fecha_mov = $fila_ab['fecha'];
                                        $id_destino = $fila_ab['id_destino'];
                                        $id_ubicacion = $fila_ab['id_ubicacion'];
                                        $folio_mov = $fila_ab['id_movimiento'];
                                        $id_ubicacion = $fila_ab['id_ubicacion'];
                                        $comentario_emisor = $fila_ab['comentario_emisor'];
                                        $comentario_receptor = $fila_ab['comentario_receptor'];
                                        $id_usuario_emisor =  $fila_ab['usuario_emisor'];
                                        $id_usuario_receptor =  $fila_ab['usuario_receptor'];
                                        foreach ($arreglo_usuarios as $usuarios) {
                                            if ($usuarios['id'] == $id_usuario_emisor) {
                                                $usuario_emisor = $usuarios['nombre'];
                                                break;
                                            }
                                        }
                                        foreach ($arreglo_usuarios as $usuarios) {
                                            if ($usuarios['id'] == $id_usuario_receptor) {
                                                $usuario_receptor = $usuarios['nombre'];
                                                break;
                                            }
                                        }
                                        $aprobado_receptor = $fila_ab['aprobado_receptor'];
                                        $aprobado_emisor = $fila_ab['aprobado_emisor'];
                                        if ($aprobado_emisor == 1) {
                                            $color_emisor = '#03bb85';
                                        } else if ($aprobado_emisor == 0) {
                                            $color_emisor = '#ffb856';
                                        } else if ($aprobado_emisor == 2) {
                                            $color_emisor = '#ee5740';
                                        };
                                        if ($aprobado_receptor == 1) {
                                            $color_receptor = '#03bb85';
                                        } else if ($aprobado_receptor == 0) {
                                            $color_receptor = '#ffb856';
                                        } else if ($aprobado_receptor == 2) {
                                            $color_receptor = '#ee5740';
                                        };

                                        $suc_ubicacion = null;
                                        $id_historial = $fila_ab['id'];
                                        foreach ($arreglo_suc as $sucursal) {
                                            if ($sucursal['id'] == $id_ubicacion) {
                                                $suc_ubicacion = $sucursal['nombre'];
                                                break;
                                            }
                                        }

                                        if ($suc_ubicacion === null) {
                                            // Manejo de error si no se encontró el 'id_ubicacion' en el arreglo

                                            $suc_ubicacion = 'Bodega';
                                        }

                                        // Para obtener el nombre de 'id_destino'
                                        $id_destino = $fila_ab['id_destino'];
                                        $suc_destino = null;

                                        foreach ($arreglo_suc as $sucursal) {
                                            if ($sucursal['id'] == $id_destino) {
                                                $suc_destino = $sucursal['nombre'];
                                                break;
                                            }
                                        }

                                        $marca = $fila_ab['Marca'];
                                        print_r("<tr id='$id_historial'>
                                                <td>$ind</td>
                                                <td>$fecha_mov</td>
                                                <td>$descripcion</td>
                                                <td>$marca</td>
                                                <td>$cantidad</td>
                                                <td style='background-color:$color_emisor; color: white'>
                                                <b>$suc_ubicacion</b> </br>
                                                $usuario_emisor
                                                </td>
                                                <td style='background-color:$color_receptor; color: white'>
                                                <b>$suc_destino</b> </br>
                                                $usuario_receptor
                                                </td>
                                                <td>$folio_mov</td>
                                                <td> $comentario_emisor</td>
                                                <td> $comentario_receptor</td>
                                                <td >
                                                <div class=''>
                                                    <div class='btn btn-success' title='Aprobar'  onclick='aprovMercancia($id_historial, 1,1, $folio_mov)'><i class='fas fa-check'></i></div>
                                                    <div class='btn btn-danger' title='Aprobar' onclick='aprovMercancia($id_historial, 1,2, $folio_mov)'><i class='fas fa-ban'></i></div>
                                                </div>
                                                </td>
                                            </tr>");
                                    }
                                } else {
                                    print_r("<tr><td colspan='11' class='text-center'> No hay mercancia pendiente de enviar </td></tr>");
                                }

                                ?>
                            </tbody>
                        </table>

                        <div class="row mt-5">
                            <div class="col-md-6">
                                <h5><b>Mercancia pendiente de recibir</b></h5>
                            </div>
                        </div>
                        <table id="mercancia-pendiente-recibir" class="table table-striped table-bordered table-hover">
                            <thead style="background-color:#36b9cc; color:white">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th>Marca</th>
                                    <th>Cantidad</th>
                                    <th>Suc. ubicación</th>
                                    <th>Suc. destino</th>
                                    <th>Folio mov.</th>
                                    <th>Comentario ubicación</th>
                                    <th>Comentario destino</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $estatus_pendiente = 'Pendiente';
                                $select_s = "SELECT * FROM sucursal";
                                $stmt = $con->prepare($select_s);
                                $stmt->execute();
                                $result_data = $stmt->get_result();
                                $stmt->free_result();
                                $stmt->close();
                                $arreglo_suc = [];
                                while ($fila_suc = $result_data->fetch_assoc()) {
                                    array_push($arreglo_suc, array('id' => $fila_suc['id'], 'nombre' => $fila_suc['nombre']));
                                }

                                $total_llantas = 0;
                                $select_ = "SELECT COUNT(dc.id) FROM historial_detalle_cambio dc INNER JOIN movimientos m ON dc.id_movimiento = m.id WHERE dc.id_destino = ? AND m.estatus = ?";
                                $stmtx = $con->prepare($select_);
                                $stmtx->bind_param('ss', $id_sucursal, $estatus_pendiente);
                                $stmtx->execute();
                                $stmtx->bind_result($total_llantas);
                                $stmtx->fetch();
                                $stmtx->close();

                                if ($total_llantas > 0) {
                                    $select_x = "SELECT dc.id, m.fecha, dc.cantidad,dc.id_movimiento, dc.id_ubicacion, dc.id_destino, ll.Descripcion, ll.Marca, dc.aprobado_receptor, dc.aprobado_emisor, dc.comentario_emisor, dc.comentario_receptor, dc.usuario_emisor, dc.usuario_receptor  FROM historial_detalle_cambio dc INNER JOIN llantas ll ON dc.id_llanta = ll.id INNER JOIN movimientos m ON dc.id_movimiento = m.id WHERE dc.id_destino = ? AND m.estatus = ?";
                                    $stmt = $con->prepare($select_x);
                                    $stmt->bind_param('ss', $id_sucursal, $estatus_pendiente);
                                    $stmt->execute();
                                    $result_data = $stmt->get_result();
                                    $stmt->free_result();
                                    $stmt->close();
                                    $ind = 1;

                                    while ($fila_ab = $result_data->fetch_assoc()) {
                                        $cantidad = $fila_ab['cantidad'];
                                        $fecha_mov = $fila_ab['fecha'];
                                        $descripcion = $fila_ab['Descripcion'];
                                        $id_destino = $fila_ab['id_destino'];
                                        $id_ubicacion = $fila_ab['id_ubicacion'];
                                        $folio_mov = $fila_ab['id_movimiento'];
                                        $id_ubicacion = $fila_ab['id_ubicacion'];
                                        $aprobado_receptor = $fila_ab['aprobado_receptor'];
                                        $aprobado_emisor = $fila_ab['aprobado_emisor'];
                                        $comentario_emisor = $fila_ab['comentario_emisor'];
                                        $comentario_receptor = $fila_ab['comentario_receptor'];
                                        $id_usuario_emisor =  $fila_ab['usuario_emisor'];
                                        $id_usuario_receptor =  $fila_ab['usuario_receptor'];
                                        foreach ($arreglo_usuarios as $usuarios) {
                                            if ($usuarios['id'] == $id_usuario_emisor) {
                                                $usuario_emisor = $usuarios['nombre'];
                                                break;
                                            }
                                        }
                                        foreach ($arreglo_usuarios as $usuarios) {
                                            if ($usuarios['id'] == $id_usuario_receptor) {
                                                $usuario_receptor = $usuarios['nombre'];
                                                break;
                                            }
                                        }
                                        if ($aprobado_emisor == 1) {
                                            $color_emisor = '#03bb85';
                                        } else if ($aprobado_emisor == 0) {
                                            $color_emisor = '#ffb856';
                                        } else if ($aprobado_emisor == 2) {
                                            $color_emisor = '#ee5740';
                                        };
                                        if ($aprobado_receptor == 1) {
                                            $color_receptor = '#03bb85';
                                        } else if ($aprobado_receptor == 0) {
                                            $color_receptor = '#ffb856';
                                        } else if ($aprobado_receptor == 2) {
                                            $color_receptor = '#ee5740';
                                        };
                                        $suc_ubicacion = null;
                                        $id_historial = $fila_ab['id'];
                                        foreach ($arreglo_suc as $sucursal) {
                                            if ($sucursal['id'] == $id_ubicacion) {
                                                $suc_ubicacion = $sucursal['nombre'];
                                                break;
                                            }
                                        }

                                        if ($suc_ubicacion === null) {
                                            // Manejo de error si no se encontró el 'id_ubicacion' en el arreglo
                                            $suc_ubicacion = 'Bodega';
                                        }

                                        // Para obtener el nombre de 'id_destino'
                                        $id_destino = $fila_ab['id_destino'];
                                        $suc_destino = null;

                                        foreach ($arreglo_suc as $sucursal) {
                                            if ($sucursal['id'] == $id_destino) {
                                                $suc_destino = $sucursal['nombre'];
                                                break;
                                            }
                                        }

                                        $marca = $fila_ab['Marca'];
                                        
                                        print_r("<tr id='$id_historial'>
                                            <td>$ind</td>
                                            <td>$fecha_mov</td>
                                            <td>$descripcion</td>
                                            <td>$marca</td>
                                            <td>$cantidad</td>
                                            <td style='background-color:$color_emisor; color: white'>
                                                <b>$suc_ubicacion</b> </br>
                                                $usuario_emisor
                                            </td>
                                            <td style='background-color:$color_receptor; color: white'>
                                                <b>$suc_destino</b> </br>
                                                $usuario_receptor
                                            </td>  
                                            <td>$folio_mov</td>
                                            <td> $comentario_emisor</td>
                                            <td> $comentario_receptor</td>
                                            <td>
                                                <div class=''>
                                               <div class='btn btn-success' title='Aprobar' onclick='aprovMercancia($id_historial,2,1,$folio_mov)'><i class='fas fa-check'></i></div>
                                               <div class='btn btn-danger' title='Aprobar' onclick='aprovMercancia($id_historial, 2,2,$folio_mov)'><i class='fas fa-ban'></i></div>
                                                </div>
                                            </td>
                                        </tr>");
                                        $ind++;
                                    }
                                } else {
                                    print_r("<tr><td colspan='11' class='text-center'>No hay mercancia pendiente de recibir</td></tr>");
                                }
                                ?>
                            </tbody>
                        </table>

                    </div>
                <?php } else { ?>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <h3><b>Mercancia pendiente</b></h3>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h5><b>Mercancia pendiente de aprovar/rechazar</b></h5>
                            </div>
                        </div>
                        <table id="mercancia-pendiente-enviar" class="table table-striped table-bordered table-hover">
                            <thead style="background-color:#36b9cc; color:white">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th>Marca</th>
                                    <th>Cantidad</th>
                                    <th>Suc. ubicación</th>
                                    <th>Suc. destino</th>
                                    <th>Folio mov.</th>
                                    <th>Comentario ubicación</th>
                                    <th>Comentario destino</th>
                                    <th>Aprov. Ubicacion</th>
                                    <th>Aprov. Destino</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $estatus_pendiente = 'Pendiente';
                                $select_s = "SELECT * FROM sucursal";
                                $stmt = $con->prepare($select_s);
                                $stmt->execute();
                                $result_data = $stmt->get_result();
                                $stmt->free_result();
                                $stmt->close();
                                $arreglo_suc = [];
                                while ($fila_suc = $result_data->fetch_assoc()) {
                                    array_push($arreglo_suc, array('id' => $fila_suc['id'], 'nombre' => $fila_suc['nombre']));
                                }

                                $total_llantas_ = 0;
                                $id_sucursal = $_SESSION['id_sucursal'];
                                $select = "SELECT COUNT(dc.id) FROM historial_detalle_cambio dc INNER JOIN movimientos m ON dc.id_movimiento = m.id WHERE m.estatus = ?";
                                $stmt = $con->prepare($select);
                                $stmt->bind_param('s', $estatus_pendiente);
                                $stmt->execute();
                                $stmt->bind_result($total_llantas_);
                                $stmt->fetch();
                                $stmt->close();

                                if ($total_llantas_ > 0) {
                                    $select_x = "SELECT dc.id, m.fecha, dc.cantidad,dc.id_movimiento, dc.id_ubicacion, dc.id_destino, ll.Descripcion, ll.Marca, dc.aprobado_receptor, dc.aprobado_emisor, dc.comentario_emisor, dc.comentario_receptor,  dc.usuario_emisor, dc.usuario_receptor FROM historial_detalle_cambio dc INNER JOIN llantas ll ON dc.id_llanta = ll.id INNER JOIN movimientos m ON dc.id_movimiento = m.id WHERE m.estatus = ?";
                                    $stmt = $con->prepare($select_x);
                                    $stmt->bind_param('s', $estatus_pendiente);
                                    $stmt->execute();
                                    $result_data = $stmt->get_result();
                                    $stmt->free_result();
                                    $stmt->close();
                                    $ind = 1;

                                    while ($fila_ab = $result_data->fetch_assoc()) {
                                        $cantidad = $fila_ab['cantidad'];
                                        $fecha_mov = $fila_ab['fecha'];
                                        $descripcion = $fila_ab['Descripcion'];
                                        $id_destino = $fila_ab['id_destino'];
                                        $id_ubicacion = $fila_ab['id_ubicacion'];
                                        $folio_mov = $fila_ab['id_movimiento'];
                                        $id_ubicacion = $fila_ab['id_ubicacion'];
                                        $comentario_emisor = $fila_ab['comentario_emisor'];
                                        $comentario_receptor = $fila_ab['comentario_receptor'];
                                        $aprobado_receptor = $fila_ab['aprobado_receptor'];
                                        $aprobado_emisor = $fila_ab['aprobado_emisor'];
                                        $id_usuario_emisor =  $fila_ab['usuario_emisor'];
                                        $id_usuario_receptor =  $fila_ab['usuario_receptor'];
                                        foreach ($arreglo_usuarios as $usuarios) {
                                            if ($usuarios['id'] == $id_usuario_emisor) {
                                                $usuario_emisor = $usuarios['nombre'];
                                                break;
                                            }
                                        }
                                        foreach ($arreglo_usuarios as $usuarios) {
                                            if ($usuarios['id'] == $id_usuario_receptor) {
                                                $usuario_receptor = $usuarios['nombre'];
                                                break;
                                            }
                                        }
                                        if ($aprobado_emisor == 1) {
                                            $color_emisor = '#03bb85';
                                        } else if ($aprobado_emisor == 0) {
                                            $color_emisor = '#ffb856';
                                        } else if ($aprobado_emisor == 2) {
                                            $color_emisor = '#ee5740';
                                        };
                                        if ($aprobado_receptor == 1) {
                                            $color_receptor = '#03bb85';
                                        } else if ($aprobado_receptor == 0) {
                                            $color_receptor = '#ffb856';
                                        } else if ($aprobado_receptor == 2) {
                                            $color_receptor = '#ee5740';
                                        };

                                        $suc_ubicacion = null;
                                        $id_historial = $fila_ab['id'];
                                        foreach ($arreglo_suc as $sucursal) {
                                            if ($sucursal['id'] == $id_ubicacion) {
                                                $suc_ubicacion = $sucursal['nombre'];
                                                break;
                                            }
                                        }

                                        if ($suc_ubicacion === null) {
                                            // Manejo de error si no se encontró el 'id_ubicacion' en el arreglo

                                            $suc_ubicacion = 'Bodega';
                                        }

                                        // Para obtener el nombre de 'id_destino'
                                        $id_destino = $fila_ab['id_destino'];
                                        $suc_destino = null;

                                        foreach ($arreglo_suc as $sucursal) {
                                            if ($sucursal['id'] == $id_destino) {
                                                $suc_destino = $sucursal['nombre'];
                                                break;
                                            }
                                        }

                                        $marca = $fila_ab['Marca'];
                                        print_r("<tr id='$id_historial'>
                                                <td>$ind</td>
                                                <td>$fecha_mov</td>
                                                <td>$descripcion</td>
                                                <td>$marca</td>
                                                <td>$cantidad</td>
                                                <td style='background-color:$color_emisor; color: white'>
                                                $suc_ubicacion </br>
                                                $usuario_emisor
                                                </td>
                                                <td style='background-color:$color_receptor; color: white'>
                                                $suc_destino </br>
                                                $usuario_receptor
                                                </td>
                                                <td>$folio_mov</td>
                                                <td>$comentario_emisor</td>
                                                <td> $comentario_receptor</td>
                                                <td>
                                                    <div class='btn btn-success' title='Aprobar' onclick='aprovMercancia($id_historial, 1,1,$folio_mov)'><i class='fas fa-check'></i></div>
                                                    <div class='btn btn-danger' title='Aprobar' onclick='aprovMercancia($id_historial, 1,2,$folio_mov)'><i class='fas fa-ban'></i></div>
                                                </td>
                                                <td>
                                                    <div class='btn btn-success' title='Aprobar' onclick='aprovMercancia($id_historial, 2,1,$folio_mov)'><i class='fas fa-check'></i></div>
                                                    <div class='btn btn-danger' title='Aprobar' onclick='aprovMercancia($id_historial, 2,2,$folio_mov)'><i class='fas fa-ban'></i></div>
                                                </td>
                                            </tr>");
                                            $ind++;
                                    }
                                } else {
                                    print_r("<tr><td colspan='11' class='text-center'> No hay mercancia pendiente de enviar </td></tr>");
                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
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
        <!-- <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script> -->
        <script src="src/js/vue.min.js"></script>
        <script src="src/vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="src/vendor/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="src/vendor/datatables/dataTables.bootstrap4.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="src/vendor/datatables/defaults.js"></script>
        <script src="src/vendor/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="src/js/bootstrap-select.min.js"></script>
        <script src="src/js/llantas-pendientes.js"></script>
</body>

</html>