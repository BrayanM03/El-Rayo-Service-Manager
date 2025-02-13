<?php

session_start();
include '../conexion.php';
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

        $queryInsertar = "INSERT INTO ventas (id, Fecha, sucursal, id_sucursal, id_Usuarios, id_Cliente, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_deposito, pago_sin_definir, Total, tipo, estatus, metodo_pago, hora, comentario, fecha_corte, hora_corte) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $resultado = $con->prepare($queryInsertar);
        $resultado->bind_param('ssiisdddddddsssssss', $fecha, $sucursal, $id_sucursal, $id_usuario, $id_cliente, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_deposito, $pago_sin_definir, $total, $tipo, $estatus, $desc_metodos, $hora, $comentario, $fecha_corte, $hora_corte);
        $resultado->execute();
        $id_Venta = $resultado->insert_id;
        $resultado->close();


        foreach ($info_producto_individual as $key => $value) {

            $validacion = is_numeric($key);

            if ($validacion) {
                $id_sucursal_inventario = $value['id_sucursal'];
                $id_producto = $value['id_llanta'];
                $descripcion = $value['descripcion'];
                $modelo = $value['modelo'];
                $cantidad = $value['cantidad'];
                $precio_unitario = $value['precio'];
                $importe = $value['importe'];
                $unidad_producto = $value['tipo'];
                $id_llanta = $value['id_llanta'];


                if ($unidad_producto == 2) {

                    $unidad = 'servicio';

                } else {
                    $unidad = "pieza";
                    $ID = $con->prepare("SELECT Stock FROM inventario WHERE id_Llanta = ? AND id_sucursal = ?");
                    $ID->bind_param('ss', $id_producto, $id_sucursal_inventario);
                    $ID->execute();
                    $ID->bind_result($stockActual);
                    $ID->fetch();
                    $ID->close();

                
                    if ($stockActual > 0) {
                   
                        $resultStock = $stockActual - $cantidad;
                        $updateStockSendero = $con->prepare("UPDATE inventario SET Stock = ? WHERE id_Llanta = ? AND id_sucursal = ?");
                        $updateStockSendero->bind_param('iii', $resultStock, $id_llanta, $id_sucursal);
                        $updateStockSendero->execute();
                        $updateStockSendero->close();
                    }

                }


                $queryInsertar = "INSERT INTO detalle_venta (id, id_Venta, id_Llanta, Modelo, Cantidad, Unidad, precio_Unitario, Importe) VALUES (null,?,?,?,?,?,?,?)";
                $resultado = $con->prepare($queryInsertar);
                $resultado->bind_param('iisisdd', $id_Venta, $id_producto, $modelo, $cantidad, $unidad, $precio_unitario, $importe);
                $resultado->execute();
                $resultado->close();


                //insertar utilidad
                $utlidad_res = insertarUtilidad($con, $id_Venta);
                $queryInsertar = "DELETE FROM productos_preventa WHERE id_usuario = ?";
                $resultado = $con->prepare($queryInsertar);
                $resultado->bind_param('i', $id_usuario);
                $resultado->execute();
                $resultado->close();

            }

        }

        if($tipo_venta=='Credito'){
            insertarCredito($con, $id_cliente, $id_sucursal, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_sin_definir, $desc_metodos, $total, $id_Venta);
        }

        $response = array('estatus' => true, 'mensaje' => 'Venta realizada correctamente', 'folio' => $id_Venta, 'utlidad_res' => $utlidad_res);
        echo json_encode($response);

    } else {
        $response = array('estatus' => false, 'mensaje' => 'El stock de la llantas: '. $error_llantas.' no es suficiente. Cantidad solicitada<b>'.$cantidad_post .'</b>  Stock actual:<b>'.$stockActual);
        echo json_encode($response);
    }

}
