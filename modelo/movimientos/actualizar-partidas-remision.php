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
$id_historial = $_POST['id_historial'];
$cantidad_historial = $_POST['cant_historial'];
$costo_historial = $_POST['costo_historial'];
$importe_historial = $_POST['importe_historial'];

$qr = "SELECT COUNT(*) FROM historial_detalle_cambio WHERE id =?";
$stmt = $con->prepare($qr);
$stmt->bind_param('s', $id_historial);
$stmt->execute();
$stmt->bind_result($total_reg);
$stmt->fetch();
$stmt->close();

if($total_reg>0){ 
    $stock_destino_anterior =0;
    $sel = "SELECT id_movimiento, stock_destino_anterior, importe FROM historial_detalle_cambio WHERE id = ?";
    $stmt = $con->prepare($sel);
    $stmt->bind_param('s',$id_historial);
    $stmt->execute();
    $stmt->bind_result($id_movimiento, $stock_destino_anterior, $importe_actual_historial);
    $stmt->fetch();
    $error[] = $stmt->error;
    $stmt->close();

    $qr = "SELECT total, pagado, restante FROM movimientos WHERE id =?";
    $stmt = $con->prepare($qr);
    $stmt->bind_param('i', $id_movimiento);
    $stmt->execute();
    $stmt->bind_result($total_act, $pagado_act, $restante_act);
    $stmt->fetch();
    $stmt->close();

    $nuevo_stock_destino_actual_remision = $stock_destino_anterior + $cantidad_historial;
    $update= "UPDATE historial_detalle_cambio SET cantidad =?, stock_destino_actual = ?, costo = ?, importe =? WHERE id =?";
    $stmt = $con->prepare($update);
    $stmt->bind_param('sssss', $cantidad_historial, $nuevo_stock_destino_actual_remision, $costo_historial, $importe_historial, $id_historial);
    $stmt->execute();
    $stmt->close();

    $sum = "SELECT SUM(importe) FROM historial_detalle_cambio WHERE id_movimiento = ?";
    $stmt = $con->prepare($sum);
    $stmt->bind_param('s', $id_movimiento);
    $stmt->execute();
    $stmt->bind_result($total_importe_sumatoria);
    $stmt->fetch();
    $stmt->close();
    //$nuevo_total = ($total_act - $importe_actual_historial) + $importe_historial;
    $nuevo_restante = $total_importe_sumatoria - $pagado_act;


    $update= "UPDATE movimientos SET total =?, restante =? WHERE id =?";
    $stmt = $con->prepare($update);
    $stmt->bind_param('sss',$total_importe_sumatoria, $nuevo_restante, $id_movimiento);
    $stmt->execute();
    $stmt->close();
    actualizarDescripcion($con, $id_movimiento);
    $array = array('estatus' =>true, 'mensaje' => 'Partida actualizada correctamente');

}else{
    $array = array('estatus' =>false, 'mensaje' => 'No se existe ese id en el historial');
}

echo json_encode($array);

function actualizarDescripcion($con, $id_movimiento){
    $descripcion_llanta='';
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

        $mercancia = $mercancia . " " . $descripcion_llanta . ", ";
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

    $nueva_descripcion = "Se realizo el ingreso de $cantidad_actual llanta(s) al sistema";
    $upd = "UPDATE movimientos SET mercancia = ?, descripcion = ? WHERE id = ?";
    $stmt = $con->prepare($upd);
    $stmt->bind_param('sss', $mercancia,  $nueva_descripcion, $id_movimiento);
    $stmt->execute();
    $stmt->close();

};
?>