
<?php 
include '../conexion.php';
$con= $conectando->conexion(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (!$con) {
    echo "Problemas con la conexion";
}

$id_movimiento = $_POST['id_movimiento'];
$id_sucursal = $_POST['id_sucursal'];
$id_llanta = $_POST['id_llanta'];
$cantidad = $_POST['cantidad'];
$costo_actual = $_POST['costo_actual'];
$costo_antes = $_POST['costo_antes'];
$precio_actual = $_POST['precio_actual'];
$precio_antes = $_POST['precio_antes'];
$mayoreo_actual = $_POST['mayoreo_actual'];
$mayoreo_antes = $_POST['mayoreo_antes'];
$tipo_remision = $_POST['tipo_remision'];
$actualizar_precio = $_POST['actualizar_precio'];
$permiso_act_inv = $_POST['permiso_act_inv'];

$qr = "SELECT COUNT(*) FROM movimientos WHERE id =?";
$stmt = $con->prepare($qr);
$stmt->bind_param('i', $id_movimiento);
$stmt->execute();
$stmt->bind_result($total_movimientos);
$stmt->fetch();
$stmt->close();

if($total_movimientos>0){
    if($actualizar_precio== 'true'){
        actualizarPrecioLlanta($con, $costo_actual, $precio_actual, $mayoreo_actual, $id_llanta);
    };
    $qr = "SELECT id_usuario FROM movimientos WHERE id =?";
    $stmt = $con->prepare($qr);
    $stmt->bind_param('i', $id_movimiento);
    $stmt->execute();
    $stmt->bind_result($id_usuario);
    $stmt->fetch();
    $stmt->close();

    //Validamos el tipo de remision y redirijo a la funcion correcta
    if($tipo_remision == 2){
        actualizarRemisionIngreso($con, $id_movimiento, $id_sucursal, $id_llanta, $cantidad, $costo_actual, $costo_antes, $precio_actual, $precio_antes, $mayoreo_actual, $mayoreo_antes, $id_usuario, $permiso_act_inv);
    }
}else{
    $estatus = false;
    $mensaje = "No se encontro un movimiento con este ID $id_movimiento";
}

function actualizarRemisionIngreso($con, $id_movimiento, $id_sucursal, $id_llanta, $cantidad, $costo_actual, $costo_antes, $precio_actual, $precio_antes, $mayoreo_actual, $mayoreo_antes, $id_usuario, $permiso_act_inv){
    $stock_destino_anterior =0;
    if($permiso_act_inv == 1){
        $resp_updt = actualizarInventario($id_sucursal, $id_llanta, $cantidad, $con);
        $stock_destino_actual = $cantidad + $resp_updt['stock_destino_anterior'];
        $nuevo_stock_ubicacion_anterior =  $resp_updt['stock_destino_anterior'];
    }else if($permiso_act_inv ==2){
        $query = "SELECT Stock FROM inventario WHERE id_Llanta = ? AND id_sucursal = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('ss', $id_llanta, $id_sucursal);
        $stmt->execute();
        $stmt->bind_result($stock_destino_anterior);
        $stmt->fetch();
        $stmt->close();
        $stock_destino_actual = $stock_destino_anterior + $cantidad;
        $nuevo_stock_ubicacion_anterior =  $stock_destino_anterior;
    }
  
    $usuario_emisor = $_SESSION['id_usuario'];
    $usuario_receptor = $_SESSION['id_usuario'];
    $importe = $cantidad * $costo_actual;
    $total_actual_mov=0;
    $pagado_actual_mov=0;
    $restante_actual_mov=0;
    $llantas_iguales_encontradas=0;

    //Validamos si la llanta a ingresar se encuentra en el historial detalle de cambio
    $query_ ="SELECT COUNT(*) FROM historial_detalle_cambio WHERE id_movimiento =? AND id_llanta = ? AND id_destino =?";
    $stmt = $con->prepare($query_);
    $stmt->bind_param('sss', $id_movimiento, $id_llanta, $id_sucursal);
    $stmt->execute();
    $stmt->bind_result($llantas_iguales_encontradas);
    $stmt->fetch();
    $stmt->close();

    
    if($llantas_iguales_encontradas > 0){
        $stock_destino_actual_remision =0;
        $id_historial =0;
        $cantidad_remision=0;
        $costo_remision=0;
        $importe_remision =0; 
        $query_ ="SELECT id, cantidad, stock_destino_actual, costo, importe FROM historial_detalle_cambio WHERE id_movimiento =? AND id_llanta = ? AND id_destino =?";
        $stmt = $con->prepare($query_);
        $stmt->bind_param('sss', $id_movimiento, $id_llanta, $id_sucursal);
        $stmt->execute();
        $stmt->bind_result($id_historial, $cantidad_remision, $stock_destino_actual_remision, $costo_remision, $importe_remision);
        $stmt->fetch();
        $stmt->close();
        /* print_r($cantidad_remision. '-' . $stock_destino_actual_remision . '-' .$importe_remision . '<br>');
       
        print_r($cantidad . '-' . $stock_destino_actual_remision)
        die(); */
        $nuevo_stock_destino_actual_remision = $stock_destino_actual_remision + $cantidad;
        $nueva_cantidad =$cantidad_remision + $cantidad;
        $nuevo_importe_remision = $importe_remision + $importe;

        $update= "UPDATE historial_detalle_cambio SET cantidad =?, stock_destino_actual = ?, costo = ?, importe =? WHERE id =?";
        $stmt = $con->prepare($update);
        $stmt->bind_param('sssss', $nueva_cantidad, $nuevo_stock_destino_actual_remision, $costo_actual, $nuevo_importe_remision, $id_historial);
        $stmt->execute();
        $stmt->close();

        $mensaje = 'Llanta actualizada correctamente, stock actualizado';

    }else{
        $query = "INSERT INTO historial_detalle_cambio
        (id, id_llanta, id_ubicacion, id_destino, cantidad, id_usuario, id_movimiento, stock_ubicacion_actual, stock_ubicacion_anterior, stock_destino_actual, stock_destino_anterior,
        aprobado_receptor, aprobado_emisor, usuario_emisor, usuario_receptor, costo, importe) VALUES (null,?,0,?,?,?,?,0,0,?,?,1,1,?,?,?,?)"; 
        $stmt = $con->prepare($query);
        $stmt->bind_param('sssssssssss',$id_llanta, $id_sucursal, $cantidad, $id_usuario, $id_movimiento, $stock_destino_actual,  $nuevo_stock_ubicacion_anterior, $usuario_emisor, $usuario_receptor, $costo_actual, $importe);
        $stmt->execute();
        $stmt->close();
        $mensaje = 'Llanta agregada correctamente, stock actualizado';

    }
   

    //Actualizar montos de los movimientos
    $sel = "SELECT total, pagado, restante FROM movimientos WHERE id = ?";
    $stmt = $con->prepare($sel);
    $stmt->bind_param('s',$id_movimiento);
    $stmt->execute();
    $stmt->bind_result($total_actual_mov, $pagado_actual_mov, $restante_actual_mov);
    $stmt->fetch();
    $stmt->close();

    $total_nuevo = $importe + $total_actual_mov;
    $restante_nuevo = $restante_actual_mov + $importe;
    
    $update= "UPDATE movimientos SET total =?, restante = ? WHERE id = ?";
    $stmt = $con->prepare($update);
    $stmt->bind_param('sss',$total_nuevo, $restante_nuevo, $id_movimiento);
    $stmt->execute();
    $stmt->close();
    $estatus = true;
    $response_ = array('estatus'=>$estatus, 'mensaje'=>$mensaje);

    echo json_encode($response_);

}

function actualizarInventario($id_sucursal, $id_llanta, $cantidad, $con){
    $stock_destino_anterior =0;
    $llantas_encontradas =0;
    $query = "SELECT COUNT(*) FROM inventario WHERE id_Llanta = ? AND id_sucursal = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('ss', $id_llanta, $id_sucursal);
    $stmt->execute();
    $stmt->bind_result($llantas_encontradas);
    $stmt->fetch();
    $stmt->close();
    $codigo_sucursal='';
    $sucursal ='';

    $query = "SELECT code, nombre FROM sucursal WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $id_sucursal);
    $stmt->execute();
    $stmt->bind_result($codigo_sucursal, $sucursal);
    $stmt->fetch();
    $stmt->close();

    if($llantas_encontradas > 0){
        $query = "SELECT Stock FROM inventario WHERE id_Llanta = ? AND id_sucursal = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('ss', $id_llanta, $id_sucursal);
        $stmt->execute();
        $stmt->bind_result($stock_destino_anterior);
        $stmt->fetch();
        $stmt->close();

        $updat = "UPDATE inventario SET Stock =? WHERE id_Llanta = ? AND id_sucursal = ?";
        $stmt = $con->prepare($updat);
        $stmt->bind_param('sss', $cantidad, $id_llanta, $id_sucursal);
        $stmt->execute();
        $stmt->close();

    }else{
        $stock_destino_anterior = 0;
        $query = "INSERT INTO inventario (id, id_Llanta, Codigo, Sucursal, id_sucursal, Stock) VALUES (null, ?,?,?,?,?)"; 
        $stmt = $con->prepare($query);
        $stmt->bind_param('sssss',$id_llanta, $sucursal, $codigo_sucursal, $id_sucursal, $cantidad);
        $stmt->execute();
        $error = $stmt->error;
        $stmt->close();
       
    }
    
  
    $r = array('stock_destino_anterior' => $stock_destino_anterior);
    return $r;
};

function actualizarPrecioLlanta($con, $precio_actual, $costo_actual, $mayoreo_actual, $id_llanta){
    $update = "UPDATE llantas SET precio_Inicial = ?, precio_Venta = ?, precio_Mayoreo = ? WHERE id = ?";
    $respp = $con->prepare($update);
    $respp->bind_param('ssss', $precio_actual, $costo_actual, $mayoreo_actual, $id_llanta);
    $respp->execute();
    $respp->close();
};

?>