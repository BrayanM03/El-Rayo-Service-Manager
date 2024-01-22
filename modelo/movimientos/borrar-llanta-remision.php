
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
$error=[];
$id_historial = $_POST['id_historial'];
$permiso = $_POST['permiso_borrar'];

$qr = "SELECT COUNT(*) FROM historial_detalle_cambio WHERE id =?";
$stmt = $con->prepare($qr);
$stmt->bind_param('s', $id_historial);
$stmt->execute();
$stmt->bind_result($total_reg);
$stmt->fetch();
$stmt->close();

if($total_reg>0){
    $mensaje ='';
    $sel = "SELECT id_llanta, cantidad, id_movimiento, id_destino, importe FROM historial_detalle_cambio WHERE id = ?";
    $stmt = $con->prepare($sel);
    $stmt->bind_param('s',$id_historial);
    $stmt->execute();
    $stmt->bind_result($id_llanta, $cantidad, $id_movimiento, $id_destino, $importe_actual_historial);
    $stmt->fetch();
    $error[] = $stmt->error;
    $stmt->close();

    //Actualizando inventario 
    if($permiso ==1){
        $query = "SELECT Stock FROM inventario WHERE id_Llanta = ? AND id_sucursal = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('ss', $id_llanta, $id_destino);
        $stmt->execute();
        $stmt->bind_result($stock_destino_actual);
        $stmt->fetch();
        $stmt->close();

        $nuevo_stock = $stock_destino_actual - $cantidad;
        if($nuevo_stock >= 0){
            $updat = "UPDATE inventario SET Stock =? WHERE id_Llanta = ? AND id_sucursal = ?";
            $stmt = $con->prepare($updat);
            $stmt->bind_param('sss', $nuevo_stock, $id_llanta, $id_destino);
            $stmt->execute();
            $stmt->close();
            $mensaje .= 'El inventario se actualizó con exito. Nuevo stock: ' . $nuevo_stock . '. ';
        }else{
            $mensaje .= 'El inventario quedará con un stock menor a 0, (Stock resultante: '. $nuevo_stock.'),  no se actualizó el inventario, contacte al administrador. ';
        }
    }

    $quer = "DELETE FROM historial_detalle_cambio WHERE id = ?";
    $stmt = $con->prepare($quer);
    $stmt->bind_param('s', $id_historial);
    $stmt->execute();
    $error[] = $stmt->error;
    $stmt->close();

    //Actualizar montos de los movimientos
    $sel = "SELECT total, pagado, restante FROM movimientos WHERE id = ?";
    $stmt = $con->prepare($sel);
    $stmt->bind_param('s',$id_movimiento);
    $stmt->execute();
    $stmt->bind_result($total_actual_mov, $pagado_actual_mov, $restante_actual_mov);
    $stmt->fetch();
    $error[] = $stmt->error;
    $stmt->close();

    $total_nuevo = $total_actual_mov - $importe_actual_historial;
    $restante_nuevo = $restante_actual_mov - $importe_actual_historial;
    if($total_nuevo < 0){
        $total_nuevo =0;
    }
    if($restante_nuevo < 0){
        $restante_nuevo =0;
    }
    $update= "UPDATE movimientos SET total =?, restante = ?";
    $stmt = $con->prepare($update);
    $stmt->bind_param('ss',$total_nuevo, $restante_nuevo);
    $stmt->execute();
    $error[] = $stmt->error;
    $stmt->close();

    $estatus = true;
    $mensaje .= "Eliminado con exito";
}else{
    $estatus = false;
    $mensaje = "No existe es ID en el registro";

}
actualizarDescripcion($con, $id_movimiento);
$response = array('estatus' => $estatus, 'mensaje' => $mensaje, 'error' => $error);
echo json_encode($response);

function actualizarDescripcion($con, $id_movimiento){
    $descripcion_llanta='';
    $descripcion_mov ='';
    $mercancia_mov='';
    $cantidad_actual ='';
    $nombre_sucursal ='';
    $id_sucursal = 0;
    $mercancia ='';

    $traer_cambios= mysqli_query($con, "SELECT * FROM historial_detalle_cambio WHERE id_movimiento = $id_movimiento");
    while ($rows = $traer_cambios->fetch_assoc()) {
        $id_pieza = $rows['id_llanta'];
        $sel = "SELECT Descripcion FROM llantas WHERE id = ?";
        $stmt = $con->prepare($sel);
        $stmt->bind_param('i', $id_pieza);
        $stmt->execute();
        $stmt->bind_result($descripcion_llanta);
        $stmt->fetch();
        $stmt->close();

        $mercancia = $mercancia . ", " . $descripcion_llanta . ", ";
    }

    $sel = "SELECT nombre FROM sucursal WHERE id = ?";
    $stmt = $con->prepare($sel);
    $stmt->bind_param('i', $id_sucursal);
    $stmt->execute();
    $stmt->bind_result($nombre_sucursal);
    $stmt->fetch();
    $stmt->close();

    $sel = "SELECT SUM(cantidad) FROM historial_detalle_cambio WHERE id_movimiento = ?";
    $stmt = $con->prepare($sel);
    $stmt->bind_param('i', $id_movimiento);
    $stmt->execute();
    $stmt->bind_result($cantidad_actual);
    $stmt->fetch();
    $stmt->close();

    $nueva_descripcion = "Se realizo el ingreso de $cantidad_actual llanta(s) al inventario del sistema";
    $upd = "UPDATE movimientos SET mercancia = ?, descripcion = ? WHERE id = ?";
    $stmt = $con->prepare($upd);
    $stmt->bind_param('sss', $mercancia,  $nueva_descripcion, $id_movimiento);
    $stmt->execute();
    $stmt->close();

};