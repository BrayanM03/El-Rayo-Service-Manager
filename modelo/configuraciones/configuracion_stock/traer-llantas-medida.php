<?php

// Función para extraer las medidas de la descripción de la llanta

include '../../conexion.php';
$con= $conectando->conexion(); 
$id_medida = $_POST['id_medida']; 
$id_sucursal = $_POST['id_sucursal']; 

$validar = "SELECT COUNT(*) FROM medidas_stock WHERE id = ?";
$stmt = $con->prepare($validar);
$stmt->bind_param('s', $id_medida);
$stmt->execute();
$stmt->bind_result($total_medidas);
$stmt->fetch();
$stmt->close();
if($total_medidas>0){
    $sel = "SELECT * FROM medidas_stock WHERE id = ?";
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

    $ancho = floatval($data['ancho']);
    $perfil = floatval($data['perfil']);
    $rin = floatval($data['rin']);
    $select = "SELECT l.*, i.stock_minimo, i.stock_maximo, i.Stock, i.id as id_inventario, i.medida_stock_estatus FROM llantas l INNER JOIN inventario i ON l.id = i.id_LLanta
     WHERE l.Ancho = ? AND l.Proporcion = ? AND l.Diametro = ? AND i.id_Sucursal =?";
    $stmt = $con->prepare($select);
    $stmt->bind_param('ddds', $ancho, $perfil, $rin, $id_sucursal);
    $stmt->execute();
    $data_ = $stmt->get_result();
    $stmt->free_result();
    $stmt->close();
    $medidas = array();
    foreach($data_ as $fila){
        $medidas[] = $fila;
    }
}else{
    $estatus = false;
    $mensaje = 'No se encontró una medida con ese ID';
    $data =[];
}

echo json_encode(array('estatus'=>$estatus, 'mensaje'=>$mensaje, 'data'=>$medidas, 'da'=>$data));

