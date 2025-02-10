<?php


session_start();
include '../conexion.php';
include '../helpers/response_helper.php';

$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}
$id_usuario = $_SESSION['id_usuario'];
$codigo = $_POST['codigo'];
$nuevo_precio_unitario = $_POST['nuevo_precio_unitario'];
$count = "SELECT count(*) FROM productos_preventa WHERE id_usuario = ? AND codigo = ?";
$stmt = $con->prepare($count);
$stmt->bind_param('ss', $id_usuario, $codigo);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();

if($total>0){
    $sel = 'SELECT cantidad FROM productos_preventa WHERE id_usuario = ? AND codigo = ?';
    $stmt= $con->prepare($sel);
    $stmt->bind_param('ss', $id_usuario, $codigo);
    $stmt->execute();
    $resultado_ = $stmt->get_result();

    while ($registro = $resultado_->fetch_assoc()) {
        $cantidad = $registro['cantidad'];
    }
    $nuevo_importe = $cantidad * $nuevo_precio_unitario;
    $stmt->close();

    $update = "UPDATE productos_preventa SET precio =?, importe = ? WHERE id_usuario = ? AND codigo = ?";
    $stmt = $con->prepare($update);
    $stmt->bind_param('ddss', $nuevo_precio_unitario, $nuevo_importe, $id_usuario, $codigo);
    $stmt->execute();
    $stmt->close();
    responder(true,  "Precio actualizado con exito", 'success', [], true);   

}else{
    responder(false,  "No se encontraron productos con ese codigo", 'success', $datos, true);   
}

