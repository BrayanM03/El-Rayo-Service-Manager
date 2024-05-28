<?php

// Función para extraer las medidas de la descripción de la llanta

include '../../conexion.php';
$con= $conectando->conexion(); 
$id_inventario = $_POST['id_inventario']; 
$stock_minimo = $_POST['stock_minimo']; 
$stock_maximo = $_POST['stock_maximo'];
$estatus = $_POST['estatus'];

$query = "SELECT count(*) FROM inventario WHERE id =?";
$stmt = $con->prepare($query);
$stmt->bind_param('s', $id_inventario);
$stmt->execute();
$stmt->bind_result($total_llantas);
$stmt->fetch();
$stmt->close();

if($total_llantas >0){
    $query = "UPDATE inventario SET stock_minimo =?, stock_maximo =?, medida_stock_estatus = ? WHERE id =?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('ssss', $stock_minimo, $stock_maximo, $estatus, $id_inventario);
    $stmt->execute();
    $stmt->close();
    $estatus = true;
    $mensaje = 'Actualizado correctamente';

}else{
    $estatus = false;
    $mensaje = 'No se pudo actualizar el stock minimo o maximo de la medida de la llanta';

}

$response = array('estatus'=>$estatus,  'mensaje'=>$mensaje, 'post'=> $_POST);
echo json_encode($response);