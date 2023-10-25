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
$comentario = $_POST['comentario'];
$id_dc = $_POST['id_dc'];
$id_sesion = $_SESSION['id_usuario'];
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
    $col_comentario = 'comentario_emisor';
    $col_usuario = 'usuario_emisor';
}else{
    $col = 'aprobado_receptor';
    $col_comentario = 'comentario_receptor';
    $col_usuario = 'usuario_receptor';

}
$query = "UPDATE historial_detalle_cambio SET $col = ?, $col_comentario = ?, $col_usuario =? WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('ssss', $accion, $comentario, $id_sesion, $id_dc);
$stmt->execute();
$stmt->close();
$mensaje = 'Estatus de mercancia actualizado';
//Comprobacion 
$query = "SELECT COUNT(*) FROM historial_detalle_cambio WHERE id_movimiento = ? AND (aprobado_emisor != 1 OR aprobado_receptor != 1)";
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