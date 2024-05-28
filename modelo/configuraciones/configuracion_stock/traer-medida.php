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
    $sel = "SELECT m.*, s.nombre as nombre_sucursal FROM medidas_stock m INNER JOIN sucursal s ON m.id_sucursal = s.id WHERE m.id = ?";
    $stmt = $con->prepare($sel);
    $stmt->bind_param('s', $id_medida);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->free_result();
    $stmt->close();
    $estatus = true;
    $mensaje = 'Medida encontrada correctamente';
    foreach($res as $row){
        $data = $row;
    }
}else{
    $estatus = false;
    $mensaje = 'No se encontró una medida con ese ID';
    $data =[];
}

echo json_encode(array('estatus'=>$estatus, 'mensaje'=>$mensaje, 'data'=>$data));

