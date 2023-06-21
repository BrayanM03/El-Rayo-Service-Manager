<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

date_default_timezone_set("America/Matamoros");

if (!$con) {
    echo "Problemas con la conexion";
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../login.php");
}

$comision = $_POST['comision'];
$id_usuario = $_POST['id'];

$consultar_usuario = "SELECT COUNT(*) FROM usuarios WHERE id = ?";
$resultado = $con->prepare($consultar_usuario);
$resultado->bind_param('i', $id_usuario);
$resultado->execute();
$resultado->bind_result($total);
$resultado->fetch();
$resultado->close();

if($total > 0) {
    $query = "UPDATE usuarios SET comision = ? WHERE id = ?";
    $resultado = $con->prepare($query);
    $resultado->bind_param('di', $comision, $id_usuario);
    $resultado->execute();
    $resultado->close();

    $data = array('mensaje' => 'Comision actualizada correctamente', 'estatus' => true);
}else{
    $data = array('mensaje' => 'No se encontro el usuario', 'estatus' => false);
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);

?>