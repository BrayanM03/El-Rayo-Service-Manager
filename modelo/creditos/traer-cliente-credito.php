<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if(isset($_POST)){

    $id = $_POST["id"];



$traercliente = $con->prepare("SELECT Nombre_Cliente FROM clientes WHERE id LIKE ?");
$traercliente->bind_param('s', $id);
$traercliente->execute();
$traercliente->bind_result($nombre);
$traercliente->fetch();
$traercliente->close();

print_r($nombre);

}

?>