<?php
session_start();
include '../conexion.php';
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
    $sel = "SELECT * FROM vista_garantias WHERE id = ?";
    $res = $con->prepare($sel);
    $res->bind_param('s', $id_garantia);
    $res->execute();
    $error = $con->error;
    $resultado_garantias = $res->get_result();  
    $res->free_result();
    $res->close();
    $data = array();
    while($fila = $resultado_garantias->fetch_assoc()){
        $data = $fila;
    }

    $sel = "SELECT * FROM proveedores";
    $res = $con->prepare($sel);
    $res->execute();
    $resultado_proveedores = $res->get_result();  
    $res->free_result();
    $res->close();
    while($fila_ = $resultado_proveedores->fetch_assoc()){
        $data_provs[] = $fila_;
    }

    $res = array('estatus'=> true, 'mensaje'=> 'Se encontraron datos', 'data'=>$data, 'error'=> $error, 'proveedores'=> $data_provs);

}else{
    $res = array('estatus'=> false, 'mensaje'=> 'El folio no coincide con el gasto');
}

echo json_encode($res);

