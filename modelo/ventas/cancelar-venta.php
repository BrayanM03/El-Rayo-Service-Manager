<?php


session_start();
include '../conexion.php';
$con = $conectando->conexion();

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}



if ($_SESSION["rol"]  !== "1") {

    header("Location:../../notfound_404.php");

}
date_default_timezone_set("America/Matamoros");
$hora = date("h:i a");
$fecha = date('Y-m-d');

if(isset($_POST)) {
    $id_venta = $_POST["id_venta"];
    $motivo = $_POST["motivo_cancel"];
    $id_sesion = $_SESSION['id_usuario'];
    //Conseguir susucusal

    $obtenerSuc = "SELECT id_sucursal, sucursal, estatus FROM ventas WHERE id LIKE ?";
    $stmt = $con->prepare($obtenerSuc);
    $stmt->bind_param('i', $id_venta);
    $stmt->execute();
    $stmt->bind_result($sucursal, $sucursal_nombre, $estatus);
    $stmt->fetch();
    $stmt->close();

    if($estatus == "Cancelada") {
        print_r(3);
    } else {

        //Continuamos con la validad de llantas con esa venta
        $obtenerCant = "SELECT COUNT(*) total FROM detalle_venta WHERE id_Venta = ?";
        $stmt = $con->prepare($obtenerCant);
        $stmt->bind_param('i', $id_venta);
        $stmt->execute();
        $stmt->bind_result($total_detalles);
        $stmt->fetch();
        $stmt->close();

        if ($total_detalles == 0) {
            print_r(0);
        } else {

            //Actualizar historial de movimientos con la cancelación
            $nombre_usuario = $_SESSION['nombre'] . ' ' . $_SESSION['apellidos'];
            $tipo = 3;
            $pend = 'Pendiente';
            $insert = "INSERT INTO movimientos (fecha, hora, usuario, tipo, sucursal, estatus, id_usuario)
          VALUES(?,?,?,?,?,?,?)";
            $ress = $con->prepare($insert);
            $ress->bind_param('sssssss', $fecha, $hora, $nombre_usuario, $tipo, $sucursal, $pend, $id_sesion);
            $ress->execute();
            $movimiento_id = $con->insert_id;
            $ress->close();



            $llantasaDevolver = "SELECT id_Llanta, Cantidad, Unidad FROM detalle_venta WHERE id_Venta = ?";
            $stmt = $con->prepare($llantasaDevolver);
            $stmt->bind_param('s', $id_venta);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $stmt->close();

            $cantidad_llantas_movimiento = 0;
            $bandera_producto_servicio = 0;
            while ($row = $resultado->fetch_array()) {
                $id_llanta = $row["id_Llanta"];
                $cantidad = $row["Cantidad"];
                $unidad = $row["Unidad"];

                if($unidad == 'pieza') {
                    $bandera_producto_servicio++;
                    //Cotejamos las cantidad para luego sumarlas
                    $obtenerStock = "SELECT Stock FROM inventario WHERE id_Llanta LIKE ? AND id_sucursal = ?";
                    $stmt = $con->prepare($obtenerStock);
                    $stmt->bind_param('ii', $id_llanta, $sucursal);
                    $stmt->execute();
                    $stmt->bind_result($stock_actual);
                    $stmt->fetch();
                    $stmt->close();

                    $cantidad_total = $cantidad + $stock_actual;

                    $editar_llanta = $con->prepare("UPDATE inventario SET Stock = ? WHERE id_Llanta = ? AND id_sucursal = ?");
                    $editar_llanta->bind_param('iii', $cantidad_total, $id_llanta, $sucursal);
                    $editar_llanta->execute();
                    $editar_llanta->close();

                    //Actualizamos estatus de venta normal
                    if($estatus == "Pagado") {
                        $newStatus = "Cancelada";
                        $editar_status = $con->prepare("UPDATE ventas SET estatus = ?, comentario = ? WHERE id = ?");
                        $editar_status->bind_param('ssi', $newStatus, $motivo, $id_venta);
                        $editar_status->execute();
                        $editar_status->close();

                    } elseif($estatus == "Abierta") {
                        $newStatus = "Cancelada";
                        $editar_status = $con->prepare("UPDATE ventas SET estatus = ?, comentario = ? WHERE id = ?");
                        $editar_status->bind_param('ssi', $newStatus, $motivo, $id_venta);
                        $editar_status->execute();
                        $editar_status->close();

                        $newstatuscredito = 5;
                        $editar_status_credito = $con->prepare("UPDATE creditos SET estatus = ? WHERE id_venta = ?");
                        $editar_status_credito->bind_param('si', $newstatuscredito, $id_venta);
                        $editar_status_credito->execute();
                        $editar_status_credito->close();

                    }


                    //Actualizar historial de movimientos con la cancelación
                    $ins = "INSERT INTO historial_detalle_cambio(id_llanta, id_ubicacion, id_destino, cantidad, id_usuario, id_movimiento, stock_destino_actual, stock_destino_anterior, aprobado_receptor, aprobado_emisor, usuario_emisor, usuario_receptor)
                    VALUES (?,?,?,?,?,?,?,?,0,0,?,?)";
                    $rr = $con->prepare($ins);
                    $rr->bind_param('iiiiiiiiii', $id_llanta, $sucursal, $sucursal, $cantidad, $_SESSION['id_usuario'], $movimiento_id, $cantidad_total, $stock_actual, $id_sesion, $id_sesion);
                    $rr->execute();
                    $rr->close();


                    //Obtener la descripcion de las llantas para insertarlas en los movimientos
                    $select_llanta = "SELECT * FROM llantas WHERE id = ?";
                    $res = $con->prepare($select_llanta);
                    $res->bind_param('i', $id_llanta);
                    $res->execute();
                    $resultado_ll = $res->get_result();
                    $res->close();

                    //Actualizar detalle historial de movimientos con la cancelación
                    
                    while ($row = $resultado_ll->fetch_array()) {
                        $descripcion_llanta = $row['Descripcion'];
                        $mercancia[] = $descripcion_llanta; // Guardamos cada descripción en un array
                    }
                    
                    $mercancia = implode(', ', $mercancia); // Unimos las descripciones con coma y espacio
                    $mercancia .= '.'; // Agregamos el punto al final

                    $cantidad_llantas_movimiento += $cantidad;
                }else{
                  $bandera_producto_servicio += 0;
                  $mercancia .= '';
                  $cantidad_llantas_movimiento +=0;
                   //Actualizamos estatus de venta normal
                   if($estatus == "Pagado") {
                    $newStatus = "Cancelada";
                    $editar_status = $con->prepare("UPDATE ventas SET estatus = ?, comentario = ? WHERE id = ?");
                    $editar_status->bind_param('ssi', $newStatus, $motivo, $id_venta);
                    $editar_status->execute();
                    $editar_status->close();

                } elseif($estatus == "Abierta") {
                    $newStatus = "Cancelada";
                    $editar_status = $con->prepare("UPDATE ventas SET estatus = ?, comentario = ? WHERE id = ?");
                    $editar_status->bind_param('ssi', $newStatus, $motivo, $id_venta);
                    $editar_status->execute();
                    $editar_status->close();

                    $newstatuscredito = 5;
                    $editar_status_credito = $con->prepare("UPDATE creditos SET estatus = ? WHERE id_venta = ?");
                    $editar_status_credito->bind_param('si', $newstatuscredito, $id_venta);
                    $editar_status_credito->execute();
                    $editar_status_credito->close();

                }
                

                }
            }

            if($total_detalles > 1 || ($total_detalles == 1 && $bandera_producto_servicio > 0)){
              
              $descripcion_mov = 'Se agregan ' . $cantidad_llantas_movimiento . ' llantas a la sucursal ' . $sucursal_nombre . ' por motivo de cancelación. RAY'.$id_venta . ': ' . $motivo;
              $update = "UPDATE movimientos SET descripcion = ?, mercancia = ? WHERE id = ?";
              $respp = $con->prepare($update);
             
              $respp->bind_param('ssi', $descripcion_mov, $mercancia, $movimiento_id);
              $respp->execute();
              $respp->close();
            }

            if($bandera_producto_servicio == 0){
              $deleteQuery = "DELETE FROM movimientos WHERE id = ?";
              $deleteStmt = $con->prepare($deleteQuery);
              $deleteStmt->bind_param('i', $movimiento_id);
              $deleteStmt->execute();
              $deleteStmt->close();
            }

            print_r(1);

        }

    }




}
