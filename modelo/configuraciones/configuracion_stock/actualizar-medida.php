<?php

// Función para extraer las medidas de la descripción de la llanta

include '../../conexion.php';
$con= $conectando->conexion(); 
$id_medida = $_POST['id_medida']; 

$ancho = $_POST['ancho']; 
$perfil = $_POST['perfil']; 
$rin=$_POST['rin'];
$id_medida = $_POST['id_medida'];
$medida = $_POST['medida']; 
$estatus = $_POST['estatus'];
$construccion = $_POST['construccion']; 
$estatus_medida = $_POST['estatus']; 
$stock_minimo = $_POST['stock_minimo']; 
$stock_maximo = empty($_POST['stock_maximo']) ? 0 : $_POST['stock_maximo']; 
$id_sucursal = $_POST['id_sucursal'];

$validar = "SELECT COUNT(*) FROM medidas_stock WHERE id = ?";
$stmt = $con->prepare($validar);
$stmt->bind_param('s', $id_medida);
$stmt->execute();
$stmt->bind_result($total_medidas);
$stmt->fetch();
$stmt->close();
if($total_medidas>0){
    $upd = "UPDATE medidas_stock SET descripcion =?, ancho =?, perfil =?, rin=?, construccion=?, id_sucursal=?, stock_minimo=?, stock_maximo =?, estatus = ? WHERE id = ?";
    $stmt = $con->prepare($upd);
    $stmt->bind_param('ssssssssss', $medida, $ancho, $perfil, $rin, $construccion, $id_sucursal, $stock_minimo, $stock_maximo, $estatus, $id_medida);
    $stmt->execute();
    $stmt->close();
    $estatus = true;
    $mensaje = 'Medida actualizada correctamente';
}else{
    $estatus = false;
    $mensaje = 'No se encontró una medida con ese ID';
}

echo json_encode(array('estatus'=>$estatus, 'mensaje'=>$mensaje));

