<?php

session_start();
include '../conexion.php';
include '../punto_venta/Venta.php';
$con = $conectando->conexion();

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

include 'insertar_utilidad.php';
include 'insertar-credito.php';
include '../helpers/response_helper.php';
 
if (isset($_POST)) {
    date_default_timezone_set("America/Matamoros");
    $hora = date("h:i a");

    //Variables para el historial venta
    $fecha = date("Y-m-d");
    $id_sucursal =  $_SESSION['id_sucursal'];
    $tipo_venta = $_POST['tipo_venta'];
    $comentario = $_POST['comentario'];
    $id_usuario =   $_SESSION['id_usuario'];
    $id_cliente =  $_POST['id_cliente'];
    $total =   0;

    if(!$id_sucursal || $id_cliente==0 || !$id_cliente){
        responder(false, 'El id del cliente o sucursal esta en 0, no se hizó la venta. Intente recargar la pagina: Control + shift + R', 'warning', [], true);
    }

    //Obtenemos datos de la sucursal
    $querySuc = "SELECT nombre, hora_corte_normal, hora_corte_sabado FROM sucursal WHERE id =?";
    $resp = $con->prepare($querySuc);
    $resp->bind_param('i', $id_sucursal);
    $resp->execute();
    $resp->bind_result($sucursal, $hora_corte_normal, $hora_corte_sabado);
    $resp->fetch();
    $resp->close();

    //Clasificamos el tipo de venta
    if ($tipo_venta == 'Credito') {
        $estatus = 'Abierta';
        $tipo = 'Credito';
    } elseif ($tipo_venta == 'Normal') {
        $estatus = 'Pagado';
        $tipo = 'Normal';
    }

    //Aquí procesamos el arreglo para el multi metodo de pago
    $desc_metodos = '';
    $pago_efectivo = 0;
    $pago_transferencia = 0;
    $pago_tarjeta = 0;
    $pago_cheque = 0;
    $pago_deposito = 0;
    $pago_sin_definir = 0;

    $arreglo_metodos = ['Efectivo', 'Tarjeta', 'Transferencia', 'Cheque', 'Sin definir', 'Deposito'];
    foreach ($_POST['metodos_formateado'] as $key => $value) {
        $metodo_id = isset($value['clave']) ? $value['clave'] : $key;
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

            case 5:
                $pago_deposito = $value['monto'];
                break;

            default:
                break;
        }
        $monto_pago = $value['monto'];
        $metodo_pago = $arreglo_metodos[$value['metodo']];
        if ($key != count($_POST['metodos_formateado']) - 1) {
            // Este código se ejecutará para todos menos el último
            $desc_metodos .= $metodo_pago . ", ";
        } else {
            $desc_metodos .= $metodo_pago . ". ";
        }
    }

    /* print_r('Efectivo: ' . $pago_efectivo . ' - ');
    print_r('Tarjeta: ' . $pago_tarjeta . ' - ');
    print_r('Transferencia: ' . $pago_transferencia . ' - ');
    print_r('Cheque: ' . $pago_cheque . ' - ');
    print_r('Sin definir: ' . $pago_sin_definir . ' - ');

    die(); */

    $sele = "SELECT COUNT(*) FROM productos_preventa WHERE id_usuario = ?";
    $stmt = $con->prepare($sele);
    $stmt->bind_param('i', $id_usuario);
    $stmt->execute();
    $stmt->bind_result($total_productos);
    $stmt->fetch();
    $stmt->close();

    if ($total_productos < 0) {
        responder(false, 'No hay productos en preventa', 'warning', [], true);
    }

    $sele_ = "SELECT * FROM productos_preventa WHERE id_usuario = ?";
    $stmt = $con->prepare($sele_);
    $stmt->bind_param('i', $id_usuario);
    $stmt->execute();
    $datos = $stmt->get_result();
    $stmt->close();

    while ($fila = $datos->fetch_assoc()) {
        $info_producto_individual[] = $fila;
    }

    $stock_ok = true;
    $error_llantas = '';
    //Recorremos el arreglo de los productos preventa
    foreach ($info_producto_individual as $key => $value) {

        $id_llanta = $value['id_llanta'];
        $cantidad_post = $value['cantidad'];
        $unidad_producto = $value['tipo'];
        $total += floatval($value['importe']);
     
        if ($unidad_producto == 1) {
         
            $ID = $con->prepare("SELECT Stock FROM inventario WHERE id_Llanta = ? AND id_sucursal =?");
            $ID->bind_param('ss', $id_llanta, $id_sucursal);
            $ID->execute();
            $ID->bind_result($stockActual);
            $ID->fetch();
            $ID->close();

            if ($stockActual < $cantidad_post) {
                $error_llantas .= $value['descripcion'] .', ';
                $stock_ok = false;
            }
        }

    }
    
 

    //Si hay stock procedemos a realizar la venta
    if ($stock_ok) {
            include '../helpers/verificar-hora-corte.php';
        
            // INICIO DE TRANSACCIÓN
            $con->begin_transaction();
        
            try {
                // 1. Insertar en ventas
                $queryInsertar = "INSERT INTO ventas (id, Fecha, sucursal, id_sucursal, id_Usuarios, id_Cliente, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_deposito, pago_sin_definir, Total, tipo, estatus, metodo_pago, hora, comentario, fecha_corte, hora_corte) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $stmt = $con->prepare($queryInsertar);
                if (!$stmt) throw new Exception("Error preparando INSERT ventas: " . $con->error);
                $stmt->bind_param('ssiisdddddddsssssss', $fecha, $sucursal, $id_sucursal, $id_usuario, $id_cliente, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_deposito, $pago_sin_definir, $total, $tipo, $estatus, $desc_metodos, $hora, $comentario, $fecha_corte, $hora_corte);
                if (!$stmt->execute()) throw new Exception("Error ejecutando INSERT ventas: " . $stmt->error);
                $id_Venta = $stmt->insert_id;
                $stmt->close();
        
                // 2. Insertar en detalle_venta y descontar stock
                foreach ($info_producto_individual as $value) {
                    $id_sucursal_inventario = $value['id_sucursal'];
                    $id_producto = $value['id_llanta'];
                    $modelo = $value['modelo'];
                    $cantidad = $value['cantidad'];
                    $precio_unitario = $value['precio'];
                    $importe = $value['importe'];
                    $unidad_producto = $value['tipo'];
                    $descripcion = $value['descripcion'];
        
                    $unidad = ($unidad_producto == 2) ? 'servicio' : 'pieza';
        
                    // Si es pieza, descontamos stock
                    if ($unidad == 'pieza') {
                        $ID = $con->prepare("SELECT Stock FROM inventario WHERE id_Llanta = ? AND id_sucursal = ?");
                        if (!$ID) throw new Exception("Error preparando SELECT stock: " . $con->error);
                        $ID->bind_param('ss', $id_producto, $id_sucursal_inventario);
                        $ID->execute();
                        $ID->bind_result($stockActual);
                        $ID->fetch();
                        $ID->close();
        
                        if ($stockActual < $cantidad) {
                            throw new Exception("Stock insuficiente para el producto: $descripcion");
                        }
        
                        $nuevo_stock = $stockActual - $cantidad;
                        $updateStock = $con->prepare("UPDATE inventario SET Stock = ? WHERE id_Llanta = ? AND id_sucursal = ?");
                        if (!$updateStock) throw new Exception("Error preparando UPDATE stock: " . $con->error);
                        $updateStock->bind_param('iii', $nuevo_stock, $id_producto, $id_sucursal_inventario);
                        if (!$updateStock->execute()) throw new Exception("Error actualizando stock: " . $updateStock->error);
                        $updateStock->close();
                    }
         
                    // Insertar en detalle_venta
                    $queryDetalle = "INSERT INTO detalle_venta (id, id_Venta, id_Llanta, Modelo, Cantidad, Unidad, precio_Unitario, Importe) VALUES (null,?,?,?,?,?,?,?)";
                    $stmt = $con->prepare($queryDetalle);
                    if (!$stmt) throw new Exception("Error preparando INSERT detalle_venta: " . $con->error);
                    $stmt->bind_param('iisisdd', $id_Venta, $id_producto, $modelo, $cantidad, $unidad, $precio_unitario, $importe);
                    if (!$stmt->execute()) throw new Exception("Error ejecutando INSERT detalle_venta: " . $stmt->error);
                    $stmt->close();
                }
        
                // 3. Insertar utilidad
                $utlidad_res = insertarUtilidad($con, $id_Venta);
        
                // 4. Eliminar productos_preventa
                $queryDelete = "DELETE FROM productos_preventa WHERE id_usuario = ?";
                $stmt = $con->prepare($queryDelete);
                if (!$stmt) throw new Exception("Error preparando DELETE preventa: " . $con->error);
                $stmt->bind_param('i', $id_usuario);
                if (!$stmt->execute()) throw new Exception("Error ejecutando DELETE preventa: " . $stmt->error);
                $stmt->close();
        
                // 5. Insertar crédito si aplica
                if ($tipo_venta == 'Credito') {
                    insertarCredito($con, $id_cliente, $id_sucursal, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_sin_definir, $desc_metodos, $total, $id_Venta);
                }

                $clase_venta = new Ventas($con);
                $comprobacion_venta = $clase_venta->existeDetalleVenta($id_Venta);
                if($comprobacion_venta['estatus'] == false ){
                    throw new Exception("Error : No se encontraron detalles de venta");
                }
                /* echo json_encode($comprobacion_venta);
                die(); */
        
                // Todo correcto, se hace commit
                $con->commit();
                $response = array('estatus' => true, 'mensaje' => 'Venta realizada correctamente', 'folio' => $id_Venta, 'utlidad_res' => $utlidad_res);
                echo json_encode($response);
        
            } catch (Exception $e) {
                $con->rollback();
               // error_log("Error en venta: " . $e->getMessage()); // También puedes guardar en tabla si prefieres
                responder(false, 'Error en la venta: ' . $e->getMessage(), 'error', [], true);
            
            }

    } else {
        $response = array('estatus' => false, 'mensaje' => 'El stock de la llantas: '. $error_llantas.' no es suficiente. Cantidad solicitada<b>'.$cantidad_post .'</b>  Stock actual:<b>'.$stockActual);
        echo json_encode($response);
    }

}
