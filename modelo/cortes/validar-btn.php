<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$sucursal = $_POST["sucursal"];

$comprobar = $con->prepare("SELECT corte FROM `sucursal` WHERE id =?");
               $comprobar->bind_param('s', $sucursal);
               $comprobar->execute();
               $comprobar->bind_result($valor_cort);
               $comprobar->fetch();
               $comprobar->close();

               
               echo $valor_cort;
               
            

?>