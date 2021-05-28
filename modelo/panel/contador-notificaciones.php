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

    $id_user = $_SESSION["id_usuario"];

    $comprobar_notificaciones = $con->prepare("SELECT COUNT(*) total FROM registro_notificaciones WHERE id_usuario LIKE ? AND estatus LIKE 1");
    $comprobar_notificaciones->bind_param('i', $id_user);
    $comprobar_notificaciones->execute();
    $comprobar_notificaciones->bind_result($total);
    $comprobar_notificaciones->fetch();
    $comprobar_notificaciones->close();

    print_r($total);
      
            
                
        }

       

    




?>