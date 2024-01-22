<?php

include '../conexion.php';
$con= $conectando->conexion(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!$con) {
    echo "Problemas con la conexion";
}
$id_historial = $_POST['id_historial'];
$qr = "SELECT COUNT(*) FROM historial_detalle_cambio WHERE id =?";
$stmt = $con->prepare($qr);
$stmt->bind_param('i', $id_historial);
$stmt->execute();
$stmt->bind_result($total_partidas);
$stmt->fetch();
$stmt->close();

if($total_partidas >0){
    $id_historial = $_POST['id_historial'];
    $qr = "SELECT * FROM historial_detalle_cambio WHERE id =?";
    $stmt = $con->prepare($qr);
    $stmt->bind_param('i', $id_historial);
    $stmt->execute();
    $result_hdc = $stmt->get_result();
    $stmt->free_result();
    $stmt->close();
    while($fila = $result_hdc->fetch_assoc()){
        $id_llanta = $fila['id_llanta'];
        $id_destino = $fila['id_destino'];
        $data_llanta = traer_llanta($id_llanta, $con, $id_destino);
        $marca = $data_llanta[1];
        $descripcion = $data_llanta[0];
        $stock = $data_llanta[2];
        $fila['marca'] = $marca;
        $fila['stock'] = $stock;
        $fila['descripcion'] = $descripcion;
        $data_partidas[]= $fila;
    };


    $response = array('estatus'=>true, 'mensaje' => 'Se encontró una partida con ese movimiento', 'data'=> $data_partidas);
}else{
    $response = array('estatus'=>false, 'mensaje' => 'No se encontró una partida con ese movimiento');
}

echo json_encode($response);


function traer_llanta($id_llanta, $con, $id_destino){
    $descripcion = '';
    $marca ='';
    $stock_actual=0;
    $traer_stock = "SELECT Stock FROM inventario WHERE id_sucursal = ? AND id_Llanta = ?";
    $stmt= $con->prepare($traer_stock);
    $stmt->bind_param('ss',$id_destino, $id_llanta);
    $stmt->execute();
    $stmt->bind_result($stock_actual);
    $stmt->fetch();
    $stmt->close();
    if(empty($stock_actual)){
        $stock_actual =0;
    }
    $qr = "SELECT descripcion, marca FROM llantas WHERE id =?";
    $stmt = $con->prepare($qr);
    $stmt->bind_param('i', $id_llanta);
    $stmt->execute();
    $stmt->bind_result($descripcion, $marca);
    $stmt->fetch();
    $stmt->close();

    return array($descripcion, $marca, $stock_actual);
}