<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if (!$con) {
    echo "maaaaal";
}


if(isset($_POST)){

    $id_usuario = $_POST["id_usuario"];
    

    $borrar_credito= $con->prepare("DELETE FROM detalle_cambio WHERE id_usuario = ?");
    $borrar_credito->bind_param('i', $id_usuario);
    $borrar_credito->execute();
    $borrar_credito->close();

    print_r($id_usuario);

}

?>