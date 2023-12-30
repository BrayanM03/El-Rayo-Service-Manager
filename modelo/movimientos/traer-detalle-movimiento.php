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
    $stmt->fetch();
    $stmt->close();
    while($fila = $result_hdc->fetch_assoc()){
        $id_llanta = $fila['id_llanta'];
        $data_llanta = traer_llanta($id_llanta, $con);
        $marca = $data_llanta[1];
        $descripcion = $data_llanta[0];
        $fila['marca'] = $marca;
        $fila['descripcion'] = $descripcion;
        $data_partidas[]= $fila;
    };


    $response = array('estatus'=>true, 'mensaje' => 'No se encontró una partida con ese movimiento', 'data'=> $data_partidas);
}else{
    $response = array('estatus'=>false, 'mensaje' => 'No se encontró una partida con ese movimiento');
}

echo json_encode($response);


function traer_llanta($id_llanta, $con){
    $descripcion = '';
    $marca ='';
    $qr = "SELECT descripcion, marca FROM llantas WHERE id =?";
    $stmt = $con->prepare($qr);
    $stmt->bind_param('i', $id_llanta);
    $stmt->execute();
    $stmt->bind_result($descripcion, $marca);
    $stmt->fetch();
    $stmt->close();

    return array($descripcion, $marca);
}