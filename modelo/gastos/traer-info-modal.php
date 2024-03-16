<?php
session_start();
include '../conexion.php';
$con = $conectando->conexion(); 
$id_sucursal_sesion = $_SESSION['id_sucursal'];
$id_rol_sesion = intval($_SESSION['rol']);
$ID = $con->prepare("SELECT COUNT(*) FROM categorias_gastos");
$ID->execute();
$ID->bind_result($total_categorias);
$ID->fetch();
$ID->close();

$ID = $con->prepare("SELECT COUNT(*) FROM sucursal");
$ID->execute();
$ID->bind_result($total_sucursales);
$ID->fetch();
$ID->close();



$data = array();
$data['id_sesion_sucursal'] = $id_sucursal_sesion;
$data['id_sesion_rol'] = $id_rol_sesion;
if($total_categorias > 0){
    $ID = $con->prepare("SELECT * FROM categorias_gastos ORDER BY nombre ASC");
    $ID->execute();
    $resultado_cate = $ID->get_result();    
    $ID->free_result();
    $ID->close();
    while($fila_ab = $resultado_cate->fetch_assoc()){
        $data['categorias'][] = $fila_ab;
    }
}else{
    $data['categorias'][] = [];
}

if($total_sucursales > 0){
    $ID = $con->prepare("SELECT * FROM sucursal");
    $ID->execute();
    $resultado_cate = $ID->get_result();    
    $ID->free_result();
    $ID->close();
    while($fila_ab = $resultado_cate->fetch_assoc()){
        $data['sucursales'][] = $fila_ab;
    }
}else{
    $data['sucursales'][] = [];
}

$response = array('estatus'=>true, 'data'=>$data);


echo json_encode($response);


