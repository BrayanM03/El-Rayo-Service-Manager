<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$iduser = $_SESSION["id_usuario"];

if($_POST){
    
    $consultar_importe = "SELECT SUM(importe) total FROM cotizacion_temp$iduser";
    $stmt = $con->prepare($consultar_importe);
    $stmt->execute();
    $stmt->bind_result($importe_total);
    $stmt->fetch();
    $stmt->close();

    print_r($importe_total);
}