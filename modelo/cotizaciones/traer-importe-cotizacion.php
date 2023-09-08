<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$iduser = $_SESSION["id_usuario"];
$tipo_cotizacion  = $_POST["tipo_cotizacion"];
if($_POST){

    $consultar_importe = "SELECT SUM(importe) total FROM detalle_nueva_cotizacion WHERE id_usuario = ? AND tipo = ?";
    $stmt = $con->prepare($consultar_importe);
    $stmt->bind_param('ii', $iduser, $tipo_cotizacion);
    $stmt->execute();
    $stmt->bind_result($importe_total);
    $stmt->fetch();
    $stmt->close();

    print_r($importe_total);
}