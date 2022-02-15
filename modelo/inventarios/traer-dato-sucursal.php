<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if($_POST){
    $id_sucursal = $_POST['id_suc'];
    $sql = "SELECT nombre FROM sucursal WHERE id= ?";
    $res = $con->prepare($sql);
    $res->bind_param('s', $id_sucursal);
    $res->execute();
    $res->bind_result($nombre);
    $res->fetch();
    $res->close();

    print_r($nombre);

}

?>