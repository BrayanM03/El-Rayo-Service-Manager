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
$id_movimiento = $_POST['id_mov'];
$query = "SELECT COUNT(*) FROM movimientos WHERE id = ? AND estatus != 'Completado'";
$id_sesion = $_SESSION['id_usuario'];
$stmt = $con->prepare($query);
$stmt->bind_param('s', $id_movimiento);
$stmt->execute();
$stmt->bind_result($tot);
$stmt->fetch();
$stmt->close();

if($tot){
    $query = "UPDATE movimientos SET estatus = 'Completado' WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $id_movimiento);
    $stmt->execute();
    $stmt->close();

    $query = "UPDATE historial_detalle_cambio SET aprobado_emisor = 1, aprobado_receptor = 1 WHERE id_movimiento = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $id_movimiento);
    $stmt->execute();
    $stmt->close();

    $query = "SELECT COUNT(*) FROM requerimientos WHERE id_movimiento = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $id_movimiento);
    $stmt->execute();
    $stmt->bind_result($total_requerimientos);
    $stmt->fetch();
    $stmt->close();

    if($total_requerimientos > 0){
        $query = "SELECT id FROM requerimientos WHERE id_movimiento = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('s', $id_movimiento);
        $stmt->execute();
        $stmt->bind_result($id_requerimiento);
        $stmt->fetch();
        $stmt->close();
        $query = "UPDATE requerimientos SET estatus = 4 WHERE id_movimiento = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('s', $id_movimiento);
        $stmt->execute();
        $stmt->close();
        $query = "UPDATE detalle_requerimientos SET estatus = 6 WHERE id_requerimiento = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('s', $id_requerimiento);
        $stmt->execute();
        $stmt->close();
    }
    

    $mensaje = 'Estatus de mercancia actualizado y movimiento actualizado a completado';
    $response = array('estatus' =>true, 'mensaje' => $mensaje);
}else{
    $mensaje = 'No se pudo actualizar, el movimiento ya esta actualizado o no fue encontrado';
    $response = array('estatus' =>false, 'mensaje' => $mensaje);

}

echo json_encode($response);

