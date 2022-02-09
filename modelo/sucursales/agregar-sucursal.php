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

$nombre_sucursal = $_POST['nombre_sucursal'];
$telefono = $_POST['telefono'];
$direccion = $_POST['direccion'];

$add = "INSERT INTO sucursal(id, nombre, Direccion, Telefono) VALUES (null,?,?,?)";
$res =$con->prepare($add);
$res->bind_param('sss', $nombre_sucursal, $direccion, $telefono);
$res->execute();
$res->close();

print_r(1);

?>