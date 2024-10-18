<?php
session_start();
include '../conexion.php';
include '../helpers/response_helper.php';
$con = $conectando->conexion(); 
$id_garantia = $_POST['id_garantia'];

$count = "SELECT COUNT(*) FROM garantias WHERE id = ?";
$res = $con->prepare($count);
$res->bind_param('s', $id_garantia);
$res->execute();
$res->bind_result($total_garantias);
$res->fetch();
$res->close();

if($total_garantias>0){
    $sel = "SELECT * FROM garantias_imagenes WHERE id_garantia = ?";
    $res = $con->prepare($sel);
    $res->bind_param('s', $id_garantia);
    $res->execute();
    $resultado_imagenes = $res->get_result();  
    $res->free_result();
    $res->close();
    while($fila_ = $resultado_imagenes->fetch_assoc()){
        $data_img[] = $fila_;
    }
    responder(true, 'Se encontraron imagenes de la garantía', 'success', $data_img, true);

}else{
    responder(false, 'No se encontró la garantía', 'danger', null, true);

}

