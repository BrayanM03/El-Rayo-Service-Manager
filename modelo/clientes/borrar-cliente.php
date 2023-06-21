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
require_once('../movimientos/mover_clientes.php');

if(isset($_POST)){

    $id = $_POST["id"];
    $cliente_nombre = traerCliente($id, $con);
    $cambios = "Se eliminó cliente: $cliente_nombre";
    InsertarMovimiento("eliminación", $cambios, $con);

    $borrar_credito= $con->prepare("DELETE FROM clientes WHERE id = ?");
    $borrar_credito->bind_param('i', $id);
    $borrar_credito->execute();
    $borrar_credito->close();
    
   print_r(1); 

}

?>