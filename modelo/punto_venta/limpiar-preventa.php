<?php


session_start();
include '../conexion.php';
include '../helpers/response_helper.php';

$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$id_usuario = $_SESSION["id_usuario"];
$sql = "DELETE FROM productos_preventa WHERE id_usuario = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('s', $id_usuario);
$stmt->execute();
$stmt->close();

responder(true, 'Carrito limpiado con exito', 'success', [], true, true);



?>