<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}
 
if (!$con) {
    echo "maaaaal";
}
$fecha = date('Y-m-d');
$hora = date('h:i a');
$tipo = $_POST['tipo'];
$accion = $_POST['accion'];
$id_dc = $_POST['id_dc'];
$id_movimiento = $_POST['id_movimiento'];
$query = "SELECT COUNT(*) FROM historial_detalle_cambio WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('s', $id_dc);
$stmt->execute();
$stmt->bind_result($total_mer);
$stmt->fetch();
$stmt->close();

if($total_mer>0){
if($tipo==1){
    $col = 'aprobado_emisor';
}else{
    $col = 'aprobado_receptor';
}
$query = "UPDATE historial_detalle_cambio SET $col = ? WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('ss', $accion, $id_dc);
$stmt->execute();
$stmt->close();
$mensaje = 'Estatus de mercancia actualizado';
//Comprobacion 
$query = "SELECT COUNT(*) FROM historial_detalle_cambio WHERE id_movimiento = ? AND ((aprobado_emisor = 0 OR aprobado_receptor = 0) OR (aprobado_emisor = 2 OR aprobado_receptor = 2))";
$stmt = $con->prepare($query);
$stmt->bind_param('s', $id_movimiento);
$stmt->execute();
$stmt->bind_result($historial_aprobado);
$stmt->fetch();
$stmt->close();

if($historial_aprobado ==0){
    $query = "UPDATE movimientos SET estatus = 'Completado' WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $id_movimiento);
    $stmt->execute();
    $stmt->close();
    $mensaje = 'Estatus de mercancia actualizado y movimiento actualizado a completado';
}

$response = array('estatus' =>true, 'mensaje' => $mensaje, 'numero_no_aprobado'=> $historial_aprobado);

}else{
    $response = array('estatus' =>false, 'mensaje' =>'No se encontro el registro de esta llanta en el detalle de cambios');
}

echo json_encode($response);

?>