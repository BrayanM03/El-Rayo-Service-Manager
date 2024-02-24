<?php

// Función para extraer las medidas de la descripción de la llanta

include '../../conexion.php';
$con= $conectando->conexion(); 
$ancho = $_POST['ancho']; 
$perfil = $_POST['perfil']; 
$rin=$_POST['rin']; /* 
$id_marca = $_POST['id_marca'];  */
$medida = $_POST['medida']; 
$construccion = $_POST['construccion']; 
$estatus_medida = $_POST['estatus']; 
$stock_minimo = $_POST['stock_minimo']; 
$stock_maximo = empty($_POST['stock_maximo']) ? 0 : $_POST['stock_maximo']; 
$id_sucursal = $_POST['id_sucursal'];
$fecha_actual = date("Y-m-d H:i:s");

$validar = "SELECT COUNT(*) FROM medidas_stock WHERE ancho =? AND perfil =? AND rin = ? AND id_sucursal = ? AND construccion = ?";
$stmt = $con->prepare($validar);
$stmt->bind_param('sssss', $ancho, $perfil, $rin, $id_sucursal, $construccion);
$stmt->execute();
$stmt->bind_result($total_medidas);
$stmt->fetch();
$stmt->close();

if($total_medidas > 0){
    $mensaje = 'La medida ingresada ya existe';
    $estatus = false;
}else{
    $insert = "INSERT INTO medidas_stock (id, descripcion, ancho, perfil, rin, construccion, id_sucursal, stock_minimo, stock_maximo, estatus, created_at) VALUES (null, ?,?,?,?,?,?,?,?,?,?)";
    $stmt = $con->prepare($insert);
    $stmt->bind_param('ssssssssss', $medida, $ancho, $perfil, $rin, $construccion, $id_sucursal, $stock_minimo, $stock_maximo, $estatus_medida, $fecha_actual);
    $stmt->execute();
    $stmt->close();
    $mensaje = 'Medida registrada correctamente';
    $estatus = true;
}
$resp = array('estatus' => $estatus, 'mensaje' => $mensaje);
echo json_encode($resp);
?>