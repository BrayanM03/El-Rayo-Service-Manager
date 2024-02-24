<?php

// Función para extraer las medidas de la descripción de la llanta

include '../../conexion.php';
$con= $conectando->conexion(); 
$id_medida = $_POST['id_medida']; 

$validar = "SELECT COUNT(*) FROM medidas_stock WHERE id = ?";
$stmt = $con->prepare($validar);
$stmt->bind_param('s', $id_medida);
$stmt->execute();
$stmt->bind_result($total_medidas);
$stmt->fetch();
$stmt->close();
if($total_medidas>0){
    $del = "DELETE FROM medidas_stock WHERE id = ?";
    $stmt = $con->prepare($del);
    $stmt->bind_param('s', $id_medida);
    $stmt->execute();
    $stmt->close();
    $estatus = true;
    $mensaje = 'Medida borrada correctamente';
}else{
    $estatus = false;
    $mensaje = 'No se encontró una medida con ese ID';
}

echo json_encode(array('estatus'=>$estatus, 'mensaje'=>$mensaje));

?>