<?php
session_start();
include '../conexion.php';
$con = $conectando->conexion(); 

$id_pedido = $_POST['id_pedido'];
$desconfirmacion = $_POST['desconfirmar'] == 'true' ? 0 : 1;
$pedido = "SELECT COUNT(*) FROM pedidos WHERE id = ?";
$res = $con->prepare($pedido);
$res->bind_param('s', $id_pedido);
$res->execute();
$res->bind_result($total_pedidos);
$res->fetch();
$res->close();

if($total_pedidos > 0){
    $updt = "UPDATE pedidos SET orden_confirmada = ? WHERE id = ?";
    $res = $con->prepare($updt);
    $res->bind_param('ss', $desconfirmacion, $id_pedido);
    $res->execute();
    $res->close();
    
    $response = array('estatus' =>true, 'mensaje' =>'Pedido actualizado');
}else{
    $response = array('estatus' =>true, 'mensaje' =>'No existe el pedido con ese folio');

}

echo json_encode($response);
?>