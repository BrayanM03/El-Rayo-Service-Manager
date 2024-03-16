<?php
session_start();
include '../conexion.php';
$con = $conectando->conexion(); 

//insertar utilidad
include '../ventas/insertar_utilidad.php';
date_default_timezone_set("America/Matamoros");
$hora = date("h:i a");
$fecha_actual = date("Y-m-d");
include '../creditos/obtener-utilidad-abono.php';

$id = $_POST["id"];
$plazo_credito = $_POST["plazo"];
$select = 'SELECT COUNT(*) FROM pedidos WHERE id =?';
$res = $con->prepare($select);
$res->bind_param('s', $id);
$res->execute();
$res->bind_result($total_pedidos);
$res->fetch();
$res->close();

if($total_pedidos>0){
    $select = 'SELECT id_sucursal, sucursal, abonado, restante, id_usuario, total, id_cliente, fecha_inicio FROM pedidos WHERE id =?';
    $res = $con->prepare($select);
    $res->bind_param('i', $id);
    $res->execute();
    $res->bind_result($id_sucursal, $sucursal, $abonado, $restante, $id_usuario, $total, $id_cliente, $fecha_inicio);
    $res->fetch();
    $res->close();

    if($restante == 0){
        $res = array('estatus'=> false, 'mensaje'=> 'El pedido ya fue pagado');

    }else{
        //Haciendo consulta a detalle del apartado

        $detalle = $con->prepare("SELECT i.Codigo, llantas.Modelo as modelo, da.cantidad,llantas.Descripcion as descripcion, 
        llantas.Marca, da.precio_unitario, da.importe, i.Stock FROM detalle_pedido da 
        INNER JOIN inventario i ON da.id_Llanta = i.id_Llanta
        INNER JOIN llantas ON da.id_Llanta = llantas.id WHERE da.id_pedido = ? AND i.id_sucursal = ?");
        $detalle->bind_param('ii', $id, $id_sucursal);
        $detalle->execute();
        $resultado = $detalle->get_result(); 
        $detalle->close();

        $stockSuficiente = true;
        $lista_llantas ='';
        while($fila = $resultado->fetch_assoc()){

            $cantidadSolicitada = $fila['cantidad'];
            $stockDisponible = $fila['Stock'];
            $codigo = $fila['Codigo'];
            
            if ($stockDisponible >= $cantidadSolicitada) {
                // Aquí puedes realizar las acciones necesarias
            } else {
                // El stock es insuficiente para la cantidad solicitadas
                $descripcion = $fila['descripcion'];
                $stockSuficiente = false;
                $lista_llantas .= ', '.$descripcion;
            }
            $lista_llantas = rtrim($lista_llantas, ', ');
            
            if(!$stockSuficiente){
                $mensaje = 'El stock es insuficiente para la llanta: '.$codigo.' '.$lista_llantas . '
                Cantidad solicitada: ' . $cantidadSolicitada . ' Stock actual: '. $stockDisponible;
                break;
            }
        }
        if($stockSuficiente){
            $insert_cotiza = insertarCotizacion($con, $resultado, $id, $id_cliente, $abonado, $restante, $total, $plazo_credito, $fecha_actual, $id_sucursal, $sucursal, $id_usuario);
            
            $res = $insert_cotiza;
        }else{
            $res = array('estatus'=> $stockSuficiente, 'mensaje'=> $mensaje);
        }
    }

        
}else{
    $res = array('estatus'=> false, 'mensaje'=> 'No se encontraron pedidos con este folio;: ' . $id);

}
echo json_encode($res);


function insertarCotizacion($con, $resultado, $id, $id_cliente, $abonado, $restante, $total, $plazo_credito, $fecha_actual, $id_sucursal, $sucursal, $id_usuario){
    
    //Declarando variables
    $suma_pago_efectivo = 0;
    $suma_pago_tarjeta = 0;
    $suma_pago_transferencia = 0;
    $suma_pago_cheque = 0;
    $suma_pago_deposito = 0;
    $suma_pago_sin_definir = 0;
    $select_ab = 'SELECT SUM(pago_efectivo),SUM(pago_tarjeta),SUM(pago_transferencia), SUM(pago_cheque), SUM(pago_deposito), SUM(pago_sin_definir) FROM abonos_pedidos WHERE id_pedido =?';
    $r = $con->prepare($select_ab);
    $r->bind_param('i', $id);
    $r->execute();
    $r->bind_result($suma_pago_efectivo,$suma_pago_tarjeta,$suma_pago_transferencia, $suma_pago_cheque, $suma_pago_deposito, $suma_pago_sin_definir);
    $r->fetch();
    $r->close();

    //Primero insertamos la venta al sistema
    $tipo_venta = 'Credito';
    $estatus_venta = 'Abierta';
    $metodo_por_def = 'Por definir';
    $hora_venta = date('h:i a');

    $hora_actual = date("H:i:s");
    //Script que verifica la hora de corte actual
    $hora = date("h:i a");
    include '../helpers/verificar-hora-corte.php';

    $insert = "INSERT INTO ventas(Fecha, sucursal, id_sucursal, id_usuarios, id_Cliente, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_deposito, pago_sin_definir, Total, tipo, estatus, metodo_pago, hora, fecha_corte, hora_corte)
    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $res = $con->prepare($insert);
    $res->bind_param('ssssssssssssssssss', $fecha_actual, $sucursal, $id_sucursal, $id_usuario , $id_cliente, $suma_pago_efectivo, $suma_pago_tarjeta, $suma_pago_transferencia, $suma_pago_cheque, $suma_pago_deposito, $suma_pago_sin_definir, $total, $tipo_venta, $estatus_venta, $metodo_por_def, $hora_venta, $fecha_corte, $hora_corte);
    $res->execute();
    $id_venta = mysqli_insert_id($con);
    $res->close();
    
    //Luego insertamos el credito
    $insert = "INSERT INTO creditos(id_cliente, pagado, restante, total, estatus, fecha_inicio, fecha_final, plazo, id_venta)
    VALUES(?,?,?,?,?,?,?,?,?)";
    $res = $con->prepare($insert);
    $estatus_credito = $abonado == 0 ? 0 : 2;
    $fecha = new DateTime($fecha_actual);

    // Aumenta la fecha según el plazo
    if ($plazo_credito == 1) {
        $fecha->modify('+1 week');
    } elseif ($plazo_credito == 2) {
        $fecha->modify('+15 days');
    } elseif ($plazo_credito == 3) {
        $fecha->modify('+1 month');
    } elseif ($plazo_credito == 5) {
        // No hace nada, ya que es "sin definir"
    }

// Obtiene la nueva fecha aumentada
    $nueva_final = $fecha->format('Y-m-d');
    $res->bind_param('sssssssss', $id_cliente, $abonado, $restante, $total, $estatus_credito,  $fecha_actual, $nueva_final, $plazo_credito, $id_venta);
    $res->execute();
    $error = $con->error;
    $id_credito = mysqli_insert_id($con);
    $res->close();
    //Insertamos el detalle de la venta
   
    $detalle = $con->prepare("SELECT * FROM detalle_pedido WHERE id_pedido = ?");
        $detalle->bind_param('i', $id);
        $detalle->execute();
        $resultado_dp = $detalle->get_result(); 
        $detalle->close();

    while($filaz = $resultado_dp->fetch_assoc()){
        $id_llanta = $filaz['id_Llanta'];
        $cantidad = $filaz['Cantidad'];
        $unidad_pieza = $filaz['Unidad'];
        $precio_unitario = $filaz['precio_Unitario'];
        $importe = $filaz['Importe'];
        $modelo_llanta ='';
        $select = 'SELECT Modelo FROM llantas WHERE id = ?';
        $rr = $con->prepare($select);
        $rr->bind_param('i', $id_llanta);
        $rr->execute();
        $rr->bind_result($modelo_llanta);
        $rr->fetch();
        $rr->close();
        
        $insert_dv = "INSERT INTO detalle_venta(id_Venta, id_Llanta, Cantidad, Modelo, Unidad, precio_Unitario, Importe)
        VALUES (?,?,?,?,?,?,?)";
        $res_d = $con->prepare($insert_dv);
        $res_d->bind_param('sssssss', $id_venta, $id_llanta, $cantidad, $modelo_llanta, $unidad_pieza, $precio_unitario, $importe);
        $err = $con->error;
        $res_d->execute();

        descontarStock($con, $id_llanta, $id_sucursal, $cantidad);

        $updt = "UPDATE pedidos SET estatus = 'Pendiente' WHERE id = ?";
        $stmt = $con->prepare($updt);
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $stmt->close();
    }

    $utlidad_res = insertarUtilidad($con, $id_venta);

    $select_ap = "SELECT * FROM abonos_pedidos WHERE id_pedido = ?";
    $resp = $con->prepare($select_ap);
    $resp->bind_param('i', $id);
    $resp->execute();
    $resultado_abonos = $resp->get_result();

    $arreglo_abonos = array(); // Inicializa un array para almacenar los resultados

    while ($fila = $resultado_abonos->fetch_assoc()) {
        // Agrega cada fila al array $arreglo_abonos
        $abono = $fila['abono'];
        $metodo_pago = $fila['metodo_pago'];
        $pago_efectivo = $fila['pago_efectivo'];
        $pago_tarjeta = $fila['pago_tarjeta'];
        $pago_transferencia = $fila['pago_transferencia'];
        $pago_cheque = $fila['pago_cheque'];
        $pago_deposito = $fila['pago_deposito'];
        $pago_sin_definir = $fila['pago_sin_definir'];
        $usuario = $fila['usuario'];
        $fecha_abono = $fila['fecha'];
        $hora_abono = $fila['hora'];
        $id_usuario = $fila['id_usuario'];
        $arreglo_abonos[] = $fila;
        $estado =0;
        $insertar_abonos = 'INSERT INTO abonos(id_credito, fecha, hora, abono, metodo_pago, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_deposito, pago_sin_definir, usuario, id_usuario, estado, sucursal, id_sucursal, fecha_corte, hora_corte)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $stmt = $con->prepare($insertar_abonos);
        $stmt->bind_param('ssssssssssssssssss', $id_credito, $fecha_abono, $hora_abono, $abono, $metodo_pago, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_deposito, $pago_sin_definir, $usuario, $id_usuario, $estado, $sucursal, $id_sucursal, $fecha_corte, $hora_corte);
        $stmt->execute();
        $id_abono = $con->insert_id;
        insertarUtilidadAbono($id_abono, $con);
    }

    $resp->close();
    
    if(!$error){
        return array('estatus' =>true, 'mensaje' =>'Credito insertado correctamente', 'utilidad_res'=>$utlidad_res);
    }else{
        return array('estatus' =>false, 'mensaje' =>'Hubo un error: '. $error);
    }
}

function descontarStock($con, $id_llanta, $id_sucursal, $cantidad){
    $stock_actual =0;
    $select = "SELECT Stock FROM inventario WHERE id_Llanta =? AND id_sucursal =?";
    $stmt = $con->prepare($select);
    $stmt->bind_param('ss', $id_llanta, $id_sucursal);
    $stmt->execute();
    $stmt->bind_result($stock_actual);
    $stmt->fetch();
    $stmt->close();
    
    if($stock_actual < $cantidad){
        print_r('Erro, el stock actual es menor a la cantidad a retirar');
    }else{
        $nuevo_stock = intval($stock_actual) - intval($cantidad);
        $updt = "UPDATE inventario SET Stock = ? WHERE id_Llanta = ? AND id_sucursal =?";
        $stmt = $con->prepare($updt);
        $stmt->bind_param('sss', $nuevo_stock, $id_llanta, $id_sucursal);
        $stmt->execute();
        $stmt->close();
    }
}