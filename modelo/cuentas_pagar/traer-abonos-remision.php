<?php

include '../conexion.php';
$con= $conectando->conexion(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!$con) {
    echo "Problemas con la conexion";
}

$id_movimiento = $_POST['id_movimiento'];
$importe_total =0;
$pagado =0;
$restante =0;
$qr = "SELECT COUNT(*) FROM movimientos WHERE id =? AND tipo = 2";
$stmt = $con->prepare($qr);
$stmt->bind_param('i', $id_movimiento);
$stmt->execute();
$stmt->bind_result($total_movimientos);
$stmt->fetch();
$stmt->close();

if($total_movimientos>0){
    $query = "SELECT total, pagado, restante FROM movimientos WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i',$id_movimiento);
    $stmt->execute();
    $result = $stmt->bind_result($importe_total, $pagado, $restante);
    $stmt->fetch();
    $stmt->close();
    if($importe_total ==null){
        $importe_total =0;
        $mensaje_sin_importe = ', no se encontró un importe.';
    }else{
        $mensaje_sin_importe = '.';
    }

    $qr = "SELECT COUNT(*) FROM abonos_cuentas WHERE id_movimiento =?";
    $stmt = $con->prepare($qr);
    $stmt->bind_param('i', $id_movimiento);
    $stmt->execute();
    $stmt->bind_result($total_abonos);
    $stmt->fetch();
    $stmt->close();

    if($total_abonos > 0){
        $query = "SELECT * FROM abonos_cuentas WHERE id_movimiento = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('i',$id_movimiento);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $mensaje = 'Se encontraron movimientos con ese ID '.$mensaje_sin_importe;
    }else{
        $mensaje = 'Sin abonos por parte de la remision'.$mensaje_sin_importe;
        $data = [];
    }
    $estatus = true;

}else{
    $data = [];
    $mensaje = 'No se encontraron movimientos con ese ID o no son remisiones de ingreso';
    $estatus = false;
    
}

echo json_encode(array('estatus'=>$estatus, 'post'=>$_POST, 'data'=>$data, 'importe_total'=>$importe_total, 'pagado'=>$pagado, 'restante'=> $restante, 'mensaje'=>$mensaje));

?>