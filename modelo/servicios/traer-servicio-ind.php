<?php

session_start();
include '../conexion.php';
$con = $conectando->conexion();

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$id_servicio = $_POST['id_servicio'];


$queryChange = "SELECT COUNT(*) FROM servicios WHERE id=?";
$resps = $con->prepare($queryChange);
$resps->bind_param('s', $id_servicio);
$resps->execute();
$resps->bind_result($total_servicios);
$resps->fetch();
$resps->close();

if ($total_servicios > 0) {

    $queryChange = "SELECT * FROM servicios WHERE id=?";
    $resps = $con->prepare($queryChange);
    $resps->bind_param('s', $id_servicio);
    $resps->execute();
    $resps->bind_result($id, $codigo, $descripcion, $precio, $estatus, $img);
    $resps->fetch();
    $resps->close();

        $data = array("id"=>$id, "codigo"=> $codigo, 
                        "descripcion"=> $descripcion, 
                        "precio"=> $precio,
                        "estatus"=> $estatus, 
                        "img"=> $img);


   echo json_encode($data, JSON_UNESCAPED_UNICODE);
}else{
    $data = array("id"=>false, "mensaje"=> "Sin datos");
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}


