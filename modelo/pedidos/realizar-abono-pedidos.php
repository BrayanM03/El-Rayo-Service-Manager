<?php

session_start();
include '../conexion.php';
$con = $conectando->conexion();

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if (isset($_POST)) {

    date_default_timezone_set("America/Matamoros");
    $hora = date("h:i a");

    //Variables para el historial venta
    $fecha = date("Y-m-d");
    $id_apartado = $_POST['id_apartado'];
    $pago_efectivo = 0;
    $pago_transferencia = 0;
    $pago_tarjeta = 0;
    $pago_cheque = 0;
    $pago_sin_definir = 0;
    $monto_total_abono = 0;
    foreach ($_POST['metodos_pago'] as $key => $value) {
        $metodo_id = isset($value['id_metodo']) ? $value['id_metodo'] : $key;
        switch ($metodo_id) {
            case 0:
                $pago_efectivo = $value['monto'];
                break;

            case 1:
                $pago_tarjeta = $value['monto'];
                break;

            case 2:
                $pago_transferencia = $value['monto'];

                break;

            case 3:
                $pago_cheque = $value['monto'];
                break;

            case 4:
                $pago_sin_definir = $value['monto'];
                break;

            default:
                break;
        }
        $monto_pago = $value['monto'];
        $monto_total_abono += $value['monto'];
        $metodo_pago = $value['metodo'];
        $desc_metodos = '';
        if ($key != count($_POST["metodos_pago"]) - 1) {
            // Este código se ejecutará para todos menos el último
            $desc_metodos .= $metodo_pago . ", ";
        } else {
            $desc_metodos .= $metodo_pago . ". ";
        }
    }

    $vendedor_id = $_SESSION['id_usuario'];

    $ID = $con->prepare("SELECT nombre, apellidos FROM usuarios WHERE id = ?");
    $ID->bind_param('i', $vendedor_id);
    $ID->execute();
    $ID->bind_result($vendedor_name, $vendedor_apellido);
    $ID->fetch();
    $ID->close();

    $vendedor_usuario = $vendedor_name . " " . $vendedor_apellido;

    $select = "SELECT id_sucursal, id_usuario, id_cliente, sucursal, total, comentario FROM pedidos WHERE id = ?";
    $re = $con->prepare($select);
    $re->bind_param('i', $id_apartado);
    $re->execute();
    $re->bind_result($id_sucursal, $id_usuario, $id_cliente, $sucursal, $importe_total, $comentario);
    $re->fetch();
    $re->close();

    //Script que verifica la hora de corte actual
    include '../helpers/verificar-hora-corte.php';

    //Revisar si no hay errores en los montos
    $select = "SELECT SUM(abono), SUM(pago_efectivo), SUM(pago_tarjeta), SUM(pago_transferencia), SUM(pago_cheque), SUM(pago_sin_definir) FROM abonos_pedidos WHERE id_pedido = ?";
    $re = $con->prepare($select);
    $re->bind_param('i', $id_apartado);
    $re->execute();
    $re->bind_result(
        $suma_abonos,
        $actual_pago_efectivo,
        $actual_pago_tarjeta,
        $actual_pago_transferencia,
        $actual_pago_cheque,
        $actual_pago_sin_definir
    );
    $re->fetch();
    $re->close();

    $nueva_suma_abonos = $suma_abonos + $monto_total_abono;
    $nuevo_restante = $importe_total - $nueva_suma_abonos;
    $nuevo_pago_efectivo = $pago_efectivo + $actual_pago_efectivo;
    $nuevo_pago_tarjeta = $pago_tarjeta + $actual_pago_tarjeta;
    $nuevo_pago_transferencia = $pago_transferencia + $actual_pago_transferencia;
    $nuevo_pago_cheque = $pago_cheque + $actual_pago_cheque;
    $nuevo_pago_sin_definir = $pago_sin_definir + $actual_pago_sin_definir;

    if (($suma_abonos + $monto_total_abono) > $importe_total) {
        $res = array('estatus' => false, 'mensaje' => 'El monto que tratas de agregar soprepasa el total del importe', 'liquidacion' => true);
        echo json_encode($res);
    } else {

        if (($suma_abonos + $monto_total_abono) == $importe_total) {
            $tipo = 0;
        } else {
            $tipo = 1;
        }

        if ($nuevo_restante == 0) {
            $estatus = 'Pagado';
            $tipo = 'Pedido';
            $liquidacion = true;
            $estado = 0;
            //Insertando la venta
            $fecha_actual = date('Y-m-d');

            //Revisar si hay stock disponible
            $detalle = $con->prepare("SELECT i.Codigo, llantas.Modelo as modelo, da.Cantidad,llantas.Descripcion as descripcion, 
            llantas.Marca, da.precio_Unitario, da.Importe, i.Stock FROM detalle_pedido da 
            INNER JOIN inventario i ON da.id_Llanta = i.id_Llanta
            INNER JOIN llantas ON da.id_Llanta = llantas.id WHERE da.id_pedido = ? AND i.id_sucursal = ?");
            $detalle->bind_param('ii', $id_apartado, $id_sucursal);
            $detalle->execute();
            $resultado = $detalle->get_result();
            $num_rows = $detalle->num_rows();
            $detalle->close();

            $stockSuficiente = true;
            $lista_llantas = '';

            while ($fila = $resultado->fetch_assoc()) {

                $cantidadSolicitada = $fila['Cantidad'];
                $stockDisponible = $fila['Stock'];
                $codigo = $fila['Codigo'];

                if ($stockDisponible >= $cantidadSolicitada) {
                    // Aquí puedes realizar las acciones necesarias
                } else {
                    // El stock es insuficiente para la cantidad solicitadas
                    $descripcion = $fila['descripcion'];
                    $stockSuficiente = false;
                    $lista_llantas .= ', ' . $descripcion;
                }
                $lista_llantas = rtrim($lista_llantas, ', ');

                if (!$stockSuficiente) {
                    $mensaje = 'El stock es insuficiente para la llanta: ' . $codigo . ' ' . $lista_llantas . '
                    Cantidad solicitada: ' . $cantidadSolicitada . ' Stock actual: ' . $stockDisponible;
                    break;
                }
            }

            if ($stockSuficiente) {
                $queryInsertar = "INSERT INTO abonos_pedidos (id, id_pedido, 
                                                            fecha, 
                                                            hora, 
                                                            abono, 
                                                            metodo_pago,
                                                            pago_efectivo, 
                                                            pago_tarjeta, 
                                                            pago_transferencia, 
                                                            pago_cheque, 
                                                            pago_sin_definir, 
                                                            usuario, 
                                                            id_usuario,
                                                            estado,
                                                            sucursal,
                                                            id_sucursal, credito, fecha_corte) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0,?,?)";
                $resultado = $con->prepare($queryInsertar);
                $resultado->bind_param(
                    'sssssssssssssssss',
                    $id_apartado,
                    $fecha,
                    $hora,
                    $monto_total_abono,
                    $desc_metodos,
                    $pago_efectivo,
                    $pago_tarjeta,
                    $pago_transferencia,
                    $pago_cheque,
                    $pago_sin_definir,
                    $vendedor_usuario,
                    $id_usuario,
                    $estado,
                    $sucursal,
                    $id_sucursal,
                    $fecha_corte,
                    $hora
                );
                $resultado->execute();
                $error = $resultado->error;
                $resultado->close();

                //Validar hora de cortes
                $hora_actual = date("H:i a");
                $dia_de_la_semana = date("l");

                $hora_corte_normal = '';
                $hora_corte_sabado = '';
                $querySuc = "SELECT nombre, hora_corte_normal, hora_corte_sabado FROM sucursal WHERE id = ?";
                $resp = $con->prepare($querySuc);
                $resp->bind_param('i', $id_sucursal);
                $resp->execute();
                $resp->bind_result($sucursal, $hora_corte_normal, $hora_corte_sabado);
                $resp->fetch();
                $resp->close();

                $hora_a_comparar = $dia_de_la_semana == 'Saturday' ? $hora_corte_sabado : $hora_corte_normal;
                if ($hora_actual < $hora_a_comparar) {
                    $fecha_corte = $fecha_actual;
                } else {
                    if ($dia_de_la_semana == 'Saturday') {
                        // Crear un objeto DateTime a partir de la cadena de fecha
                        $fecha_obj = new DateTime($fecha_actual);
                        $fecha_obj->modify('+2 day');
                        $fecha_corte = $fecha_obj->format('Y-m-d');
                        $hora = '08:30 am';
                    } else {
                        $fecha_obj = new DateTime($fecha_actual);
                        $fecha_obj->modify('+1 day');
                        $fecha_corte = $fecha_obj->format('Y-m-d');
                        $hora = '08:30 am';
                    }
                }

                $insertar = $con->prepare("INSERT INTO ventas (Fecha, sucursal, id_sucursal, id_Usuarios, id_Cliente, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_sin_definir, Total, tipo, estatus, metodo_pago, hora, comentario, fecha_corte, hora_corte) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $insertar->bind_param('ssssssssssssssssss', $fecha_actual, $sucursal, $id_sucursal, $id_usuario, $id_cliente, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_sin_definir, $importe_total, $tipo, $estatus, $metodo_pago, $hora, $comentario, $fecha_corte, $hora);
                $insertar->execute();
                // Obtener el ID insertado
                $id_Venta = $con->insert_id;
                $insertar->close();

                //Haciendo consulta a detalle del apartado

                $detalle = $con->prepare("SELECT * FROM detalle_pedido dp INNER JOIN llantas ON dp.id_Llanta = llantas.id WHERE dp.id_pedido = ?");
                $detalle->bind_param('i', $id_apartado);
                $detalle->execute();
                $resultado_da = $detalle->get_result();
                $detalle->close();

                while ($fila = $resultado_da->fetch_assoc()) {
                    //print_r($fila);

                    $cantidad = $fila["Cantidad"];
                    $modelo = $fila["Modelo"];
                    $unidad = $fila["Unidad"];
                    $precio_unitario = $fila["precio_Unitario"];
                    $importe = $fila["Importe"];
                    $id_Llanta = $fila["id_Llanta"];

                    $dt_insert = "INSERT INTO detalle_venta (id, id_Venta, id_Llanta, Cantidad, Modelo, Unidad, precio_Unitario, Importe) VALUES (null,?,?,?,?,?,?,?)";
                    $resultado = $con->prepare($dt_insert);
                    $resultado->bind_param('iiisssd', $id_Venta, $id_Llanta, $cantidad, $modelo, $unidad, $precio_unitario, $importe);
                    $resultado->execute();
                    $resultado->close();

                    descontarStock($con, $id_Llanta, $id_sucursal, $cantidad);
                }
                $upd = "UPDATE pedidos SET
                abonado = ?,
                restante = ?,
                estatus = ?,
                id_venta =? WHERE id = ?";
                $ress = $con->prepare($upd);
                $ress->bind_param('ddssi', $nueva_suma_abonos, $nuevo_restante, $estatus, $id_Venta, $id_apartado);
                $ress->execute();
                $ress->close();

                $res = array('estatus' => true, 'mensaje' => 'Pedido liquidado correctamente', 'liquidacion' => $liquidacion);
            } else {
                $res = array('estatus' => $stockSuficiente, 'mensaje' => $mensaje);
            }
        } else {

            

            $estado = 1;
            $queryInsertar = "INSERT INTO abonos_pedidos (id, id_pedido, 
                                                            fecha, 
                                                            hora, 
                                                            abono, 
                                                            metodo_pago,
                                                            pago_efectivo, 
                                                            pago_tarjeta, 
                                                            pago_transferencia, 
                                                            pago_cheque, 
                                                            pago_sin_definir, 
                                                            usuario,
                                                            id_usuario, 
                                                            estado,
                                                            sucursal,
                                                            id_sucursal, credito, fecha_corte, hora_corte) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0,?,?)";
            $resultado = $con->prepare($queryInsertar);
            $resultado->bind_param(
                'sssssssssssssssss',
                $id_apartado,
                $fecha,
                $hora,
                $monto_total_abono,
                $desc_metodos,
                $pago_efectivo,
                $pago_tarjeta,
                $pago_transferencia,
                $pago_cheque,
                $pago_sin_definir,
                $vendedor_usuario,
                $id_usuario,
                $estado,
                $sucursal,
                $id_sucursal,
                $fecha_corte,
                $hora
            );
            $resultado->execute();
            $error = $resultado->error;
            $resultado->close();

            $estatus = 'Activo';
            $id_Venta = null;
            $liquidacion = false;

            $res = array('estatus' => true, 'mensaje' => 'Abono realizado correctamente', 'liquidacion' => $liquidacion);
            $upd = "UPDATE pedidos SET
        abonado = ?,
        restante = ?,
        estatus = ?,
        id_venta =? WHERE id = ?";
            $ress = $con->prepare($upd);
            $ress->bind_param('ddssi', $nueva_suma_abonos, $nuevo_restante, $estatus, $id_Venta, $id_apartado);
            $ress->execute();
            $ress->close();
        }

        echo json_encode($res);
    }
}

function descontarStock($con, $id_llanta, $id_sucursal, $cantidad)
{
    $stock_actual = 0;
    $select = "SELECT Stock FROM inventario WHERE id_Llanta =? AND id_sucursal =?";
    $stmt = $con->prepare($select);
    $stmt->bind_param('ss', $id_llanta, $id_sucursal);
    $stmt->execute();
    $stmt->bind_result($stock_actual);
    $stmt->fetch();
    $stmt->close();

    if ($stock_actual < $cantidad) {
        print_r('Erro, el stock actual es menor a la cantidad a retirar');
    } else {
        $nuevo_stock = intval($stock_actual) - intval($cantidad);
        $updt = "UPDATE inventario SET Stock = ? WHERE id_Llanta = ? AND id_sucursal =?";
        $stmt = $con->prepare($updt);
        $stmt->bind_param('sss', $nuevo_stock, $id_llanta, $id_sucursal);
        $stmt->execute();
        $stmt->close();
    }
}
